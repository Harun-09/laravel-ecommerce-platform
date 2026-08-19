@extends('admin.layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1>Order #{{ $order->order_number }}</h1>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <a href="{{ route('admin.orders.index') }}">Orders</a>
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
            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
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
        <!-- Order Items -->
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
                            <th>Quantity</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <img src="{{ $item->product_image ? asset('storage/' . $item->product_image) : '/placeholder.jpg' }}" alt="" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                                        <div>
                                            <p style="font-weight: 500;">{{ $item->product_name }}</p>
                                            <p style="font-size: 13px; color: #64748b;">SKU: {{ $item->product_sku }}</p>
                                            @if($item->variation_details)
                                                <p style="font-size: 12px; color: #64748b;">
                                                    @foreach($item->variation_details as $detail)
                                                        {{ $detail['attribute'] }}: {{ $detail['value'] }}
                                                    @endforeach
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>BDT {{ number_format($item->unit_price) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td style="text-align: right; font-weight: 600;">BDT {{ number_format($item->total_price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background: #f8fafc;">
                        <tr>
                            <td colspan="3" style="text-align: right;">Subtotal</td>
                            <td style="text-align: right;">BDT {{ number_format($order->subtotal) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="3" style="text-align: right; color: #16a34a;">Discount</td>
                                <td style="text-align: right; color: #16a34a;">-BDT {{ number_format($order->discount_amount) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3" style="text-align: right;">Shipping</td>
                            <td style="text-align: right;">BDT {{ number_format($order->shipping_cost) }}</td>
                        </tr>
                        @if($order->cod_fee > 0)
                            <tr>
                                <td colspan="3" style="text-align: right;">COD Fee</td>
                                <td style="text-align: right;">BDT {{ number_format($order->cod_fee) }}</td>
                            </tr>
                        @endif
                        @if(($order->refunded_amount ?? 0) > 0)
                            <tr>
                                <td colspan="3" style="text-align: right; color: #d97706;">Refund</td>
                                <td style="text-align: right; color: #d97706;">-BDT {{ number_format($order->refunded_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: 600; font-size: 16px;">Total</td>
                            <td style="text-align: right; font-weight: 700; font-size: 18px; color: var(--primary);">BDT {{ number_format($order->total) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: 600;">Vendor Payable</td>
                            <td style="text-align: right; font-weight: 700; color: #15803d;">BDT {{ number_format($order->payout_payable_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Update Status -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>Update Status</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
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
                                <small style="color: #64748b;">No further lifecycle transition available for this order.</small>
                            @else
                                <small style="color: #64748b;">Lifecycle: Pending -> Confirmed -> Processing -> Shipped -> Delivered -> Canceled/Returned</small>
                            @endif
                        </div>
                        
                        <div class="form-group" style="margin: 0;">
                            <label>Tracking Number</label>
                            <input type="text" name="tracking_number" class="form-control" value="{{ $order->tracking_number }}" placeholder="Enter tracking number">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 16px;">
                        <label>Shipping Carrier</label>
                        <input type="text" name="shipping_carrier" class="form-control" value="{{ $order->shipping_carrier }}" placeholder="e.g., Pathao, Sundarban, RedX">
                    </div>
                    
                    <div class="form-group">
                        <label>Note/Comment</label>
                        <textarea name="comment" class="form-control" rows="2" placeholder="Add a note about this status update"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>
        </div>
        
        <!-- Update Payment -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>Update Payment</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group" style="margin: 0;">
                            <label>Payment Status</label>
                            <select name="payment_status" class="form-control">
                                @foreach(['pending', 'paid', 'failed', 'partially_refunded', 'refunded'] as $paymentStatus)
                                    <option value="{{ $paymentStatus }}" {{ $order->payment_status === $paymentStatus ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $paymentStatus)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" style="margin: 0;">
                            <label>Refund Amount (if refunded)</label>
                            <input
                                type="number"
                                name="refund_amount"
                                class="form-control"
                                min="0"
                                max="{{ $order->total }}"
                                step="0.01"
                                value="{{ $order->refunded_amount ?? 0 }}"
                                placeholder="0.00"
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn btn-outline" style="margin-top: 16px;">Update Payment Status</button>
                </form>
            </div>
        </div>

        @if($order->returnRequests->isNotEmpty())
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Return Requests</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; gap: 10px;">
                        @foreach($order->returnRequests as $returnRequest)
                            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <a href="{{ route('admin.returns.show', $returnRequest) }}"
                                        style="color: var(--primary); font-weight: 600;">
                                        {{ $returnRequest->rma_number }}
                                    </a>
                                    <span class="badge badge-{{ $returnRequest->status_badge }}">{{ $returnRequest->status_label }}</span>
                                </div>
                                <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                                    Requested: BDT {{ number_format($returnRequest->requested_refund_amount, 2) }}
                                    @if($returnRequest->approved_refund_amount)
                                        | Approved: BDT {{ number_format($returnRequest->approved_refund_amount, 2) }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Status History -->
        <div class="card">
            <div class="card-header">
                <h3>Order Timeline</h3>
            </div>
            <div class="card-body">
                <div style="position: relative; padding-left: 24px;">
                    @foreach($order->statusHistories as $history)
                        <div style="position: relative; padding-bottom: 24px; border-left: 2px solid #e2e8f0; margin-left: 8px; padding-left: 24px;">
                            <div style="position: absolute; left: -9px; top: 0; width: 16px; height: 16px; background: {{ $loop->first ? 'var(--primary)' : '#e2e8f0' }}; border-radius: 50%; border: 3px solid white;"></div>
                            <div style="font-weight: 600;">
                                {{ $history->new_status_label }}
                                @if($history->old_status_label)
                                    <span style="font-weight: 400; color: #64748b;">(from {{ $history->old_status_label }})</span>
                                @endif
                            </div>
                            <div style="font-size: 13px; color: #64748b;">{{ $history->created_at->format('M d, Y h:i A') }}</div>
                            @if($history->comment)
                                <div style="margin-top: 8px; padding: 8px 12px; background: #f8fafc; border-radius: 6px; font-size: 14px;">{{ $history->comment }}</div>
                            @endif
                            @if($history->user)
                                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">By {{ $history->user->name }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <div>
        <!-- Order Summary -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                    <span style="color: #64748b;">Order Status</span>
                    <span class="badge badge-{{ $order->status_badge }}">{{ $order->status_label }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                    <span style="color: #64748b;">Payment Status</span>
                    <span class="badge badge-{{ $order->payment_status_badge }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                    <span style="color: #64748b;">Payment Method</span>
                    <span style="font-weight: 500;">{{ strtoupper($order->payment_method) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                    <span style="color: #64748b;">Refund Amount</span>
                    <span style="font-weight: 500;">BDT {{ number_format($order->refunded_amount ?? 0, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                    <span style="color: #64748b;">Vendor Payable</span>
                    <span style="font-weight: 700; color: #15803d;">BDT {{ number_format($order->payout_payable_amount, 2) }}</span>
                </div>
                @if($order->delivery_zone)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                        <span style="color: #64748b;">Delivery Zone</span>
                        <span style="font-weight: 500;">{{ $order->delivery_zone }}</span>
                    </div>
                @endif
                
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Order Date</span>
                    <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Customer Info -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>Customer</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 18px;">
                        {{ substr($order->user->name ?? 'G', 0, 1) }}
                    </div>
                    <div>
                        <p style="font-weight: 600;">{{ $order->user->name ?? 'Guest' }}</p>
                        <p style="font-size: 13px; color: #64748b;">{{ $order->user->email ?? $order->shipping_email }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Shipping Address -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>Shipping Address</h3>
            </div>
            <div class="card-body">
                <p style="font-weight: 500;">{{ $order->shipping_name }}</p>
                <p style="color: #64748b;">{{ $order->shipping_phone }}</p>
                <p style="color: #64748b; margin-top: 8px;">{{ $order->shipping_address }}</p>
                <p style="color: #64748b;">{{ $order->shipping_city }}, {{ $order->shipping_postal_code }}</p>
                @if($order->delivery_zone)
                    <p style="color: #64748b; margin-top: 4px;">Zone: {{ $order->delivery_zone }}</p>
                @endif
                <p style="color: #64748b; margin-top: 4px;">Method: {{ $order->shipping_method ?? 'N/A' }}</p>
            </div>
        </div>
        
        <!-- Vendor Info -->
        <div class="card">
            <div class="card-header">
                <h3>Vendor</h3>
            </div>
            <div class="card-body">
                <p style="font-weight: 600;">{{ $order->vendor->shop_name ?? 'N/A' }}</p>
                @if($order->vendor)
                    <p style="font-size: 13px; color: #64748b;">{{ $order->vendor->email }}</p>
                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e2e8f0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #64748b;">Commission</span>
                            <span>BDT {{ number_format($order->commission_amount) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Vendor Earning</span>
                            <span style="font-weight: 600; color: #16a34a;">BDT {{ number_format($order->vendor_earning) }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection





