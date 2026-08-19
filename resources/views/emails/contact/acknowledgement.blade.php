<p>Hello {{ $contactMessage->name }},</p>

<p>Thank you for contacting {{ config('app.name') }}. We received your message and our support team will get back to you soon.</p>

<p><strong>Your Subject:</strong> {{ $contactMessage->subject }}</p>
<p><strong>Your Message:</strong></p>
<p>{{ $contactMessage->message }}</p>

<p>Regards,<br>{{ config('mail.contact.name', config('app.name') . ' Support') }}</p>
