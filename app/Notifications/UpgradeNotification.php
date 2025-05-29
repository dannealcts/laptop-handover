<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UpgradeNotification extends Notification
{
    protected $laptop;

    public function __construct($laptop)
    {
        $this->laptop = $laptop;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Laptop Eligible for Upgrade')
            ->greeting('Hello ' . $notifiable->name)
            ->line("Your assigned laptop (Tag: {$this->laptop->tag_no}) is now eligible for an upgrade.")
            ->action('Request Upgrade', url('/staff/request-upgrade'))
            ->line('Please contact IT if you need further assistance.');
    }
}
