@extends('admin.layouts.app')

@push('styles')
    <style>
        .audit-json {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
            padding: 0 10px;
            min-width: 220px;
        }

        .audit-json summary {
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            padding: 8px 0;
            list-style: none;
        }

        .audit-json summary::-webkit-details-marker {
            display: none;
        }

        .audit-json summary::before {
            content: "\25B8";
            display: inline-block;
            margin-right: 6px;
            transition: transform 0.2s ease;
        }

        .audit-json[open] summary::before {
            transform: rotate(90deg);
        }

        .audit-json pre {
            margin: 0 0 10px;
            padding: 10px;
            border-radius: 8px;
            background: #0f172a;
            color: #e2e8f0;
            font-size: 11px;
            line-height: 1.45;
            overflow: auto;
            max-height: 220px;
            white-space: pre;
        }

        .audit-json-empty {
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    @php
        $formatAuditJson = static function (mixed $value): string {
            $normalized = is_array($value) ? $value : [];

            return json_encode(
                $normalized,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            ) ?: '{}';
        };
    @endphp

    <div class="page-header">
        <div>
            <h1>Audit Logs</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Audit Logs</span>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form action="{{ route('admin.audit-logs.index') }}" method="GET"
                style="display: grid; grid-template-columns: repeat(6, minmax(140px, 1fr)); gap: 12px;">
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Event</label>
                    <select name="event" class="form-control">
                        <option value="">All</option>
                        @foreach($events as $event)
                            <option value="{{ $event }}" {{ request('event') === $event ? 'selected' : '' }}>{{ $event }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Vendor</label>
                    <select name="vendor_id" class="form-control">
                        <option value="">All</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ (string) request('vendor_id') === (string) $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->shop_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Actor</label>
                    <input type="text" name="actor" class="form-control" value="{{ request('actor') }}"
                        placeholder="Admin name">
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div style="display: flex; align-items: flex-end; gap: 8px;">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Event</th>
                        <th>Actor</th>
                        <th>Vendor</th>
                        <th>Target</th>
                        <th>Old Values</th>
                        <th>New Values</th>
                        <th>Meta</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="white-space: nowrap;">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td><span class="badge badge-info">{{ $log->event }}</span></td>
                            <td>{{ $log->actor->name ?? 'System' }}</td>
                            <td>{{ $log->vendor->shop_name ?? 'N/A' }}</td>
                            <td>
                                <div>{{ class_basename((string) $log->auditable_type) ?: 'N/A' }}</div>
                                <div style="font-size: 12px; color: #64748b;">ID: {{ $log->auditable_id ?? 'N/A' }}</div>
                            </td>
                            @php
                                $oldValues = is_array($log->old_values) ? $log->old_values : [];
                                $newValues = is_array($log->new_values) ? $log->new_values : [];
                                $metaValues = is_array($log->meta) ? $log->meta : [];
                            @endphp
                            <td>
                                @if($oldValues === [])
                                    <span class="audit-json-empty">-</span>
                                @else
                                    <details class="audit-json">
                                        <summary>{{ count($oldValues) }} field{{ count($oldValues) > 1 ? 's' : '' }}</summary>
                                        <pre>{{ $formatAuditJson($oldValues) }}</pre>
                                    </details>
                                @endif
                            </td>
                            <td>
                                @if($newValues === [])
                                    <span class="audit-json-empty">-</span>
                                @else
                                    <details class="audit-json">
                                        <summary>{{ count($newValues) }} field{{ count($newValues) > 1 ? 's' : '' }}</summary>
                                        <pre>{{ $formatAuditJson($newValues) }}</pre>
                                    </details>
                                @endif
                            </td>
                            <td>
                                @if($metaValues === [])
                                    <span class="audit-json-empty">-</span>
                                @else
                                    <details class="audit-json">
                                        <summary>{{ count($metaValues) }} field{{ count($metaValues) > 1 ? 's' : '' }}</summary>
                                        <pre>{{ $formatAuditJson($metaValues) }}</pre>
                                    </details>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #64748b; padding: 40px;">No audit logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
