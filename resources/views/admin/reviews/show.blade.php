@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Review #{{ $review->id }}</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('admin.reviews.index') }}">Reviews</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Review #{{ $review->id }}</span>
            </div>
        </div>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Review Content</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                        <div style="font-size: 18px; font-weight: 700;">
                            {{ $review->title ?: 'Untitled review' }}
                        </div>
                        <div style="font-size: 15px;">
                            @for($star = 1; $star <= 5; $star++)
                                <span style="color: {{ $star <= (int) $review->rating ? '#f59e0b' : '#cbd5e1' }};">&#9733;</span>
                            @endfor
                            <span style="margin-left: 6px; color: #64748b;">({{ (int) $review->rating }}/5)</span>
                        </div>
                    </div>

                    <div style="margin-top: 14px; color: #334155; line-height: 1.6;">
                        {{ $review->comment ?: 'No comment provided.' }}
                    </div>

                    @if($review->pros || $review->cons)
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px;">
                            <div style="padding: 12px; border-radius: 8px; background: #f0fdf4;">
                                <div style="font-weight: 700; color: #166534; margin-bottom: 6px;">Pros</div>
                                <div>{{ $review->pros ?: 'N/A' }}</div>
                            </div>
                            <div style="padding: 12px; border-radius: 8px; background: #fef2f2;">
                                <div style="font-weight: 700; color: #b91c1c; margin-bottom: 6px;">Cons</div>
                                <div>{{ $review->cons ?: 'N/A' }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Moderation Actions</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px;">
                        @can('approve reviews')
                            @if(!$review->is_approved)
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Approve Review
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-ban"></i> Reject Review
                                    </button>
                                </form>
                            @endif
                        @endcan

                        @can('delete reviews')
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                onsubmit="return confirm('Delete this review? This action can be restored only from database backups.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete Review
                                </button>
                            </form>
                        @endcan
                    </div>

                    @can('approve reviews')
                        <form id="reply-form" action="{{ route('admin.reviews.reply', $review) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Admin Reply</label>
                                <textarea name="admin_reply" class="form-control" rows="4"
                                    placeholder="Write an official response to this customer review...">{{ old('admin_reply', $review->admin_reply) }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-reply"></i> Save Reply
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

            @if($review->images->isNotEmpty())
                <div class="card">
                    <div class="card-header">
                        <h3>Review Images</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;">
                            @foreach($review->images as $image)
                                <img src="{{ $image->image_url }}" alt="Review image"
                                    style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Status</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Approval</span>
                        <span class="badge badge-{{ $review->is_approved ? 'success' : 'warning' }}">
                            {{ $review->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Verified Purchase</span>
                        <span class="badge badge-{{ $review->is_verified_purchase ? 'success' : 'secondary' }}">
                            {{ $review->is_verified_purchase ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Helpful Votes</span>
                        <span>{{ (int) $review->helpful_count }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Reply Status</span>
                        <span class="badge badge-{{ !empty($review->admin_reply) ? 'info' : 'secondary' }}">
                            {{ !empty($review->admin_reply) ? 'Replied' : 'Not replied' }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Replied At</span>
                        <span>{{ $review->admin_replied_at?->format('M d, Y h:i A') ?? '-' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Created</span>
                        <span>{{ $review->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Updated</span>
                        <span>{{ $review->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Product</h3>
                </div>
                <div class="card-body">
                    <div style="font-weight: 700;">{{ $review->product->name }}</div>
                    <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                        Vendor: {{ $review->product->vendor->shop_name ?? 'N/A' }}
                    </div>
                    <a href="{{ route('admin.products.show', $review->product) }}" class="btn btn-sm btn-outline"
                        style="margin-top: 10px;">
                        <i class="fas fa-box"></i> Open Product
                    </a>
                </div>
            </div>

            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Customer</h3>
                </div>
                <div class="card-body">
                    <div style="font-weight: 700;">{{ $review->user->name }}</div>
                    <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                        {{ $review->user->email }}
                    </div>
                </div>
            </div>

            @if($review->order)
                <div class="card">
                    <div class="card-header">
                        <h3>Order Context</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: #64748b;">Order Number</span>
                            <span>#{{ $review->order->order_number }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: #64748b;">Order Status</span>
                            <span>{{ $review->order->status_label }}</span>
                        </div>
                        <a href="{{ route('admin.orders.show', $review->order) }}" class="btn btn-sm btn-outline">
                            <i class="fas fa-shopping-bag"></i> View Order
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

