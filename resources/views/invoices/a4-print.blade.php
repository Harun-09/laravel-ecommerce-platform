<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $order->invoice_number }} A4</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 18px;
            background: #eef2ff;
            color: #111827;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        .actions {
            max-width: 980px;
            margin: 0 auto 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #4f46e5;
            color: #fff;
        }

        .btn-outline {
            background: #fff;
            color: #1f2937;
            border: 1px solid #cbd5e1;
        }

        .sheet {
            max-width: 980px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 26px 28px;
        }

        .warning {
            margin-bottom: 14px;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #facc15;
            background: #fef9c3;
            color: #854d0e;
            font-size: 13px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .muted {
            color: #6b7280;
            margin: 2px 0;
            font-size: 13px;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            gap: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .summary td {
            border-left: none;
            border-right: none;
            border-top: none;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .actions {
                display: none;
            }

            .sheet {
                border: none;
                border-radius: 0;
                max-width: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button class="btn btn-primary" type="button" onclick="window.print()">Print A4</button>
        <a class="btn btn-outline" href="{{ request()->fullUrlWithQuery(['format' => 'pdf']) }}" target="_blank">
            Open PDF
        </a>
        <button class="btn btn-outline" type="button" onclick="history.back()">Back</button>
    </div>

    <div class="sheet">
        @if(!empty($warning))
            <div class="warning">{{ $warning }}</div>
        @endif

        <div class="header">
            <div>
                <h2>NovaMart</h2>
                <p class="muted">E-Commerce Marketplace</p>
                <p class="muted">Dhaka, Bangladesh</p>
            </div>
            <div class="text-right">
                <h1>INVOICE</h1>
                <p><strong>{{ $order->invoice_number }}</strong></p>
                <p class="muted">Order: #{{ $order->order_number }}</p>
                <p class="muted">Date: {{ $order->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <div class="card">
            <div class="meta">
                <div>
                    <h3 style="margin-bottom: 6px;">Bill To</h3>
                    <p><strong>{{ $order->shipping_name }}</strong></p>
                    <p>{{ $order->shipping_phone }}</p>
                    @if($order->shipping_email)
                        <p>{{ $order->shipping_email }}</p>
                    @endif
                    <p>{{ $order->shipping_full_address }}</p>
                </div>
                <div class="text-right">
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

        <table class="summary" style="margin-top: 12px;">
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

        <p class="muted" style="margin-top: 14px;">
            Generated at {{ now()->format('Y-m-d H:i:s') }} ({{ strtoupper($issuedFor ?? 'system') }} view).
        </p>
    </div>

    @if(!empty($printOnLoad))
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () {
                    window.print();
                }, 250);
            });
        </script>
    @endif
</body>
</html>
