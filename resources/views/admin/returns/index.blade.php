@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Return Requests</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Returns</span>
            </div>
        </div>
    </div>

    <div class="stats-grid" style="grid-template-columns: repeat(5, 1fr); margin-bottom: 24px;">
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['requested'] }}</div>
            <div class="label">Requested</div>
        </div>
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['approved'] }}</div>
            <div class="label">Approved</div>
        </div>
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['picked_up'] }}</div>
            <div class="label">Picked Up</div>
        </div>
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['refunded'] }}</div>
            <div class="label">Refunded</div>
        </div>
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['rejected'] }}</div>
            <div class="label">Rejected</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form action="{{ route('admin.returns.index') }}" method="GET"
                style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 240px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="RMA / Order / Customer"
                        value="{{ request('search') }}">
                </div>
                <div style="width: 220px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        @foreach(\App\Models\ReturnRequest::allStatuses() as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ \App\Models\ReturnRequest::statusLabel($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.returns.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>RMA</th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Vendor</th>
                        <th>Reason</th>
                        <th>Requested</th>
                        <th>Approved</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $returnRequest)
                        <tr>
                            <td style="font-weight: 600;">{{ $returnRequest->rma_number }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $returnRequest->order) }}"
                                    style="color: var(--primary); font-weight: 600;">
                                    #{{ $returnRequest->order->order_number }}
                                </a>
                            </td>
                            <td>{{ $returnRequest->user->name ?? 'N/A' }}</td>
                            <td>{{ $returnRequest->vendor->shop_name ?? 'N/A' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($returnRequest->reason, 40) }}</td>
                            <td>BDT {{ number_format($returnRequest->requested_refund_amount, 2) }}</td>
                            <td>BDT {{ number_format($returnRequest->approved_refund_amount ?? 0, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $returnRequest->status_badge }}">
                                    {{ $returnRequest->status_label }}
                                </span>
                            </td>
                            <td style="font-size: 13px; color: #64748b;">{{ $returnRequest->updated_at->format('M d, Y h:i A') }}
                            </td>
                            <td>
                                <a href="{{ route('admin.returns.show', $returnRequest) }}" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; color: #64748b; padding: 40px;">No return requests
                                found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $returns->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
