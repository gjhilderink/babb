<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Setting;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public const STANDAARD_ONDERWERP = 'Factuur {factuurnummer} van BABB';

    public const STANDAARD_TEKST = "Beste {naam},\n\n"
        ."In de bijlage vind je factuur {factuurnummer} van {factuurdatum} ter hoogte van {bedrag}.\n"
        ."We verzoeken je het bedrag uiterlijk {vervaldatum} over te maken.\n\n"
        ."Met vriendelijke groet,\n\nBABB";

    /** Beschikbare variabelen voor onderwerp en tekst (getoond bij de instellingen). */
    public const VARIABELEN = [
        '{naam}'          => 'Naam van het lid',
        '{factuurnummer}' => 'Factuurnummer',
        '{bedrag}'        => 'Totaalbedrag incl. BTW',
        '{factuurdatum}'  => 'Factuurdatum',
        '{vervaldatum}'   => 'Vervaldatum',
    ];

    public function __construct(public Invoice $invoice) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->vul(Setting::get('invoice_mail_subject') ?: self::STANDAARD_ONDERWERP));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.invoice', with: [
            'tekst' => $this->vul(Setting::get('invoice_mail_body') ?: self::STANDAARD_TEKST),
        ]);
    }

    public function attachments(): array
    {
        $service = app(InvoiceService::class);

        return [
            Attachment::fromData(fn () => $service->pdfInhoud($this->invoice), $service->pdfBestandsnaam($this->invoice))
                ->withMime('application/pdf'),
        ];
    }

    private function vul(string $tekst): string
    {
        $i = $this->invoice;

        return strtr($tekst, [
            '{naam}'          => $i->member?->full_name ?? '',
            '{factuurnummer}' => $i->invoice_number,
            '{bedrag}'        => '€ '.number_format((float) $i->total, 2, ',', '.'),
            '{factuurdatum}'  => $i->issue_date?->format('d-m-Y') ?? '',
            '{vervaldatum}'   => $i->due_date?->format('d-m-Y') ?? '',
        ]);
    }
}
