@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Vendor Dashboard</h1>
            <div class="breadcrumb">
                <span>Welcome back, {{ $vendor->shop_name }}</span>
            </div>
        </div>
        <div>
            <a href="{{ route('vendor.orders.index') }}" class="btn btn-primary" style="margin-right: 8px;">
                <i class="fas fa-shopping-bag"></i> Manage Orders
            </a>
            <a href="{{ route('vendor.reports.index') }}" class="btn btn-outline">
                <i class="fas fa-chart-line"></i> View Reports
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon blue"><i class="fas fa-box"></i></div>
            <div class="value">{{ $stats['total_products'] }}</div>
            <div class="label">Total Products</div>
            <div class="change" style="background: #dbeafe; color: #2563eb;">{{ $stats['active_products'] }} active</div>
        </div>

        <div class="stat-card">
            <div class="icon green"><i class="fas fa-shopping-bag"></i></div>
            <div class="value">{{ $stats['total_orders'] }}</div>
            <div class="label">Total Orders</div>
            @if($stats['pending_orders'] > 0)
                <div class="change" style="background: #fef3c7; color: #d97706;">{{ $stats['pending_orders'] }} pending</div>
            @endif
        </div>

        <div class="stat-card">
            <div class="icon orange"><i class="fas fa-dollar-sign"></i></div>
            <div class="value">৳{{ number_format($stats['total_sales']) }}</div>
            <div class="label">Total Sales</div>
        </div>

        <div class="stat-card">
            <div class="icon purple"><i class="fas fa-wallet"></i></div>
            <div class="value">৳{{ number_format($stats['pending_payout']) }}</div>
            <div class="label">Pending Payout</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Orders</h3>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td style="font-weight: 500;">
                                    <a href="{{ route('vendor.orders.show', $order) }}" style="color: var(--primary);">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td>{{ $order->user->name ?? 'Guest' }}</td>
                                <td style="font-weight: 500;">৳{{ number_format($order->total) }}</td>
                                <td>
                                    <span class="badge badge-{{ $order->status_badge }}">{{ $order->status_label }}</span>
                                </td>
                                <td style="color: #64748b;">{{ $order->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: #64748b; padding: 40px;">No orders yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h3>Account Info</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 16px;">
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 4px;">Shop Name</p>
                    <p style="font-weight: 600;">{{ $vendor->shop_name }}</p>
                </div>
                <div style="margin-bottom: 16px;">
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 4px;">Commission Rate</p>
                    <p style="font-weight: 600;">{{ $vendor->commission_rate }}%</p>
                </div>
                <div style="margin-bottom: 16px;">
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 4px;">Rating</p>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star" style="color: {{ $i <= $vendor->rating ? '#facc15' : '#d1d5db' }};"></i>
                        @endfor
                        <span style="font-weight: 500;">{{ number_format($vendor->rating, 1) }}</span>
                    </div>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 4px;">Status</p>
                    <span class="badge badge-success">{{ ucfirst($vendor->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h3>Payout Ledger (Pending)</h3>
        </div>
        <div class="card-body" style="padding-bottom: 0;">
            <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 18px;">
                <div style="padding: 12px; border-radius: 10px; background: #f8fafc;">
                    <p style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Order Amount</p>
                    <p style="font-size: 18px; font-weight: 700;">BDT {{ number_format($payoutSummary['gross'], 2) }}</p>
                </div>
                <div style="padding: 12px; border-radius: 10px; background: #f8fafc;">
                    <p style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Commission</p>
                    <p style="font-size: 18px; font-weight: 700; color: #dc2626;">BDT {{ number_format($payoutSummary['commission'], 2) }}</p>
                </div>
                <div style="padding: 12px; border-radius: 10px; background: #f8fafc;">
                    <p style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Refund</p>
                    <p style="font-size: 18px; font-weight: 700; color: #d97706;">BDT {{ number_format($payoutSummary['refund'], 2) }}</p>
                </div>
                <div style="padding: 12px; border-radius: 10px; background: #ecfdf5; border: 1px solid #bbf7d0;">
                    <p style="font-size: 12px; color: #166534; margin-bottom: 4px;">Payable</p>
                    <p style="font-size: 18px; font-weight: 800; color: #15803d;">BDT {{ number_format($payoutSummary['payable'], 2) }}</p>
                </div>
            </div>

            <div class="table-wrapper" style="margin: 0 -24px;">
                <table>
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Order Amount</th>
                            <th>Commission</th>
                            <th>Refund</th>
                            <th>Payable</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payoutLedger as $ledger)
                            <tr>
                                <td style="font-weight: 600;">#{{ $ledger->order_number }}</td>
                                <td>BDT {{ number_format($ledger->total, 2) }}</td>
                                <td style="color: #dc2626;">- BDT {{ number_format($ledger->commission_amount, 2) }}</td>
                                <td style="color: #d97706;">- BDT {{ number_format($ledger->refunded_amount ?? 0, 2) }}</td>
                                <td style="font-weight: 700; color: #15803d;">BDT {{ number_format($ledger->payout_payable_amount, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $ledger->payment_status_badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $ledger->payment_status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #64748b; padding: 28px;">
                                    No pending payout ledger entries found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding: 14px 0 18px; color: #64748b; font-size: 13px;">
                Formula: <strong>Payable = Order Amount - Commission - Refund</strong>
            </div>
        </div>
    </div>
@endsection
