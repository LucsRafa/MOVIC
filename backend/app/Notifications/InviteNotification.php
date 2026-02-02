<?php

namespace App\Notifications;

use App\Models\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteNotification extends Notification
{
    use Queueable;

    public function __construct(private Invite $invite)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Convite MOVIC')
            ->line('Voce foi convidado para acessar o MOVIC.')
            ->line('Token: '.$this->invite->token)
            ->line('Valido ate: '.$this->invite->expires_at?->toDateTimeString());
    }
}
