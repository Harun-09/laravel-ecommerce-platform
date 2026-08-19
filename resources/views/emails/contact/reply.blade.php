<p>Hello {{ $contactMessage->name }},</p>

<p>Thank you for contacting {{ config('app.name') }}.</p>

<p><strong>Our Reply:</strong></p>
<p>{!! nl2br(e($replyMessage)) !!}</p>

<hr>

<p><strong>Your Original Subject:</strong> {{ $contactMessage->subject }}</p>
<p><strong>Your Original Message:</strong></p>
<p>{{ $contactMessage->message }}</p>

<p>Regards,<br>{{ $repliedByName }}</p>
