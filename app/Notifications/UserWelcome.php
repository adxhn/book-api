<?php

namespace App\Notifications;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\WithoutRelations;

#[WithoutRelations]
class UserWelcome extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;
    public int $timeout = 15;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $verificationUrl
    )
    {
        $this->onQueue('low');
        $this->afterCommit();
    }

    public function backoff(): int
    {
        return 3;
    }

    public function retryUntil(): DateTime
    {
        return now()->plus(minutes: 5);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Hoşgeldiniz, Kaydınız Oluşturuldu')
            ->line('Kullanıcı adınız: ' . $notifiable->name)
            ->line('Lütfen aşağıdaki linke tıklayarak e-posta adresinizi onaylatın')
            ->action('E-posta adresini onayla', $this->verificationUrl);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
