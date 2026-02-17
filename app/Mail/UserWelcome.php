<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

#[WithoutRelations]
class UserWelcome extends Mailable implements ShouldBeUnique
{
    use Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 15;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
    ) {
        $this->afterCommit();
        $this->onQueue('low');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('test@example.com', config('app.name')),
            subject: 'Hoşgeldiniz, Kaydınız Oluşturuldu',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.user-welcome',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function uniqueId(): string
    {
        return $this->user->id;
    }
}
