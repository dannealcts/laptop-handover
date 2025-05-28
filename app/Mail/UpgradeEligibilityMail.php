<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UpgradeEligibilityMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $laptop;

    public function __construct($user, $laptop)
    {
        $this->user = $user;
        $this->laptop = $laptop;
    }

    public function build()
    {
        return $this->subject('Laptop Upgrade Eligibility Notification')
                    ->view('emails.upgrade-eligibility'); // make sure this blade file exists
    }
}
