@extends('layouts.app')

@section('content')
    <div class="container section">
        <div class="account-layout-grid">
            @include('frontend.account.partials.sidebar', ['user' => auth()->user()])

            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h1 style="font-size: 24px; font-weight: 700;">My Orders</h1>
                </div>

                <div class="card" style="margin-bottom: 18px;">
                    <div class="card-body" style="padding: 16px;">
                        <form method="GET" action="{{ route('account.orders') }}"
                            style="display: grid; grid-template-columns: 1fr 220px auto; gap: 12px;">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by order number..." value="{{ request('search') }}">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                @foreach(\App\Models\Order::lifecycleOrder() as $status)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                        {{ \App\Models\Order::statusLabel($status) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    @if($orders->isEmpty())
                        <div style="padding: 50px 24px; text-align: center;">
                            <i class="fas fa-shopping-bag" style="font-size: 42px; color: #d1d5db; margin-bottom: 12px;"></i>
                            <p style="color: #6b7280;">You have no orders yet.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary" style="margin-top: 12px;">Start
                                Shopping</a>
                        </div>
                    @else
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 14px 16px; text-align: left;">Order</th>
                                        <th style="padding: 14px 16px; text-align: left;">Date</th>
                                        <th style="padding: 14px 16px; text-align: left;">Status</th>
                                        <th style="padding: 14px 16px; text-align: left;">Payment</th>
                                        <th style="padding: 14px 16px; text-align: left;">Return</th>
                                        <th style="padding: 14px 16px; text-align: right;">Total</th>
                                        <th style="padding: 14px 16px; text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr style="border-bottom: 1px solid #f3f4f6;">
                                            <td style="padding: 14px 16px;">
                                                <a href="{{ route('account.orders.detail', $order->order_number) }}"
                                                    style="font-weight: 600; color: #4f46e5;">
                                                    #{{ $order->order_number }}
                                                </a>
                                                <div style="font-size: 12px; color: #6b7280;">
                                                    {{ $order->vendor->shop_name ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td style="padding: 14px 16px; color: #6b7280;">{{ $order->created_at->format('M d, Y') }}
                                            </td>
                                            <td style="padding: 14px 16px;">
                                                <span class="badge badge-{{ $order->status_badge }}">{{ $order->status_label }}</span>
                                            </td>
                                            <td style="padding: 14px 16px;">
                                                <span class="badge badge-{{ $order->payment_status_badge }}">
                                                    {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                                </span>
                                            </td>
                                            <td style="padding: 14px 16px;">
                                                @php $latestReturn = $order->returnRequests->sortByDesc('created_at')->first(); @endphp
                                                @if($latestReturn)
                                                    <span class="badge badge-{{ $latestReturn->status_badge }}">
                                                        {{ $latestReturn->status_label }}
                                                    </span>
                                                @else
                                                    <span style="font-size: 12px; color: #6b7280;">No return</span>
                                                @endif
                                            </td>
                                            <td style="padding: 14px 16px; text-align: right; font-weight: 600;">
                                                {{ store_money($order->total) }}
                                            </td>
                                            <td style="padding: 14px 16px; text-align: right;">
                                                <a href="{{ route('account.orders.detail', $order->order_number) }}"
                                                    class="btn btn-outline" style="padding: 8px 12px;">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($orders->hasPages())
                            <div style="padding: 16px 20px; border-top: 1px solid #e5e7eb;">
                                {{ $orders->withQueryString()->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

