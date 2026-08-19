@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Review Moderation</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Reviews</span>
            </div>
        </div>
    </div>

    <div class="stats-grid" style="grid-template-columns: repeat(5, 1fr); margin-bottom: 24px;">
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['total'] }}</div>
            <div class="label">Total Reviews</div>
        </div>
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['pending'] }}</div>
            <div class="label">Pending Approval</div>
        </div>
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['approved'] }}</div>
            <div class="label">Approved</div>
        </div>
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['verified'] }}</div>
            <div class="label">Verified Purchase</div>
        </div>
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['replied'] }}</div>
            <div class="label">Admin Replied</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form action="{{ route('admin.reviews.index') }}" method="GET"
                style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
                @if(request()->filled('product'))
                    <input type="hidden" name="product" value="{{ request('product') }}">
                @endif
                <div style="flex: 1; min-width: 240px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Product / Customer / Comment"
                        value="{{ request('search') }}">
                </div>
                <div style="width: 180px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div style="width: 130px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Rating</label>
                    <select name="rating" class="form-control">
                        <option value="">All</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ (string) request('rating') === (string) $i ? 'selected' : '' }}>
                                {{ $i }} Star
                            </option>
                        @endfor
                    </select>
                </div>
                <div style="width: 160px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Verified</label>
                    <select name="verified" class="form-control">
                        <option value="">All</option>
                        <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Verified</option>
                        <option value="no" {{ request('verified') === 'no' ? 'selected' : '' }}>Not Verified</option>
                    </select>
                </div>
                <div style="width: 150px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Replied</label>
                    <select name="replied" class="form-control">
                        <option value="">All</option>
                        <option value="yes" {{ request('replied') === 'yes' ? 'selected' : '' }}>Replied</option>
                        <option value="no" {{ request('replied') === 'no' ? 'selected' : '' }}>No Reply</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Review</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Flags</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td style="min-width: 280px;">
                                <div style="font-weight: 600;">{{ $review->title ?: 'Untitled review' }}</div>
                                <div style="font-size: 13px; color: #64748b;">
                                    @for($star = 1; $star <= 5; $star++)
                                        <span style="color: {{ $star <= (int) $review->rating ? '#f59e0b' : '#cbd5e1' }};">&#9733;</span>
                                    @endfor
                                    <span style="margin-left: 6px;">({{ (int) $review->rating }}/5)</span>
                                </div>
                                <div style="font-size: 13px; color: #64748b; margin-top: 6px;">
                                    {{ \Illuminate\Support\Str::limit((string) $review->comment, 90) }}
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.products.show', $review->product) }}" style="color: var(--primary); font-weight: 600;">
                                    {{ $review->product->name }}
                                </a>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $review->user->name }}</div>
                                <div style="font-size: 12px; color: #64748b;">{{ $review->user->email }}</div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $review->is_verified_purchase ? 'success' : 'secondary' }}">
                                    {{ $review->is_verified_purchase ? 'Verified' : 'Unverified' }}
                                </span>
                                @if(!empty($review->admin_reply))
                                    <span class="badge badge-info" style="margin-top: 6px;">Replied</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $review->is_approved ? 'success' : 'warning' }}">
                                    {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </td>
                            <td style="font-size: 13px; color: #64748b;">{{ $review->updated_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-sm btn-outline" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @can('approve reviews')
                                        <a href="{{ route('admin.reviews.show', $review) }}#reply-form"
                                            class="btn btn-sm btn-outline"
                                            title="Reply">
                                            <i class="fas fa-reply"></i>
                                        </a>
                                    @endcan

                                    @can('approve reviews')
                                        @if(!$review->is_approved)
                                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-warning" title="Reject">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #64748b; padding: 40px;">
                                No reviews found for selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $reviews->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

