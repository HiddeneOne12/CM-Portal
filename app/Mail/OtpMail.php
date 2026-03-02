<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $logo;

    /**
     * Create a new message instance.
     */
    public function __construct(string $code)
    {
        $this->code = $code;

        // Use public URL instead of embedding
        $this->logo = url('frontend/assets/images/email-logo.jpg');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), 'Cyber Majlis'),
            subject: 'Cyber Majlis – One-time code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'code' => $this->code,
                'logo' => $this->logo,
            ],
        );
    }
}
