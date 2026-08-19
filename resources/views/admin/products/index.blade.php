@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Products</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Products</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form action="{{ route('admin.products.index') }}" method="GET"
                style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Product name or SKU"
                        value="{{ request('search') }}">
                </div>
                <div style="width: 150px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Category</label>
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="width: 150px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="{{ $product->listing_image_url }}" alt="{{ $product->name }}"
                                        style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;"
                                        onerror="this.onerror=null;this.src='{{ asset('images') }}/placeholders/no-product-image.svg';">
                                    <div style="max-width: 250px;">
                                        <p
                                            style="font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $product->name }}</p>
                                        <p style="font-size: 12px; color: #64748b;">SKU: {{ $product->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                            <td>{{ $product->vendor->shop_name ?? 'N/A' }}</td>
                            <td>
                                <p style="font-weight: 500;">৳{{ number_format($product->price) }}</p>
                                @if($product->compare_price)
                                    <p style="font-size: 12px; color: #64748b; text-decoration: line-through;">
                                        ৳{{ number_format($product->compare_price) }}</p>
                                @endif
                            </td>
                            <td>
                                @if($product->track_quantity)
                                    <span
                                        class="{{ $product->quantity > 0 ? 'text-success' : 'text-danger' }}">{{ $product->quantity }}</span>
                                @else
                                    <span style="color: #64748b;">∞</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = match ($product->status) {
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">{{ ucfirst($product->status) }}</span>
                                @if($product->featured)
                                    <span class="badge badge-info"><i class="fas fa-star"></i></span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.products.toggle-featured', $product) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm {{ $product->featured ? 'btn-warning' : 'btn-outline' }}"
                                            title="Toggle Featured">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                    @if($product->status === 'pending')
                                        <form action="{{ route('admin.products.approve', $product) }}" method="POST"
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
                            <td colspan="7" style="text-align: center; color: #64748b; padding: 40px;">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $products->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
