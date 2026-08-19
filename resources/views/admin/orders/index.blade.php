@extends('admin.layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1>Orders</h1>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <span>Orders</span>
        </div>
    </div>
</div>

<!-- Order Stats -->
<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
    <div class="stat-card" style="padding: 16px 20px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div class="icon" style="width: 40px; height: 40px; margin: 0; background: #fef3c7; color: #d97706;">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="value" style="font-size: 24px;">{{ $stats['pending'] }}</div>
                <div class="label" style="margin-top: 0;">Pending</div>
            </div>
        </div>
    </div>
    <div class="stat-card" style="padding: 16px 20px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div class="icon" style="width: 40px; height: 40px; margin: 0; background: #dbeafe; color: #2563eb;">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <div class="value" style="font-size: 24px;">{{ $stats['paid'] }}</div>
                <div class="label" style="margin-top: 0;">Confirmed</div>
            </div>
        </div>
    </div>
    <div class="stat-card" style="padding: 16px 20px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div class="icon" style="width: 40px; height: 40px; margin: 0; background: #e0e7ff; color: #4f46e5;">
                <i class="fas fa-truck"></i>
            </div>
            <div>
                <div class="value" style="font-size: 24px;">{{ $stats['shipped'] }}</div>
                <div class="label" style="margin-top: 0;">Shipped</div>
            </div>
        </div>
    </div>
    <div class="stat-card" style="padding: 16px 20px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div class="icon" style="width: 40px; height: 40px; margin: 0; background: #dcfce7; color: #16a34a;">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <div class="value" style="font-size: 24px;">{{ $stats['delivered'] }}</div>
                <div class="label" style="margin-top: 0;">Delivered</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body" style="padding: 16px 24px;">
        <form action="{{ route('admin.orders.index') }}" method="GET" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Order # or customer name" value="{{ request('search') }}">
            </div>
            <div style="width: 150px;">
                <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Confirmed</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Canceled</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                </select>
            </div>
            <div style="width: 150px;">
                <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Payment</label>
                <select name="payment_status" class="form-control">
                    <option value="">All Payment</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="partially_refunded" {{ request('payment_status') == 'partially_refunded' ? 'selected' : '' }}>Partially Refunded</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Vendor</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" style="color: var(--primary); font-weight: 600;">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            <div>
                                <p style="font-weight: 500;">{{ $order->user->name ?? 'Guest' }}</p>
                                <p style="font-size: 13px; color: #64748b;">{{ $order->shipping_phone }}</p>
                            </div>
                        </td>
                        <td>{{ $order->vendor->shop_name ?? 'N/A' }}</td>
                        <td>{{ $order->items->count() }} items</td>
                        <td style="font-weight: 600;">৳{{ number_format($order->total) }}</td>
                        <td>
                            <span class="badge badge-{{ $order->payment_status_badge }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $order->status_badge }}">{{ $order->status_label }}</span>
                        </td>
                        <td style="color: #64748b; font-size: 13px;">{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: #64748b; padding: 40px;">No orders found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($orders->hasPages())
        <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
            {{ $orders->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
