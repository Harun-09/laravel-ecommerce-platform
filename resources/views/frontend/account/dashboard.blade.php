@extends('layouts.app')

@section('content')
    <div class="container section">
        <div class="account-layout-grid">
            <!-- Sidebar -->
            @include('frontend.account.partials.sidebar', ['user' => $user])

            <!-- Main Content -->
            <div>
                <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 24px;">My Account</h1>

                <!-- Stats -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
                    <div class="card" style="padding: 24px; text-align: center;">
                        <div
                            style="width: 50px; height: 50px; background: #dbeafe; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #2563eb; font-size: 20px;">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <p style="font-size: 28px; font-weight: 700;">{{ $recentOrders->count() }}</p>
                        <p style="color: #6b7280; font-size: 14px;">Total Orders</p>
                    </div>

                    <div class="card" style="padding: 24px; text-align: center;">
                        <div
                            style="width: 50px; height: 50px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #dc2626; font-size: 20px;">
                            <i class="fas fa-heart"></i>
                        </div>
                        <p style="font-size: 28px; font-weight: 700;">{{ $wishlistCount }}</p>
                        <p style="color: #6b7280; font-size: 14px;">Wishlist Items</p>
                    </div>

                    <div class="card" style="padding: 24px; text-align: center;">
                        <div
                            style="width: 50px; height: 50px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #16a34a; font-size: 20px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <p style="font-size: 28px; font-weight: 700;">{{ $addressCount }}</p>
                        <p style="color: #6b7280; font-size: 14px;">Saved Addresses</p>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card">
                    <div
                        style="padding: 20px 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-weight: 600;">Recent Orders</h3>
                        <a href="{{ route('account.orders') }}"
                            style="color: #6366f1; font-weight: 500; font-size: 14px;">View All</a>
                    </div>

                    @if($recentOrders->isEmpty())
                        <div style="padding: 60px 24px; text-align: center;">
                            <i class="fas fa-shopping-bag" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                            <p style="color: #6b7280;">No orders yet</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary" style="margin-top: 16px;">Start
                                Shopping</a>
                        </div>
                    @else
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <th
                                            style="padding: 14px 16px; text-align: left; font-size: 13px; font-weight: 600; color: #6b7280;">
                                            Order</th>
                                        <th
                                            style="padding: 14px 16px; text-align: left; font-size: 13px; font-weight: 600; color: #6b7280;">
                                            Date</th>
                                        <th
                                            style="padding: 14px 16px; text-align: left; font-size: 13px; font-weight: 600; color: #6b7280;">
                                            Status</th>
                                        <th
                                            style="padding: 14px 16px; text-align: right; font-size: 13px; font-weight: 600; color: #6b7280;">
                                            Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr style="border-bottom: 1px solid #f3f4f6;">
                                            <td style="padding: 14px 16px;">
                                                <a href="{{ route('account.orders.detail', $order->order_number) }}"
                                                    style="color: #6366f1; font-weight: 500;">#{{ $order->order_number }}</a>
                                            </td>
                                            <td style="padding: 14px 16px; color: #6b7280; font-size: 14px;">
                                                {{ $order->created_at->format('M d, Y') }}</td>
                                            <td style="padding: 14px 16px;">
                                                @php
                                                    $statusClass = $order->status_badge;
                                                    $bgColors = ['warning' => '#fef3c7', 'info' => '#dbeafe', 'success' => '#dcfce7', 'danger' => '#fee2e2', 'secondary' => '#f3f4f6'];
                                                    $textColors = ['warning' => '#d97706', 'info' => '#2563eb', 'success' => '#16a34a', 'danger' => '#dc2626', 'secondary' => '#6b7280'];
                                                @endphp
                                                <span
                                                    style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; background: {{ $bgColors[$statusClass] }}; color: {{ $textColors[$statusClass] }};">
                                                    {{ $order->status_label }}
                                                </span>
                                            </td>
                                            <td style="padding: 14px 16px; text-align: right; font-weight: 600;">
                                                {{ store_money($order->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
