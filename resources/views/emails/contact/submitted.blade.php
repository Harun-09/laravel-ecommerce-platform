<p>A new contact inquiry has been submitted.</p>

<p><strong>Name:</strong> {{ $contactMessage->name }}</p>
<p><strong>Email:</strong> {{ $contactMessage->email }}</p>
<p><strong>Phone:</strong> {{ $contactMessage->phone ?: 'N/A' }}</p>
<p><strong>Subject:</strong> {{ $contactMessage->subject }}</p>
<p><strong>Submitted At:</strong> {{ $contactMessage->created_at?->format('Y-m-d h:i A') }}</p>

<p><strong>Message:</strong></p>
<p>{{ $contactMessage->message }}</p>

<p>
    <small>
        IP: {{ $contactMessage->ip_address ?: 'N/A' }}<br>
        User Agent: {{ $contactMessage->user_agent ?: 'N/A' }}
    </small>
</p>
