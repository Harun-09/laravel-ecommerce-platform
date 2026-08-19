@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Order Management</h1>
            <div class="breadcrumb">
                <a href="{{ route('vendor.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Orders</span>
            </div>
        </div>
    </div>

    <div class="stats-grid" style="grid-template-columns: repeat(5, 1fr); margin-bottom: 24px;">
        <div class="stat-card" style="padding: 16px 20px;">
            <div class="value" style="font-size: 24px;">{{ $stats['pending'] }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat-card" style="padding: 16px 20px;">
            <div class="value" style="font-size: 24px;">{{ $stats['confirmed'] }}</div>
            <div class="label">Confirmed</div>
        </div>
        <div class="stat-card" style="padding: 16px 20px;">
            <div class="value" style="font-size: 24px;">{{ $stats['processing'] }}</div>
            <div class="label">Processing</div>
        </div>
        <div class="stat-card" style="padding: 16px 20px;">
            <div class="value" style="font-size: 24px;">{{ $stats['shipped'] }}</div>
            <div class="label">Shipped</div>
        </div>
        <div class="stat-card" style="padding: 16px 20px;">
            <div class="value" style="font-size: 24px;">{{ $stats['delivered'] }}</div>
            <div class="label">Delivered</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form action="{{ route('vendor.orders.index') }}" method="GET"
                style="display: grid; grid-template-columns: minmax(220px, 1fr) repeat(4, minmax(140px, 1fr)) auto; gap: 12px; align-items: end;">
                <div>
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Order # or customer"
                        value="{{ request('search') }}">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        @foreach(\App\Models\Order::lifecycleOrder() as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ \App\Models\Order::statusLabel($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Payment</label>
                    <select name="payment_status" class="form-control">
                        <option value="">All</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="partially_refunded" {{ request('payment_status') === 'partially_refunded' ? 'selected' : '' }}>Partially Refunded</option>
                        <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td style="font-weight: 600;">#{{ $order->order_number }}</td>
                            <td>
                                <div>{{ $order->user->name ?? 'Guest' }}</div>
                                <small style="color: #64748b;">{{ $order->shipping_phone }}</small>
                            </td>
                            <td>{{ $order->items->count() }}</td>
                            <td style="font-weight: 600;">{{ store_money($order->total) }}</td>
                            <td>
                                <span class="badge badge-{{ $order->payment_status_badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $order->status_badge }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td style="color: #64748b;">{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('vendor.orders.show', $order) }}" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #64748b; padding: 36px;">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection

