<?php

namespace App\Mail;

use App\Domains\ECommerce\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactMessageAcknowledgementMail extends Mailable
{
    use Queueable;

    public function __construct(public ContactMessage $contactMessage)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We Received Your Message - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact.acknowledgement',
            with: [
                'contactMessage' => $this->contactMessage,
            ],
        );
    }
}
