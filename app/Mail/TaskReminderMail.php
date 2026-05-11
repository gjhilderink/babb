<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Collection $tasks,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Herinnering: je hebt ' . $this->tasks->count() . ' verlopen ' . ($this->tasks->count() === 1 ? 'taak' : 'taken'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tasks.reminder',
        );
    }
}
