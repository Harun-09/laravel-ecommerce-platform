@extends('admin.layouts.app')

@section('content')
    @php
        $highlightId = (string) request('highlight', '');
    @endphp

    <div class="page-header">
        <div>
            <h1>Notifications</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Notifications</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        @php
                            $data = is_array($notification->data) ? $notification->data : [];
                            $event = strtolower((string) ($data['event'] ?? ''));
                            $isHighlighted = $highlightId !== '' && $highlightId === (string) $notification->id;
                            $title = match ($event) {
                                'placed' => 'New Order Placed',
                                'shipped' => 'Order Shipped',
                                'delivered' => 'Order Delivered',
                                'refund' => 'Refund Update',
                                default => 'System Notification',
                            };
                            $message = (string) ($data['message'] ?? 'You have a new notification.');
                        @endphp
                        <tr style="{{ $isHighlighted ? 'background: #eef2ff;' : '' }}">
                            <td style="font-weight: 600;">{{ $title }}</td>
                            <td>{{ $message }}</td>
                            <td>
                                @if($notification->read_at)
                                    <span class="badge badge-secondary">Read</span>
                                @else
                                    <span class="badge badge-info">Unread</span>
                                @endif
                            </td>
                            <td style="color: #64748b;">{{ optional($notification->created_at)->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b; padding: 40px;">No notifications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection
