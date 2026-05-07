<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeclaratieMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Event $event) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Declaratie — ' . $this->event->title);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.declaratie');
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->event->costs as $cost) {
            if ($cost->receipt_path && file_exists(public_path($cost->receipt_path))) {
                $attachments[] = Attachment::fromPath(public_path($cost->receipt_path))
                    ->as(basename($cost->receipt_path));
            }
        }

        return $attachments;
    }
}
