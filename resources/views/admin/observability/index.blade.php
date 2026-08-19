@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Observability</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Observability</span>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon orange"><i class="fas fa-bell"></i></div>
            <div class="value">{{ number_format((int) $stats['open_alerts']) }}</div>
            <div class="label">Open Alerts</div>
        </div>
        <div class="stat-card">
            <div class="icon purple"><i class="fas fa-triangle-exclamation"></i></div>
            <div class="value">{{ number_format((int) $stats['critical_open_alerts']) }}</div>
            <div class="label">Critical Open Alerts</div>
        </div>
        <div class="stat-card">
            <div class="icon red" style="background: #fee2e2; color: #b91c1c;"><i class="fas fa-credit-card"></i></div>
            <div class="value">{{ number_format((int) $stats['payment_failures_24h']) }}</div>
            <div class="label">Payment Failures (24h)</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body">
            <form action="{{ route('admin.observability.index') }}" method="GET"
                style="display: grid; grid-template-columns: repeat(7, minmax(120px, 1fr)); gap: 12px;">
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Alert Status</label>
                    <select name="alert_status" class="form-control">
                        <option value="">All</option>
                        <option value="open" {{ request('alert_status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="resolved" {{ request('alert_status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Alert Severity</label>
                    <select name="alert_severity" class="form-control">
                        <option value="">All</option>
                        @foreach(['warning', 'error', 'critical'] as $severity)
                            <option value="{{ $severity }}" {{ request('alert_severity') === $severity ? 'selected' : '' }}>
                                {{ ucfirst($severity) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Event Provider</label>
                    <select name="event_provider" class="form-control">
                        <option value="">All</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider }}" {{ request('event_provider') === $provider ? 'selected' : '' }}>
                                {{ strtoupper($provider) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Event Severity</label>
                    <select name="event_severity" class="form-control">
                        <option value="">All</option>
                        @foreach(['info', 'warning', 'error', 'critical'] as $severity)
                            <option value="{{ $severity }}" {{ request('event_severity') === $severity ? 'selected' : '' }}>
                                {{ ucfirst($severity) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Event Status</label>
                    <select name="event_status" class="form-control">
                        <option value="">All</option>
                        @foreach(['pending', 'processing', 'completed', 'failed'] as $status)
                            <option value="{{ $status }}" {{ request('event_status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div style="display: flex; align-items: flex-end; gap: 8px; grid-column: 1 / -1;">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <a href="{{ route('admin.observability.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3>Monitoring Alerts</h3>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Triggered At</th>
                        <th>Type</th>
                        <th>Severity</th>
                        <th>Source</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Resolved By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alerts as $alert)
                        <tr>
                            <td>{{ $alert->triggered_at?->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $alert->type }}</td>
                            <td>
                                <span class="badge badge-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'error' ? 'warning' : 'info') }}">
                                    {{ ucfirst($alert->severity) }}
                                </span>
                            </td>
                            <td>{{ $alert->source ?: 'N/A' }}</td>
                            <td>
                                <div>{{ $alert->title }}</div>
                                @if($alert->description)
                                    <div style="font-size: 12px; color: #64748b;">{{ $alert->description }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $alert->status === 'open' ? 'warning' : 'success' }}">
                                    {{ ucfirst($alert->status) }}
                                </span>
                            </td>
                            <td>{{ $alert->resolver->name ?? 'N/A' }}</td>
                            <td>
                                @if($alert->status === 'open')
                                    <form action="{{ route('admin.observability.alerts.resolve', $alert) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">Resolve</button>
                                    </form>
                                @else
                                    <span style="font-size: 12px; color: #64748b;">Resolved</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #64748b; padding: 32px;">
                                No monitoring alerts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($alerts->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Payment Event Logs</h3>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Provider</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>Severity</th>
                        <th>Order</th>
                        <th>Payment</th>
                        <th>Message</th>
                        <th>Retry</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>{{ $event->happened_at?->format('Y-m-d H:i:s') }}</td>
                            <td>{{ strtoupper($event->provider) }}</td>
                            <td>{{ $event->event_type }}</td>
                            <td>{{ $event->status ?: 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $event->severity === 'critical' ? 'danger' : ($event->severity === 'error' ? 'warning' : ($event->severity === 'warning' ? 'info' : 'secondary')) }}">
                                    {{ ucfirst($event->severity) }}
                                </span>
                            </td>
                            <td>{{ $event->order->order_number ?? 'N/A' }}</td>
                            <td>{{ $event->payment->transaction_id ?? 'N/A' }}</td>
                            <td>{{ $event->message ?: 'N/A' }}</td>
                            <td>{{ $event->is_retry ? 'Yes' : 'No' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; color: #64748b; padding: 32px;">
                                No payment events found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($events->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $events->links() }}
            </div>
        @endif
    </div>
@endsection
