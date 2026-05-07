<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $plainPassword,
        public string $portalUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welkom bij het BABB Portaal');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.welcome-user');
    }
}
