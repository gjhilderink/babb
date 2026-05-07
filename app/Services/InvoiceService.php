<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class InvoiceService
{
    public function create(array $data): Invoice
    {
        $invoice = Invoice::create([
            'invoice_number' => $this->nextInvoiceNumber(),
            'member_id'      => $data['member_id'],
            'issue_date'     => $data['issue_date'],
            'due_date'       => $data['due_date'],
            'notes'          => $data['notes'] ?? null,
            'status'         => 'draft',
        ]);

        $this->syncItems($invoice, $data['items']);
        $invoice->recalculateTotals();

        return $invoice;
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update([
            'member_id'  => $data['member_id'],
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
        $invoiceLogoBase64 = null;
        $logoPath = Setting::get('invoice_logo');
        if ($logoPath) {
            $fullPath = public_path($logoPath);
            if (file_exists($fullPath)) {
                $mime = mime_content_type($fullPath);
                $invoiceLogoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
            }
        }

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'invoiceLogoBase64'));

        return $pdf->download("factuur-{$invoice->invoice_number}.pdf");
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
        $last   = Invoice::whereYear('created_at', $year)->max('invoice_number');

        if ($last) {
            $seq = (int) substr($last, -4) + 1;
        } else {
            $seq = 1;
        }

        return sprintf('%s-%d-%04d', $prefix, $year, $seq);
    }
}
