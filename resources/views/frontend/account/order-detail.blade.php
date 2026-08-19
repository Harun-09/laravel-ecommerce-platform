@extends('layouts.app')

@section('content')
    <div class="container section">
        <div class="account-layout-grid">
            @include('frontend.account.partials.sidebar', ['user' => auth()->user()])

            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
                    <div>
                        <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 6px;">Order #{{ $order->order_number }}</h1>
                        <p style="font-size: 14px; color: #6b7280;">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        @if($canRetryPayment)
                            <a href="{{ route('payment.process', $order->order_number) }}" class="btn btn-primary">
                                <i class="fas fa-credit-card"></i> Complete Payment
                            </a>
                        @endif
                        <a href="{{ route('account.orders.invoice.a4', $order->order_number) }}" class="btn btn-outline"
                            target="_blank">
                            <i class="fas fa-file-pdf"></i> A4 Invoice
                        </a>
                        <a href="{{ route('account.orders.receipt.thermal', $order->order_number) }}" class="btn btn-outline"
                            target="_blank">
                            <i class="fas fa-receipt"></i> Thermal Receipt
                        </a>
                        <a href="{{ route('account.orders') }}" class="btn btn-outline">Back to Orders</a>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px;">
                    <div class="card" style="padding: 16px;">
                        <p style="font-size: 12px; color: #6b7280;">Order Status</p>
                        <div style="margin-top: 6px;">
                            <span class="badge badge-{{ $order->status_badge }}">{{ $order->status_label }}</span>
                        </div>
                    </div>
                    <div class="card" style="padding: 16px;">
                        <p style="font-size: 12px; color: #6b7280;">Payment Status</p>
                        <div style="margin-top: 6px;">
                            <span class="badge badge-{{ $order->payment_status_badge }}">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="card" style="padding: 16px;">
                        <p style="font-size: 12px; color: #6b7280;">Order Total</p>
                        <p style="font-size: 20px; font-weight: 700; color: #111827; margin-top: 6px;">
                            {{ store_money($order->total) }}
                        </p>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>Items</h3>
                    </div>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 14px 16px; text-align: left;">Product</th>
                                    <th style="padding: 14px 16px; text-align: left;">Price</th>
                                    <th style="padding: 14px 16px; text-align: left;">Qty</th>
                                    <th style="padding: 14px 16px; text-align: right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 14px 16px;">
                                            <div style="font-weight: 600;">{{ $item->product_name }}</div>
                                            <div style="font-size: 12px; color: #6b7280;">SKU: {{ $item->product_sku }}</div>
                                        </td>
                                        <td style="padding: 14px 16px;">{{ store_money($item->unit_price) }}</td>
                                        <td style="padding: 14px 16px;">{{ $item->quantity }}</td>
                                        <td style="padding: 14px 16px; text-align: right; font-weight: 600;">
                                            {{ store_money($item->total_price) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: #f8fafc;">
                                <tr>
                                    <td colspan="3" style="padding: 12px 16px; text-align: right;">Subtotal</td>
                                    <td style="padding: 12px 16px; text-align: right;">{{ store_money($order->subtotal) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                    <tr>
                                        <td colspan="3" style="padding: 12px 16px; text-align: right; color: #16a34a;">Discount</td>
                                        <td style="padding: 12px 16px; text-align: right; color: #16a34a;">
                                            -{{ store_money($order->discount_amount) }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3" style="padding: 12px 16px; text-align: right;">Shipping</td>
                                    <td style="padding: 12px 16px; text-align: right;">{{ store_money($order->shipping_cost) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="padding: 12px 16px; text-align: right; font-weight: 700;">Total</td>
                                    <td style="padding: 12px 16px; text-align: right; font-weight: 700;">
                                        {{ store_money($order->total) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>Return & Refund (RMA)</h3>
                    </div>
                    <div class="card-body">
                        @if($order->returnRequests->isNotEmpty())
                            <div style="display: grid; gap: 12px;">
                                @foreach($order->returnRequests as $returnRequest)
                                    <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px;">
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <div style="font-weight: 600;">{{ $returnRequest->rma_number }}</div>
                                            <span class="badge badge-{{ $returnRequest->status_badge }}">
                                                {{ $returnRequest->status_label }}
                                            </span>
                                        </div>
                                        <div style="font-size: 14px; color: #374151;">
                                            <strong>Reason:</strong> {{ $returnRequest->reason }}
                                        </div>
                                        @if($returnRequest->details)
                                            <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">
                                                {{ $returnRequest->details }}
                                            </div>
                                        @endif
                                        <div style="font-size: 13px; color: #6b7280; margin-top: 6px;">
                                            Requested Refund: {{ store_money($returnRequest->requested_refund_amount) }}
                                            @if($returnRequest->approved_refund_amount)
                                                | Approved: {{ store_money($returnRequest->approved_refund_amount) }}
                                            @endif
                                        </div>
                                        <div style="margin-top: 10px;">
                                            <a href="{{ route('account.returns.show', $returnRequest) }}"
                                                style="font-size: 13px; color: #4f46e5; font-weight: 600;">
                                                View Return Timeline
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p style="color: #6b7280; margin-bottom: 12px;">No return request submitted for this order.</p>
                        @endif

                        @if($canRequestReturn)
                            <div style="margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 16px;">
                                <h4 style="margin-bottom: 12px;">Create Return Request</h4>
                                <form action="{{ route('account.orders.returns.store', $order->order_number) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>Reason</label>
                                        <input type="text" name="reason" class="form-control"
                                            placeholder="Damaged item / wrong product / quality issue" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Details</label>
                                        <textarea name="details" class="form-control" rows="3"
                                            placeholder="Write clear issue details for support team"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Requested Refund Amount (optional)</label>
                                        <input type="number" name="requested_refund_amount" class="form-control"
                                            min="0.01" max="{{ $order->total }}" step="0.01"
                                            placeholder="{{ number_format($order->total, 2) }}">
                                        <small style="color: #6b7280;">Leave empty to request full amount.</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        Submit Return Request
                                    </button>
                                </form>
                            </div>
                        @elseif($order->hasActiveReturnRequest())
                            <p style="margin-top: 14px; color: #d97706;">An active return request already exists for this order.</p>
                        @else
                            <p style="margin-top: 14px; color: #6b7280;">
                                Return request is available only after the order reaches Delivered status.
                            </p>
                        @endif
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
                                        style="position: absolute; left: -9px; top: 0; width: 16px; height: 16px; background: {{ $loop->first ? '#4f46e5' : '#cbd5e1' }}; border-radius: 50%; border: 3px solid white;">
                                    </div>
                                    <div style="font-weight: 600;">
                                        {{ $history->new_status_label }}
                                        @if($history->old_status_label)
                                            <span style="color: #6b7280; font-weight: 400;">(from {{ $history->old_status_label }})</span>
                                        @endif
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280;">{{ $history->created_at->format('M d, Y h:i A') }}
                                    </div>
                                    @if($history->comment)
                                        <div style="font-size: 13px; color: #475569; margin-top: 4px;">{{ $history->comment }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection








