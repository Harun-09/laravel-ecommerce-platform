@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $panelTitle }}</h1>
            <div class="breadcrumb">
                <a href="{{ $panel === 'vendor' ? route('vendor.dashboard') : route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>{{ $title }}</span>
            </div>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ $exportRoute }}?{{ http_build_query(array_merge(request()->query(), ['format' => 'csv'])) }}"
                class="btn btn-outline">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ $exportRoute }}?{{ http_build_query(array_merge(request()->query(), ['format' => 'pdf'])) }}"
                class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form action="{{ $indexRoute }}" method="GET" style="display: grid; grid-template-columns: repeat(6, minmax(140px, 1fr)); gap: 12px;">
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Report Type</label>
                    <select name="type" class="form-control">
                        <option value="sales" {{ $type === 'sales' ? 'selected' : '' }}>Sales</option>
                        <option value="stock" {{ $type === 'stock' ? 'selected' : '' }}>Stock</option>
                        <option value="payout" {{ $type === 'payout' ? 'selected' : '' }}>Payout</option>
                    </select>
                </div>
                @if($panel === 'admin')
                    <div>
                        <label style="font-size: 13px; margin-bottom: 4px; display: block;">Vendor</label>
                        <select name="vendor_id" class="form-control">
                            <option value="">All Vendors</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ (string)($filters['vendor_id'] ?? '') === (string)$vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->shop_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ ($filters['status'] ?? '') === 'paid' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ ($filters['status'] ?? '') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ ($filters['status'] ?? '') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ ($filters['status'] ?? '') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="returned" {{ ($filters['status'] ?? '') === 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Payment</label>
                    <select name="payment_status" class="form-control">
                        <option value="">All</option>
                        <option value="pending" {{ ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ ($filters['payment_status'] ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="partially_refunded" {{ ($filters['payment_status'] ?? '') === 'partially_refunded' ? 'selected' : '' }}>Partially Refunded</option>
                        <option value="refunded" {{ ($filters['payment_status'] ?? '') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Stock Filter</label>
                    <select name="stock_filter" class="form-control">
                        <option value="">All</option>
                        <option value="in_stock" {{ ($filters['stock_filter'] ?? '') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ ($filters['stock_filter'] ?? '') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ ($filters['stock_filter'] ?? '') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; margin-bottom: 4px; display: block;">Payout State</label>
                    <select name="payout_state" class="form-control">
                        <option value="">All</option>
                        <option value="pending" {{ ($filters['payout_state'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processed" {{ ($filters['payout_state'] ?? '') === 'processed' ? 'selected' : '' }}>Processed</option>
                    </select>
                </div>
                <div style="display: flex; gap: 8px; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <a href="{{ $indexRoute }}?type={{ $type }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 24px;">
        @foreach($summary as $stat)
            <div class="stat-card" style="padding: 16px 18px;">
                <div class="label">{{ $stat['label'] }}</div>
                <div class="value" style="font-size: 22px; margin-top: 6px;">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <h3>{{ $title }}</h3>
            <span style="font-size: 13px; color: #64748b;">Total Rows: {{ number_format($records->total()) }}</span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        @foreach($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            @foreach($headers as $header)
                                <td>{{ $row[$header] ?? '' }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}" style="text-align: center; padding: 40px; color: #64748b;">
                                No data found for selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $records->links() }}
            </div>
        @endif
    </div>
@endsection
