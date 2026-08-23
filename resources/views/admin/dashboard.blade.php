@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Home</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Dashboard</span>
            </div>
        </div>
        <div>
            <span style="color: #64748b;">{{ now()->format('l, F d, Y') }}</span>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon blue"><i class="fas fa-shopping-bag"></i></div>
            <div class="value">{{ number_format($stats['total_orders']) }}</div>
            <div class="label">Total Orders</div>
            <div class="change up"><i class="fas fa-arrow-up"></i> {{ $stats['today_orders'] }} today</div>
        </div>

        <div class="stat-card">
            <div class="icon green"><i class="fas fa-dollar-sign"></i></div>
            <div class="value">৳{{ number_format($stats['total_revenue']) }}</div>
            <div class="label">Total Revenue</div>
            <div class="change up"><i class="fas fa-arrow-up"></i> ৳{{ number_format($stats['today_revenue']) }} today</div>
        </div>

        <div class="stat-card">
            <div class="icon orange"><i class="fas fa-box"></i></div>
            <div class="value">{{ number_format($stats['total_products']) }}</div>
            <div class="label">Active Products</div>
        </div>

        <div class="stat-card">
            <div class="icon purple"><i class="fas fa-store"></i></div>
            <div class="value">{{ number_format($stats['total_vendors']) }}</div>
            <div class="label">Active Vendors</div>
            @if($stats['pending_vendors'] > 0)
                <div class="change" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-clock"></i> {{ $stats['pending_vendors'] }} pending
                </div>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline">View All</a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Home</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Dashboard</span>
            </div>
        </div>
        <div>
            <span style="color: #64748b;">{{ now()->format('l, F d, Y') }}</span>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon blue"><i class="fas fa-shopping-bag"></i></div>
            <div class="value">{{ number_format($stats['total_orders']) }}</div>
            <div class="label">Total Orders</div>
            <div class="change up"><i class="fas fa-arrow-up"></i> {{ $stats['today_orders'] }} today</div>
        </div>

        <div class="stat-card">
            <div class="icon green"><i class="fas fa-dollar-sign"></i></div>
            <div class="value">৳{{ number_format($stats['total_revenue']) }}</div>
            <div class="label">Total Revenue</div>
            <div class="change up"><i class="fas fa-arrow-up"></i> ৳{{ number_format($stats['today_revenue']) }} today</div>
        </div>

        <div class="stat-card">
            <div class="icon orange"><i class="fas fa-box"></i></div>
            <div class="value">{{ number_format($stats['total_products']) }}</div>
            <div class="label">Active Products</div>
        </div>

        <div class="stat-card">
            <div class="icon purple"><i class="fas fa-store"></i></div>
            <div class="value">{{ number_format($stats['total_vendors']) }}</div>
            <div class="label">Active Vendors</div>
            @if($stats['pending_vendors'] > 0)
                <div class="change" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-clock"></i> {{ $stats['pending_vendors'] }} pending
                </div>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline">View All</a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Supplier</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        style="color: var(--primary); font-weight: 500;">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td>
                                    @if($order->user)
                                        <a href="{{ route('admin.users.show', $order->user) }}"
                                           style="color: var(--primary); font-weight: 500; text-decoration: none;"
                                           onmouseover="this.style.textDecoration='underline'"
                                           onmouseout="this.style.textDecoration='none'">
                                            {{ $order->user->name }}
                                        </a>
                                    @else
                                        <span style="color: #94a3b8;">Guest</span>
                                    @endif
                                </td>
                                <td>{{ $order->supplierOrders->first()?->supplier?->company_name ?? 'N/A' }}</td>
                                <td style="font-weight: 500;">৳{{ number_format($order->grand_total) }}</td>
                                <td>
                                    @php
                                        $statusVal = $order->status?->value ?? $order->status;
                                        $badge = match($statusVal) { 'pending' => 'warning', 'paid' => 'info', 'processing' => 'info', 'shipped' => 'info', 'delivered' => 'success', 'cancelled' => 'danger', 'returned' => 'secondary', default => 'secondary' };
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">{{ ucfirst($statusVal) }}</span>
                                </td>
                                <td style="color: #64748b;">{{ $order->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #64748b; padding: 40px;">No orders yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Stats -->
        <div>
            <!-- Order Stats -->
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Order Status</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="display: flex; align-items: center; gap: 8px;">
                                <span style="width: 10px; height: 10px; background: #eab308; border-radius: 50%;"></span>
                                Pending
                            </span>
                            <span style="font-weight: 600;">{{ $stats['pending_orders'] }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="display: flex; align-items: center; gap: 8px;">
                                <span style="width: 10px; height: 10px; background: #22c55e; border-radius: 50%;"></span>
                                Delivered
                            </span>
                            <span style="font-weight: 600;">{{ \App\Domains\ECommerce\Models\Order::where('status', 'delivered')->count() }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="display: flex; align-items: center; gap: 8px;">
                                <span style="width: 10px; height: 10px; background: #ef4444; border-radius: 50%;"></span>
                                Canceled
                            </span>
                <div class="card-header">
                    <h3>Top Products</h3>
                </div>
                <div class="card-body">
                    @forelse($topProducts as $product)
                        <div
                            style="display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                            <img src="{{ $product->primary_image_url }}" alt=""
                                style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover;">
                            <div style="flex: 1; min-width: 0;">
                                <p style="font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $product->name }}</p>
                                <p style="font-size: 13px; color: #64748b;">{{ $product->order_items_count }} sold</p>
                            </div>
                        </div>
                    @empty
                        <p style="text-align: center; color: #64748b; padding: 20px;">No data yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
