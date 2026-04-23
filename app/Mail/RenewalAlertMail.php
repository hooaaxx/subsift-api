<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenewalAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Subscription $subscription) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to:      $this->subscription->user->email,
            subject: "Reminder: {$this->subscription->name} renews soon",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.renewal-alert');
    }
}
