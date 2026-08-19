@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Category</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('admin.categories.index') }}">Categories</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>{{ $category->name }}</span>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    <div class="card">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Category Name *</label>
                    <input id="name" type="text" name="name" class="form-control"
                        value="{{ old('name', $category->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="parent_id">Parent Category</label>
                    <select id="parent_id" name="parent_id" class="form-control">
                        <option value="">None (Top Level)</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}"
                                {{ (string) old('parent_id', $category->parent_id) === (string) $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="icon">Icon Class</label>
                    <input id="icon" type="text" name="icon" class="form-control"
                        value="{{ old('icon', $category->icon) }}" placeholder="e.g. fas fa-folder">
                </div>

                <div class="form-group">
                    <label for="image">Category Image</label>
                    <input id="image" type="file" name="image" class="form-control" accept="image/*">
                </div>

                @if($category->image)
                    <div class="form-group">
                        <label style="display: inline-flex; align-items: center; gap: 12px;">
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                                style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0;"
                                onerror="this.onerror=null;this.src='{{ asset('images/placeholders/no-category-image.svg') }}';">
                            <span style="display: inline-flex; gap: 8px; align-items: center;">
                                <input type="checkbox" name="remove_image" value="1" {{ old('remove_image') ? 'checked' : '' }}>
                                Remove current image
                            </span>
                        </label>
                    </div>
                @endif

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="commission_rate">Commission Rate (%)</label>
                    <input id="commission_rate" type="number" step="0.01" min="0" max="100" name="commission_rate"
                        class="form-control" value="{{ old('commission_rate', $category->commission_rate) }}">
                </div>

                <div class="form-group">
                    <label for="meta_title">Meta Title</label>
                    <input id="meta_title" type="text" name="meta_title" class="form-control"
                        value="{{ old('meta_title', $category->meta_title) }}">
                </div>

                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control"
                        rows="3">{{ old('meta_description', $category->meta_description) }}</textarea>
                </div>

                <div class="form-group" style="display: flex; gap: 24px; margin-top: 8px;">
                    <label style="display: inline-flex; gap: 8px; align-items: center;">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        Active
                    </label>
                    <label style="display: inline-flex; gap: 8px; align-items: center;">
                        <input type="checkbox" name="featured" value="1"
                            {{ old('featured', $category->featured) ? 'checked' : '' }}>
                        Featured
                    </label>
                </div>
            </div>
            <div class="card-header" style="justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Category</button>
            </div>
        </form>
    </div>
@endsection
