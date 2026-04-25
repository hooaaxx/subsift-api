<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenewalVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Subscription $subscription,
        public readonly array $tokens
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to:      $this->subscription->user->email,
            subject: "Did your {$this->subscription->name} subscription renew?",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.renewal-verification');
    }
}
