@extends('admin.layouts.app')

@section('content')
    @php
        $highlightId = (int) request('highlight', 0);
    @endphp

    <div class="page-header">
        <div>
            <h1>Messages</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Messages</span>
            </div>
        </div>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert alert-error" style="margin: 16px;">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                        @php
                            $isHighlighted = $highlightId > 0 && $highlightId === (int) $message->id;
                        @endphp
                        <tr style="{{ $isHighlighted ? 'background: #eef2ff;' : '' }}">
                            <td style="font-weight: 600;">{{ $message->name }}</td>
                            <td>{{ $message->email }}</td>
                            <td>{{ $message->subject }}</td>
                            <td style="max-width: 420px;">{{ \Illuminate\Support\Str::limit($message->message, 120) }}</td>
                            <td>
                                @if($message->status === \App\Models\ContactMessage::STATUS_NEW)
                                    <span class="badge badge-info">New</span>
                                @elseif($message->status === \App\Models\ContactMessage::STATUS_RESOLVED)
                                    <span class="badge badge-success">Resolved</span>
                                @else
                                    <span class="badge badge-secondary">Read</span>
                                @endif
                            </td>
                            <td style="color: #64748b;">{{ optional($message->created_at)->diffForHumans() }}</td>
                            <td>
                                @if($message->status === \App\Models\ContactMessage::STATUS_RESOLVED)
                                    <span style="font-size: 12px; color: #16a34a; font-weight: 600;">Replied</span>
                                @else
                                    <details {{ $isHighlighted ? 'open' : '' }}>
                                        <summary style="cursor: pointer; color: var(--primary); font-weight: 600;">Reply</summary>
                                        <form action="{{ route('admin.messages.reply', $message) }}" method="POST"
                                            style="margin-top: 10px; min-width: 280px;">
                                            @csrf
                                            <textarea name="reply_message" rows="4" class="form-control"
                                                placeholder="Write your reply..." required>{{ old('reply_message') }}</textarea>
                                            <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 8px;">
                                                Send Reply
                                            </button>
                                        </form>
                                    </details>
                                @endif
                            </td>
                        </tr>
                        @if($message->status === \App\Models\ContactMessage::STATUS_RESOLVED && $message->reply_message)
                            <tr>
                                <td colspan="7" style="background: #f8fafc;">
                                    <div style="font-size: 13px; color: #475569; line-height: 1.6;">
                                        <strong>Last Reply:</strong> {{ $message->reply_message }}
                                        @if($message->repliedBy || $message->replied_at)
                                            <div style="margin-top: 4px; font-size: 12px; color: #64748b;">
                                                Replied by {{ $message->repliedBy?->name ?? 'Admin' }}
                                                @if($message->replied_at)
                                                    • {{ $message->replied_at->diffForHumans() }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #64748b; padding: 40px;">No messages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($messages->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $messages->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
