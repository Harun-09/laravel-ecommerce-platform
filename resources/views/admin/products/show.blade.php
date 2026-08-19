@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Product Details</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('admin.products.index') }}">Products</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>{{ $product->name }}</span>
            </div>
        </div>
        <div style="display: flex; gap: 8px;">
            @can('view reviews')
                <a href="{{ route('admin.reviews.index', ['product' => $product->id]) }}" class="btn btn-outline">
                    <i class="fas fa-star-half-alt"></i> Reviews
                </a>
            @endcan
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="display: grid; grid-template-columns: 120px 1fr; gap: 20px;">
            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                style="width: 120px; height: 120px; border-radius: 12px; object-fit: cover; border: 1px solid #e2e8f0;"
                                    onerror="this.onerror=null;this.src='{{ asset('images') }}/placeholders/no-product-image.svg';">
            <div>
                <h3 style="margin-bottom: 8px;">{{ $product->name }}</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 18px; color: #475569; font-size: 14px;">
                    <span><strong>SKU:</strong> {{ $product->sku }}</span>
                    <span><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</span>
                    <span><strong>Vendor:</strong> {{ $product->vendor->shop_name ?? 'N/A' }}</span>
                    <span><strong>Price:</strong> {{ number_format((float) $product->price, 2) }}</span>
                    <span><strong>Status:</strong> {{ ucfirst($product->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="page-header" style="margin-bottom: 16px;">
        <h1 style="font-size: 20px;">Product Images</h1>
    </div>

    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <a href="{{ route('admin.products.show', $product) }}"
            class="btn {{ $showTrashedImages ? 'btn-outline' : 'btn-primary' }}">
            Active ({{ $activeImagesCount }})
        </a>
        <a href="{{ route('admin.products.show', ['product' => $product, 'trashed_images' => 1]) }}"
            class="btn {{ $showTrashedImages ? 'btn-warning' : 'btn-outline' }}">
            Trash ({{ $trashedImagesCount }})
        </a>
    </div>

    @if(!$showTrashedImages)
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>Add New Image</h3>
            </div>
            <form action="{{ route('admin.products.images.store', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body" style="display: grid; grid-template-columns: 2fr 2fr 1fr 1fr auto; gap: 12px; align-items: end;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="new_image">Image *</label>
                        <input id="new_image" type="file" name="image" class="form-control" accept="image/*" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="new_alt_text">Alt Text</label>
                        <input id="new_alt_text" type="text" name="alt_text" class="form-control" placeholder="SEO text">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="new_order">Order</label>
                        <input id="new_order" type="number" name="order" class="form-control" min="0">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="display: inline-flex; gap: 8px; align-items: center; margin-top: 34px;">
                            <input type="checkbox" name="is_primary" value="1">
                            Primary
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Meta</th>
                        <th>Timestamps</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($images as $image)
                        <tr>
                            <td style="width: 140px;">
                                <img src="{{ $image->image_url }}" alt="{{ $image->alt_text ?? $product->name }}"
                                    style="width: 110px; height: 72px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0;"
                                                        onerror="this.onerror=null;this.src='{{ asset('images') }}/placeholders/no-product-image.svg';">
                            </td>
                            <td style="min-width: 320px;">
                                @if(!$showTrashedImages)
                                    <form action="{{ route('admin.products.images.update', [$product, $image->id]) }}" method="POST"
                                        enctype="multipart/form-data"
                                        style="display: grid; grid-template-columns: 1fr 90px 110px; gap: 10px; align-items: end;">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label style="font-size: 12px; color: #64748b;">Alt Text</label>
                                            <input type="text" name="alt_text" class="form-control"
                                                value="{{ $image->alt_text }}" placeholder="SEO text">
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; color: #64748b;">Order</label>
                                            <input type="number" name="order" class="form-control" min="0"
                                                value="{{ $image->order }}">
                                        </div>
                                        <div style="display: flex; gap: 8px; align-items: center; padding-bottom: 10px;">
                                            <label style="display: inline-flex; gap: 6px; align-items: center;">
                                                <input type="checkbox" name="is_primary" value="1" {{ $image->is_primary ? 'checked' : '' }}>
                                                Primary
                                            </label>
                                        </div>
                                        <div style="grid-column: 1 / span 2;">
                                            <label style="font-size: 12px; color: #64748b;">Replace Image</label>
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                        </div>
                                        <div style="display: flex; justify-content: flex-end;">
                                            <button type="submit" class="btn btn-sm btn-outline">
                                                <i class="fas fa-save"></i> Update
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div style="display: grid; gap: 4px;">
                                        <div><strong>Alt:</strong> {{ $image->alt_text ?: '-' }}</div>
                                        <div><strong>Order:</strong> {{ $image->order }}</div>
                                        <div>
                                            <span class="badge badge-{{ $image->is_primary ? 'success' : 'secondary' }}">
                                                {{ $image->is_primary ? 'Primary' : 'Secondary' }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: #64748b;">
                                <div><strong>Created:</strong> {{ $image->created_at?->format('M d, Y h:i A') ?? '-' }}</div>
                                <div><strong>Updated:</strong> {{ $image->updated_at?->format('M d, Y h:i A') ?? '-' }}</div>
                                <div><strong>Deleted:</strong> {{ $image->deleted_at?->format('M d, Y h:i A') ?? '-' }}</div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    @if(!$showTrashedImages)
                                        <form action="{{ route('admin.products.images.destroy', [$product, $image->id]) }}" method="POST"
                                            onsubmit="return confirm('Move this image to trash?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.products.images.restore', [$product, $image->id]) }}" method="POST"
                                            onsubmit="return confirm('Restore this image?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.products.images.force-destroy', [$product, $image->id]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Permanently delete this image? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b; padding: 40px;">
                                {{ $showTrashedImages ? 'No trashed images found' : 'No images added yet' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($images->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $images->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
