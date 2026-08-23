@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Vendors</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Vendors</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form action="{{ route('admin.vendors.index') }}" method="GET"
                style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Shop name or email"
                        value="{{ request('search') }}">
                </div>
                <div style="width: 150px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.vendors.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Vendors Table -->
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>Owner</th>
                        <th>Products</th>
                        <th>Commission</th>
                        <th>Total Sales</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div
                                        style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 18px;">
                                        {{ substr($vendor->company_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p style="font-weight: 600;">{{ $vendor->company_name }}</p>
                                        <p style="font-size: 13px; color: #64748b;">{{ $vendor->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $vendor->user->name ?? 'N/A' }}</td>
                            <td>{{ $vendor->products()->count() }}</td>
                            <td>{{ $'-' }}%</td>
                            <td style="font-weight: 600;">৳{{ number_format($vendor->supplierOrders()->where('status', 'completed')->sum('grand_total')) }}</td>
                            <td>
                                @php
                                    $statusClass = match ($vendor->status) {
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        'suspended' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">{{ ucfirst($vendor->status) }}</span>
                            </td>
                            <td style="color: #64748b; font-size: 13px;">{{ $vendor->created_at->format('M d, Y') }}</td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('admin.vendors.show', $vendor) }}" class="btn btn-sm btn-outline">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($vendor->status === 'pending')
                                        <form action="{{ route('admin.vendors.approve', $vendor) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #64748b; padding: 40px;">No vendors found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $vendors->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
