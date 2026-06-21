<?php

namespace App\Mail;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PollPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Poll $poll,
        public User $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nouveau sondage : {$this->poll->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.poll-published',
        );
    }
}
