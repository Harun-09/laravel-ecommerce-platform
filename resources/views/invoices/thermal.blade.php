<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $order->invoice_number }} Thermal</title>
    <style>
        :root {
            --paper-width: 80mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Courier New", monospace;
            background: #f3f4f6;
            margin: 0;
            padding: 16px 0 24px;
            color: #111827;
        }

        .receipt {
            width: var(--paper-width);
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            padding: 10px;
        }

        .center {
            text-align: center;
        }

        .muted {
            color: #4b5563;
            font-size: 11px;
        }

        .line {
            border-top: 1px dashed #6b7280;
            margin: 8px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .items {
            margin: 6px 0;
        }

        .item {
            font-size: 11px;
            margin-bottom: 6px;
        }

        .print-actions {
            width: var(--paper-width);
            margin: 10px auto 0;
            text-align: center;
        }

        .btn {
            border: 1px solid #374151;
            padding: 6px 10px;
            background: #111827;
            color: #fff;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .receipt {
                border: none;
                width: 80mm;
                margin: 0;
                padding: 0;
            }

            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="center">
            <div style="font-weight: 700;">NovaMart</div>
            <div class="muted">THERMAL RECEIPT</div>
            <div class="muted">{{ $order->invoice_number }}</div>
        </div>

        <div class="line"></div>

        <div class="row"><span>Order</span><span>#{{ $order->order_number }}</span></div>
        <div class="row"><span>Date</span><span>{{ $order->created_at->format('Y-m-d H:i') }}</span></div>
        <div class="row"><span>Status</span><span>{{ $order->status_label }}</span></div>
        <div class="row"><span>Payment</span><span>{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span></div>
        <div class="row"><span>Customer</span><span>{{ $order->shipping_name }}</span></div>
        <div class="row"><span>Phone</span><span>{{ $order->shipping_phone }}</span></div>

        <div class="line"></div>

        <div class="items">
            @foreach($order->items as $item)
                <div class="item">
                    <div>{{ $item->product_name }}</div>
                    <div class="row">
                        <span>{{ $item->quantity }} x {{ number_format((float) $item->unit_price, 2) }}</span>
                        <span>{{ number_format((float) $item->total_price, 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="line"></div>

        <div class="row"><span>Subtotal</span><span>{{ number_format((float) $order->subtotal, 2) }}</span></div>
        @if((float) $order->discount_amount > 0)
            <div class="row"><span>Discount</span><span>-{{ number_format((float) $order->discount_amount, 2) }}</span></div>
        @endif
        <div class="row"><span>Shipping</span><span>{{ number_format((float) $order->shipping_cost, 2) }}</span></div>
        @if((float) $order->cod_fee > 0)
            <div class="row"><span>COD Fee</span><span>{{ number_format((float) $order->cod_fee, 2) }}</span></div>
        @endif
        @if((float) ($order->refunded_amount ?? 0) > 0)
            <div class="row"><span>Refund</span><span>-{{ number_format((float) $order->refunded_amount, 2) }}</span></div>
        @endif
        <div class="row" style="font-weight: 700;"><span>Total</span><span>BDT {{ number_format((float) $order->total, 2) }}</span></div>

        <div class="line"></div>
        <div class="center muted">Generated for {{ strtoupper($issuedFor ?? 'SYSTEM') }}</div>
        <div class="center muted">Thank you for shopping</div>
    </div>

    <div class="print-actions">
        <button class="btn" onclick="window.print()">Print Receipt</button>
        <button class="btn" type="button" onclick="history.back()">Back</button>
    </div>

    @if(!empty($printOnLoad))
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () {
                    window.print();
                }, 300);
            });
        </script>
    @endif
</body>
</html>
