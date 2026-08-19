@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Categories</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Categories</span>
            </div>
        </div>
        @if(!$showTrashed)
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Category
            </a>
        @endif
    </div>

    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <a href="{{ route('admin.categories.index') }}"
            class="btn {{ $showTrashed ? 'btn-outline' : 'btn-primary' }}">
            Active ({{ $activeCount }})
        </a>
        <a href="{{ route('admin.categories.index', ['trashed' => 1]) }}"
            class="btn {{ $showTrashed ? 'btn-warning' : 'btn-outline' }}">
            Trash ({{ $trashedCount }})
        </a>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form action="{{ route('admin.categories.index') }}" method="GET"
                style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
                @if($showTrashed)
                    <input type="hidden" name="trashed" value="1">
                @endif

                <div style="flex: 1; min-width: 220px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Category name"
                        value="{{ request('search') }}">
                </div>

                <div style="width: 220px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Parent</label>
                    <select name="parent" class="form-control">
                        <option value="">All Parent Categories</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ (string) request('parent') === (string) $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}{{ $parent->trashed() ? ' [Trashed]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.categories.index', $showTrashed ? ['trashed' => 1] : []) }}"
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
                        <th>Category</th>
                        <th>Slug</th>
                        <th>Parent</th>
                        <th>Products</th>
                        <th>Subcategories</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                                        style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0;"
                                        onerror="this.onerror=null;this.src='{{ asset('images/placeholders/no-category-image.svg') }}';">
                                    <div>
                                        <div style="font-weight: 500;">{{ $category->name }}</div>
                                        @if($category->icon)
                                            <div style="font-size: 12px; color: #64748b;">{{ $category->icon }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="color: #64748b;">{{ $category->slug }}</td>
                            <td>{{ $category->parent->name ?? '-' }}</td>
                            <td>{{ $category->products_count }}</td>
                            <td>{{ $category->children_count }}</td>
                            <td>
                                <span class="badge badge-{{ $category->image ? 'success' : 'secondary' }}">
                                    {{ $category->image ? 'Uploaded' : 'Placeholder' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $category->is_active ? 'success' : 'secondary' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if($category->featured)
                                    <span class="badge badge-warning">Featured</span>
                                @endif
                            </td>
                            <td style="color: #64748b; font-size: 13px;">
                                {{ $category->deleted_at ? $category->deleted_at->format('M d, Y h:i A') : '-' }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    @if(!$showTrashed)
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                            onsubmit="return confirm('Move this category to trash?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.categories.restore', $category->id) }}" method="POST"
                                            onsubmit="return confirm('Restore this category?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.categories.force-destroy', $category->id) }}" method="POST"
                                            onsubmit="return confirm('Permanently delete this category? This cannot be undone.')">
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
                            <td colspan="9" style="text-align: center; color: #64748b; padding: 40px;">
                                {{ $showTrashed ? 'Trash is empty' : 'No categories found' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $categories->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
