<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $order->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            margin: 0;
            padding: 24px;
            font-size: 12px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .header > div {
            display: table-cell;
            vertical-align: top;
        }
        .right {
            text-align: right;
        }
        h1, h2, h3, p {
            margin: 0;
        }
        .muted {
            color: #6b7280;
        }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f9fafb;
            font-weight: 700;
        }
        .text-right {
            text-align: right;
        }
        .summary td {
            border-top: none;
            border-left: none;
            border-right: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h2>NovaMart</h2>
            <p class="muted">E-Commerce Marketplace</p>
            <p class="muted">Dhaka, Bangladesh</p>
        </div>
        <div class="right">
            <h1>INVOICE</h1>
            <p><strong>{{ $order->invoice_number }}</strong></p>
            <p class="muted">Order: #{{ $order->order_number }}</p>
            <p class="muted">Date: {{ $order->created_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="header" style="margin-bottom: 0;">
            <div>
                <h3 style="margin-bottom: 6px;">Bill To</h3>
                <p><strong>{{ $order->shipping_name }}</strong></p>
                <p>{{ $order->shipping_phone }}</p>
                @if($order->shipping_email)
                    <p>{{ $order->shipping_email }}</p>
                @endif
                <p>{{ $order->shipping_full_address }}</p>
            </div>
            <div class="right">
                <h3 style="margin-bottom: 6px;">Vendor</h3>
                <p><strong>{{ $order->vendor->shop_name ?? 'N/A' }}</strong></p>
                <p>{{ $order->vendor->email ?? 'N/A' }}</p>
                <p class="muted">Status: {{ $order->status_label }}</p>
                <p class="muted">Payment: {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>SKU</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->product_sku }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">BDT {{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="text-right">BDT {{ number_format((float) $item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary" style="margin-top: 14px;">
        <tbody>
            <tr>
                <td style="width: 75%; border: none;"></td>
                <td class="text-right">Subtotal</td>
                <td class="text-right" style="width: 180px;">BDT {{ number_format((float) $order->subtotal, 2) }}</td>
            </tr>
            @if((float) $order->discount_amount > 0)
                <tr>
                    <td style="border: none;"></td>
                    <td class="text-right">Discount</td>
                    <td class="text-right">-BDT {{ number_format((float) $order->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td style="border: none;"></td>
                <td class="text-right">Shipping</td>
                <td class="text-right">BDT {{ number_format((float) $order->shipping_cost, 2) }}</td>
            </tr>
            @if((float) $order->cod_fee > 0)
                <tr>
                    <td style="border: none;"></td>
                    <td class="text-right">COD Fee</td>
                    <td class="text-right">BDT {{ number_format((float) $order->cod_fee, 2) }}</td>
                </tr>
            @endif
            @if((float) ($order->refunded_amount ?? 0) > 0)
                <tr>
                    <td style="border: none;"></td>
                    <td class="text-right">Refund</td>
                    <td class="text-right">-BDT {{ number_format((float) $order->refunded_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td style="border: none;"></td>
                <td class="text-right"><strong>Grand Total</strong></td>
                <td class="text-right"><strong>BDT {{ number_format((float) $order->total, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <p class="muted" style="margin-top: 18px;">
        Generated at {{ now()->format('Y-m-d H:i:s') }} ({{ strtoupper($issuedFor ?? 'system') }} view).
    </p>
</body>
</html>
