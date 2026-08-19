@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>User Profile</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Home</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('admin.users.index') }}">Users</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>{{ $user->name }}</span>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- User Profile Header --}}
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 30px;">
            <div style="display: flex; align-items: center; gap: 24px;">
                <div style="position: relative;">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                        style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary); box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);">
                    <span style="position: absolute; bottom: 4px; right: 4px; width: 16px; height: 16px; border-radius: 50%; border: 3px solid #fff;
                        background: {{ $user->status === 'active' ? '#22c55e' : ($user->status === 'banned' ? '#ef4444' : '#eab308') }};"></span>
                </div>
                <div style="flex: 1;">
                    <h2 style="margin: 0 0 4px 0; font-size: 22px; font-weight: 700;">{{ $user->name }}</h2>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        @foreach($user->roles as $role)
                            <span class="badge badge-info" style="font-size: 12px; padding: 4px 12px; border-radius: 20px; text-transform: capitalize;">
                                {{ $role->name }}
                            </span>
                        @endforeach
                        <span class="badge badge-{{ $user->status === 'active' ? 'success' : ($user->status === 'banned' ? 'danger' : 'warning') }}"
                            style="font-size: 12px; padding: 4px 12px; border-radius: 20px; text-transform: capitalize;">
                            {{ $user->status }}
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 20px; color: #64748b; font-size: 14px;">
                        <span><i class="fas fa-envelope" style="margin-right: 6px; color: var(--primary);"></i>{{ $user->email }}</span>
                        @if($user->phone)
                            <span><i class="fas fa-phone" style="margin-right: 6px; color: var(--primary);"></i>{{ $user->phone }}</span>
                        @endif
                        <span><i class="fas fa-calendar" style="margin-right: 6px; color: var(--primary);"></i>Joined {{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid" style="margin-bottom: 24px;">
        <div class="stat-card">
            <div class="icon blue"><i class="fas fa-shopping-bag"></i></div>
            <div class="value">{{ number_format($orderStats['total']) }}</div>
            <div class="label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="icon green"><i class="fas fa-dollar-sign"></i></div>
            <div class="value">৳{{ number_format($orderStats['total_spent']) }}</div>
            <div class="label">Total Spent</div>
        </div>
        <div class="stat-card">
            <div class="icon orange"><i class="fas fa-truck"></i></div>
            <div class="value">{{ number_format($orderStats['delivered']) }}</div>
            <div class="label">Delivered</div>
        </div>
        <div class="stat-card">
            <div class="icon purple"><i class="fas fa-star"></i></div>
            <div class="value">{{ $user->reviews()->count() }}</div>
            <div class="label">Reviews</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        {{-- Recent Orders --}}
        <div class="card">
            <div class="card-header">
                <h3>Recent Orders</h3>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Vendor</th>
                            <th>Total</th>
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
                                <td>{{ $order->vendor->shop_name ?? 'N/A' }}</td>
                                <td style="font-weight: 500;">৳{{ number_format($order->total) }}</td>
                                <td>
                                    <span class="badge badge-{{ $order->status_badge }}">{{ $order->status_label }}</span>
                                </td>
                                <td style="color: #64748b;">{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: #64748b; padding: 40px;">
                                    <i class="fas fa-shopping-cart" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
                                    No orders yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right Column --}}
        <div>
            {{-- Contact Info --}}
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Contact Information</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(99, 102, 241, 0.1); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-envelope" style="color: var(--primary);"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #94a3b8;">Email</div>
                                <div style="font-weight: 500;">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(34, 197, 94, 0.1); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-phone" style="color: #22c55e;"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #94a3b8;">Phone</div>
                                <div style="font-weight: 500;">{{ $user->phone ?? 'Not provided' }}</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(234, 179, 8, 0.1); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-shield-alt" style="color: #eab308;"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #94a3b8;">Email Verified</div>
                                <div style="font-weight: 500;">
                                    @if($user->email_verified_at)
                                        <span style="color: #22c55e;"><i class="fas fa-check-circle"></i> {{ $user->email_verified_at->format('M d, Y') }}</span>
                                    @else
                                        <span style="color: #ef4444;"><i class="fas fa-times-circle"></i> Not verified</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-clock" style="color: #8b5cf6;"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #94a3b8;">Last Updated</div>
                                <div style="font-weight: 500;">{{ $user->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Addresses --}}
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Addresses</h3>
                </div>
                <div class="card-body">
                    @forelse($user->addresses as $address)
                        <div style="padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 10px; {{ $address->is_default ? 'border-color: var(--primary); background: rgba(99,102,241,0.04);' : '' }}">
                            @if($address->is_default)
                                <span style="font-size: 11px; background: var(--primary); color: #fff; padding: 2px 8px; border-radius: 10px; margin-bottom: 6px; display: inline-block;">Default</span>
                            @endif
                            <div style="font-weight: 500; margin-bottom: 2px;">{{ $address->name ?? $user->name }}</div>
                            <div style="font-size: 13px; color: #64748b; line-height: 1.5;">
                                {{ $address->address_line_1 ?? '' }}
                                @if($address->address_line_2), {{ $address->address_line_2 }}@endif
                                <br>
                                {{ $address->city ?? '' }}{{ $address->state ? ', '.$address->state : '' }} {{ $address->zip_code ?? '' }}
                                <br>
                                {{ $address->country ?? '' }}
                            </div>
                            @if($address->phone)
                                <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                                    <i class="fas fa-phone" style="font-size: 11px;"></i> {{ $address->phone }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <p style="text-align: center; color: #94a3b8; padding: 20px;">
                            <i class="fas fa-map-marker-alt" style="font-size: 20px; display: block; margin-bottom: 6px;"></i>
                            No addresses saved
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Reviews --}}
            <div class="card">
                <div class="card-header">
                    <h3>Recent Reviews</h3>
                </div>
                <div class="card-body">
                    @forelse($recentReviews as $review)
                        <div style="padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                <div style="color: #eab308; font-size: 13px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= $review->rating ? '' : '-half-alt' }}" style="color: {{ $i <= $review->rating ? '#eab308' : '#d1d5db' }};"></i>
                                    @endfor
                                </div>
                                <span style="font-size: 12px; color: #94a3b8;">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <p style="font-size: 13px; font-weight: 500; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $review->product->name ?? 'Deleted Product' }}
                            </p>
                            @if($review->comment)
                                <p style="font-size: 13px; color: #64748b; line-height: 1.4; margin: 0;">
                                    {{ Str::limit($review->comment, 80) }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p style="text-align: center; color: #94a3b8; padding: 20px;">
                            <i class="fas fa-star" style="font-size: 20px; display: block; margin-bottom: 6px;"></i>
                            No reviews yet
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
