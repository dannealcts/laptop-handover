<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Laptop;

class UpgradeEligibilityMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $laptop;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Laptop $laptop)
    {
        $this->user = $user;
        $this->laptop = $laptop;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You’re Eligible for a Laptop Upgrade'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.upgrade-eligibility',
            with: [
                'user' => $this->user,
                'laptop' => $this->laptop,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}