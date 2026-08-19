@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Banners</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Banners</span>
            </div>
        </div>
        @if(!$showTrashed)
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Banner
            </a>
        @endif
    </div>

    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <a href="{{ route('admin.banners.index') }}" class="btn {{ $showTrashed ? 'btn-outline' : 'btn-primary' }}">
            Active ({{ $activeCount }})
        </a>
        <a href="{{ route('admin.banners.index', ['trashed' => 1]) }}"
            class="btn {{ $showTrashed ? 'btn-warning' : 'btn-outline' }}">
            Trash ({{ $trashedCount }})
        </a>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form action="{{ route('admin.banners.index') }}" method="GET"
                style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
                @if($showTrashed)
                    <input type="hidden" name="trashed" value="1">
                @endif

                <div style="flex: 1; min-width: 220px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Title or subtitle"
                        value="{{ request('search') }}">
                </div>

                <div style="width: 220px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Position</label>
                    <select name="position" class="form-control">
                        <option value="">All Positions</option>
                        @foreach($positions as $value => $label)
                            <option value="{{ $value }}" {{ request('position') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.banners.index', $showTrashed ? ['trashed' => 1] : []) }}"
                        class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Banner</th>
                        <th>Position</th>
                        <th>Order</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                        style="width: 64px; height: 40px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0;"
                                        onerror="this.onerror=null;this.src='{{ asset('images/placeholders/no-banner-image.svg') }}';">
                                    <div style="max-width: 280px;">
                                        <div style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $banner->title }}
                                        </div>
                                        @if($banner->subtitle)
                                            <div style="font-size: 12px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $banner->subtitle }}
                                            </div>
                                        @endif
                                        <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                            Mobile: {{ $banner->mobile_image ? 'Uploaded' : 'Not set' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ ucfirst($banner->position) }}</td>
                            <td>{{ $banner->order }}</td>
                            <td style="font-size: 12px; color: #64748b;">
                                @if($banner->starts_at || $banner->ends_at)
                                    {{ $banner->starts_at ? $banner->starts_at->format('M d, Y h:i A') : 'Anytime' }}
                                    <br>
                                    to
                                    <br>
                                    {{ $banner->ends_at ? $banner->ends_at->format('M d, Y h:i A') : 'No end' }}
                                @else
                                    Always
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $banner->is_active ? 'success' : 'secondary' }}">
                                    {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="color: #64748b; font-size: 13px;">
                                {{ $banner->deleted_at ? $banner->deleted_at->format('M d, Y h:i A') : '-' }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    @if(!$showTrashed)
                                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST"
                                            onsubmit="return confirm('Move this banner to trash?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.banners.restore', $banner->id) }}" method="POST"
                                            onsubmit="return confirm('Restore this banner?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.banners.force-destroy', $banner->id) }}" method="POST"
                                            onsubmit="return confirm('Permanently delete this banner? This cannot be undone.')">
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
                            <td colspan="7" style="text-align: center; color: #64748b; padding: 40px;">
                                {{ $showTrashed ? 'Trash is empty' : 'No banners found' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($banners->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $banners->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
