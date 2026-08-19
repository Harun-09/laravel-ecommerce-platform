@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Order #{{ $order->order_number }}</h1>
            <div class="breadcrumb">
                <a href="{{ route('vendor.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('vendor.orders.index') }}">Orders</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>#{{ $order->order_number }}</span>
            </div>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('admin.orders.invoice.a4', $order) }}" class="btn btn-outline" target="_blank">
                <i class="fas fa-file-pdf"></i> A4 Invoice
            </a>
            <a href="{{ route('admin.orders.receipt.thermal', $order) }}" class="btn btn-outline" target="_blank">
                <i class="fas fa-receipt"></i> Thermal Receipt
            </a>
            @if($order->canBeCancelled())
                <form action="{{ route('vendor.orders.cancel', $order) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to cancel this order?')">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancel Order
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Update Status</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group" style="margin: 0;">
                                <label>Order Status</label>
                                <select name="status" class="form-control">
                                    <option value="{{ $order->status }}">{{ $order->status_label }} (Current)</option>
                                    @foreach($allowedNextStatuses as $statusOption)
                                        <option value="{{ $statusOption }}">{{ \App\Models\Order::statusLabel($statusOption) }}</option>
                                    @endforeach
                                </select>
                                @if(empty($allowedNextStatuses))
                                    <small style="color: #64748b;">No further lifecycle transition available.</small>
                                @else
                                    <small style="color: #64748b;">Lifecycle: Pending -> Confirmed -> Processing -> Shipped -> Delivered -> Canceled/Returned</small>
                                @endif
                            </div>

                            <div class="form-group" style="margin: 0;">
                                <label>Tracking Number</label>
                                <input type="text" name="tracking_number" class="form-control"
                                    value="{{ $order->tracking_number }}" placeholder="Enter tracking number">
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 16px;">
                            <label>Shipping Carrier</label>
                            <input type="text" name="shipping_carrier" class="form-control"
                                value="{{ $order->shipping_carrier }}" placeholder="e.g., Pathao, Sundarban, RedX">
                        </div>

                        <div class="form-group">
                            <label>Comment</label>
                            <textarea name="comment" class="form-control" rows="2"
                                placeholder="Optional note for this status update"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>

            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Order Items</h3>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">{{ $item->product_name }}</div>
                                        <div style="font-size: 12px; color: #64748b;">SKU: {{ $item->product_sku }}</div>
                                    </td>
                                    <td>{{ store_money($item->unit_price) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td style="text-align: right; font-weight: 600;">{{ store_money($item->total_price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #f8fafc;">
                            <tr>
                                <td colspan="3" style="text-align: right;">Subtotal</td>
                                <td style="text-align: right;">{{ store_money($order->subtotal) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align: right;">Shipping</td>
                                <td style="text-align: right;">{{ store_money($order->shipping_cost) }}</td>
                            </tr>
                            @if($order->discount_amount > 0)
                                <tr>
                                    <td colspan="3" style="text-align: right; color: #16a34a;">Discount</td>
                                    <td style="text-align: right; color: #16a34a;">-{{ store_money($order->discount_amount) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: 700;">Total</td>
                                <td style="text-align: right; font-weight: 700;">{{ store_money($order->total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Order Timeline</h3>
                </div>
                <div class="card-body">
                    <div style="position: relative; padding-left: 24px;">
                        @foreach($order->statusHistories as $history)
                            <div
                                style="position: relative; padding-bottom: 20px; border-left: 2px solid #e2e8f0; margin-left: 8px; padding-left: 20px;">
                                <div
                                    style="position: absolute; left: -9px; top: 0; width: 16px; height: 16px; background: {{ $loop->first ? 'var(--primary)' : '#e2e8f0' }}; border-radius: 50%; border: 3px solid #fff;">
                                </div>
                                <div style="font-weight: 600;">
                                    {{ $history->new_status_label }}
                                    @if($history->old_status_label)
                                        <span style="color: #64748b; font-weight: 400;">(from {{ $history->old_status_label }})</span>
                                    @endif
                                </div>
                                <div style="font-size: 13px; color: #64748b;">{{ $history->created_at->format('M d, Y h:i A') }}</div>
                                @if($history->comment)
                                    <div style="margin-top: 6px; font-size: 13px; color: #475569;">{{ $history->comment }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 14px;">
                        <span style="color: #64748b;">Order Status</span>
                        <span class="badge badge-{{ $order->status_badge }}">{{ $order->status_label }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 14px;">
                        <span style="color: #64748b;">Payment Status</span>
                        <span class="badge badge-{{ $order->payment_status_badge }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 14px;">
                        <span style="color: #64748b;">Payment Method</span>
                        <span style="font-weight: 600;">{{ strtoupper($order->payment_method) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Order Date</span>
                        <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Customer</h3>
                </div>
                <div class="card-body">
                    <p style="font-weight: 600;">{{ $order->user->name ?? 'Guest' }}</p>
                    <p style="color: #64748b; font-size: 13px;">{{ $order->shipping_phone }}</p>
                    <p style="color: #64748b; font-size: 13px;">{{ $order->shipping_email }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Shipping Address</h3>
                </div>
                <div class="card-body">
                    <p style="font-weight: 600;">{{ $order->shipping_name }}</p>
                    <p style="color: #64748b;">{{ $order->shipping_address }}</p>
                    <p style="color: #64748b;">{{ $order->shipping_city }}, {{ $order->shipping_postal_code }}</p>
                    @if($order->delivery_zone)
                        <p style="color: #64748b;">Zone: {{ $order->delivery_zone }}</p>
                    @endif
                    <p style="color: #64748b;">Method: {{ $order->shipping_method ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

