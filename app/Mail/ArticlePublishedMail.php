<?php

namespace App\Mail;

use App\Models\Article;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticlePublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Article $article,
        public User $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nouvel article : {$this->article->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.article-published',
        );
    }
}
