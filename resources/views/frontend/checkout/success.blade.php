@extends('layouts.app')

@section('content')
    @php
        $checkoutStatus = (string) data_get($paymentSummary, 'status', 'completed');
        $isCheckoutCompleted = (bool) data_get($paymentSummary, 'is_completed', true);
        $isSplitCheckout = (bool) data_get($paymentSummary, 'is_split', false);
        $retryOrderNumber = data_get($paymentSummary, 'retry_order_number');
        $statusLabel = ucfirst(str_replace('_', ' ', $checkoutStatus));

        $heroConfig = match ($checkoutStatus) {
            'pending' => [
                'icon' => 'fas fa-clock',
                'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                'heading' => 'Order Placed, Payment Pending',
                'subtitle' => 'Your order is created. Checkout will be completed after all related payments are confirmed.',
                'titleColor' => '#b45309',
            ],
            'failed' => [
                'icon' => 'fas fa-exclamation-triangle',
                'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                'heading' => 'Order Placed, Payment Failed',
                'subtitle' => 'One or more split payments failed. Please retry payment to complete checkout.',
                'titleColor' => '#b91c1c',
            ],
            default => [
                'icon' => 'fas fa-check',
                'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)',
                'heading' => 'Order Placed Successfully!',
                'subtitle' => 'Thank you for shopping with NovaMart',
                'titleColor' => '#16a34a',
            ],
        };
    @endphp

    <div class="container section" style="text-align: center; max-width: 600px; margin: 0 auto;">
        <div
            style="width: 100px; height: 100px; background: {{ $heroConfig['gradient'] }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
            <i class="{{ $heroConfig['icon'] }}" style="font-size: 48px; color: white;"></i>
        </div>

        <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 12px; color: {{ $heroConfig['titleColor'] }};">
            {{ $heroConfig['heading'] }}
        </h1>
        <p style="color: #6b7280; font-size: 18px; margin-bottom: 30px;">{{ $heroConfig['subtitle'] }}</p>

        <div class="card" style="padding: 30px; text-align: left; margin-bottom: 24px;">
            <div
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;">
                <div>
                    <p style="color: #6b7280; font-size: 14px;">
                        {{ $isSplitCheckout ? 'Reference Order' : 'Order Number' }}
                    </p>
                    <p style="font-size: 20px; font-weight: 700; color: #6366f1;">{{ $order->order_number }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="color: #6b7280; font-size: 14px;">Order Date</p>
                    <p style="font-weight: 500;">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <h3 style="font-weight: 600; margin-bottom: 16px;">Order Items</h3>
            @foreach($order->items as $item)
                <div style="display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                    <img src="{{ $item->product_image ? asset('storage/' . $item->product_image) : '/placeholder.jpg' }}" alt=""
                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                    <div style="flex: 1;">
                        <p style="font-weight: 500;">{{ $item->product_name }}</p>
                        <p style="font-size: 13px; color: #6b7280;">Qty: {{ $item->quantity }}</p>
                    </div>
                    <p style="font-weight: 600;">{{ store_money($item->total_price) }}</p>
                </div>
            @endforeach

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #6b7280;">Subtotal</span>
                    <span>{{ store_money($order->subtotal) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #6b7280;">Shipping</span>
                    <span>{{ store_money($order->shipping_cost) }}</span>
                </div>
                @if($order->cod_fee > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: #6b7280;">COD Fee</span>
                        <span>{{ store_money($order->cod_fee) }}</span>
                    </div>
                @endif
                @if($order->discount_amount > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #16a34a;">
                        <span>Discount</span>
                        <span>-{{ store_money($order->discount_amount) }}</span>
                    </div>
                @endif
                <div
                    style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px solid #e5e7eb; margin-top: 12px;">
                    <span style="font-weight: 600;">Total</span>
                    <span
                        style="font-size: 20px; font-weight: 700; color: #6366f1;">{{ store_money($order->total) }}</span>
                </div>
            </div>

            <div style="margin-top: 24px; padding: 16px; background: #f3f4f6; border-radius: 8px;">
                <h4 style="font-weight: 600; margin-bottom: 8px;">Shipping Address</h4>
                <p style="color: #4b5563;">
                    {{ $order->shipping_name }}<br>
                    {{ $order->shipping_phone }}<br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}
                    @if($order->delivery_zone)
                        <br><span style="font-size: 13px; color: #6b7280;">Zone: {{ $order->delivery_zone }}</span>
                    @endif
                </p>
            </div>

            <div style="margin-top: 16px; padding: 16px; background: #dbeafe; border-radius: 8px;">
                <p style="color: #1d4ed8;">
                    <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                    @if($isSplitCheckout)
                        Checkout payment status: {{ $statusLabel }}
                    @elseif($order->payment_method === 'cod')
                        Payment will be collected upon delivery.
                    @else
                        Payment status: {{ ucfirst($order->payment_status) }}
                    @endif
                </p>
            </div>

            @if($isSplitCheckout)
                <div style="margin-top: 16px; padding: 16px; background: #f8fafc; border-radius: 8px;">
                    <h4 style="font-weight: 600; margin-bottom: 8px;">Split Checkout Summary</h4>
                    <p style="font-size: 13px; color: #6b7280; margin-bottom: 12px;">
                        {{ data_get($paymentSummary, 'total_orders', 1) }} vendor orders |
                        Paid {{ store_money((float) data_get($paymentSummary, 'paid_amount', 0)) }} of
                        {{ store_money((float) data_get($paymentSummary, 'total_amount', 0)) }}
                    </p>

                    <div style="display: grid; gap: 8px;">
                        @foreach($checkoutOrders as $checkoutOrder)
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; background: white;">
                                <span style="font-size: 13px; font-weight: 600;">
                                    {{ $checkoutOrder->order_number }}
                                </span>
                                <span style="font-size: 12px; color: #475569;">
                                    {{ ucfirst(str_replace('_', ' ', $checkoutOrder->payment_status)) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div style="display: flex; gap: 16px; justify-content: center;">
            @if(!$isCheckoutCompleted && $retryOrderNumber)
                <a href="{{ route('payment.process', $retryOrderNumber) }}" class="btn btn-primary">
                    <i class="fas fa-credit-card"></i> Complete Payment
                </a>
            @endif
            <a href="{{ route('account.orders.detail', $order->order_number) }}" class="btn btn-primary">
                <i class="fas fa-eye"></i> View Order
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline">
                Continue Shopping
            </a>
        </div>
    </div>
@endsection







