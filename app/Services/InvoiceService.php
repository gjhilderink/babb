<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Member;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function create(array $data): Invoice
    {
        // Zonder opgegeven referentie (bijv. bij bulkfacturatie) die van het lid overnemen
        if (! array_key_exists('referentie', $data)) {
            $data['referentie'] = Member::find($data['member_id'])?->factuur_referentie;
        }

        // Tijdelijke korting van het lid als extra (negatieve) factuurregel toevoegen
        if (! empty($data['apply_discount'])) {
            $member = Member::find($data['member_id']);
            if ($member) {
                $data['items'] = array_merge(
                    $data['items'],
                    $member->discountItemsFor($data['items'], $data['issue_date'])
                );
            }
        }

        // Bij een gelijktijdig uitgegeven nummer (unieke index) opnieuw proberen
        for ($attempt = 1; ; $attempt++) {
            try {
                return DB::transaction(function () use ($data) {
                    $invoice = Invoice::create([
                        'invoice_number' => $this->nextInvoiceNumber(),
                        'member_id'      => $data['member_id'],
                        'referentie'     => $data['referentie'] ?: null,
                        'issue_date'     => $data['issue_date'],
                        'due_date'       => $data['due_date'],
                        'notes'          => $data['notes'] ?? null,
                        'status'         => 'draft',
                    ]);

                    $this->syncItems($invoice, $data['items']);
                    $invoice->recalculateTotals();

                    return $invoice;
                });
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt >= 3) {
                    throw $e;
                }
            }
        }
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update([
            'member_id'  => $data['member_id'],
            'referentie' => $data['referentie'] ?? null,
            'issue_date' => $data['issue_date'],
            'due_date'   => $data['due_date'],
            'notes'      => $data['notes'] ?? null,
        ]);

        $invoice->items()->delete();
        $this->syncItems($invoice, $data['items']);
        $invoice->recalculateTotals();

        return $invoice->fresh();
    }

    public function generatePdf(Invoice $invoice): Response
    {
        return $this->pdf($invoice)->download($this->pdfBestandsnaam($invoice));
    }

    public function pdfBestandsnaam(Invoice $invoice): string
    {
        return "factuur-{$invoice->invoice_number}.pdf";
    }

    /** Ruwe PDF-inhoud, bijvoorbeeld als bijlage voor een e-mail. */
    public function pdfInhoud(Invoice $invoice): string
    {
        return $this->pdf($invoice)->output();
    }

    private function pdf(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->loadMissing(['member', 'items.product']);

        $invoiceLogoBase64 = null;
        $logoPath = Setting::get('invoice_logo');
        if ($logoPath) {
            $fullPath = public_path($logoPath);
            if (file_exists($fullPath)) {
                $mime = mime_content_type($fullPath);
                $invoiceLogoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
            }
        }

        $invoiceFooter   = Setting::get('invoice_footer');
        $companyAddress  = Setting::get('company_address');
        $companyKvk      = Setting::get('company_kvk');
        $companyVat      = Setting::get('company_vat');

        return Pdf::loadView('invoices.pdf', compact(
            'invoice', 'invoiceLogoBase64', 'invoiceFooter',
            'companyAddress', 'companyKvk', 'companyVat'
        ));
    }

    private function syncItems(Invoice $invoice, array $items): void
    {
        foreach ($items as $item) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'product_id'  => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'tax_rate'    => $item['tax_rate'],
            ]);
        }
    }

    private function nextInvoiceNumber(): string
    {
        $prefix = config('babb.invoice_prefix', 'BABB');
        $year   = now()->year;
        $base   = sprintf('%s-%d-', $prefix, $year);

        // Ook verwijderde (soft-deleted) facturen meetellen: hun nummer blijft bezet in de unieke index
        $max = Invoice::withTrashed()
            ->where('invoice_number', 'like', $base . '%')
            ->pluck('invoice_number')
            ->map(fn ($n) => (int) substr($n, strlen($base)))
            ->max();

        return sprintf('%s%04d', $base, ($max ?? 0) + 1);
    }
}
