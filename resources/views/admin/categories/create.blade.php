@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Add Category</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('admin.categories.index') }}">Categories</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Add</span>
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
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Category Name *</label>
                    <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="parent_id">Parent Category</label>
                    <select id="parent_id" name="parent_id" class="form-control">
                        <option value="">None (Top Level)</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ (string) old('parent_id') === (string) $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="icon">Icon Class</label>
                    <input id="icon" type="text" name="icon" class="form-control" value="{{ old('icon') }}"
                        placeholder="e.g. fas fa-folder">
                </div>

                <div class="form-group">
                    <label for="image">Category Image</label>
                    <input id="image" type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="commission_rate">Commission Rate (%)</label>
                    <input id="commission_rate" type="number" step="0.01" min="0" max="100" name="commission_rate"
                        class="form-control" value="{{ old('commission_rate') }}">
                </div>

                <div class="form-group">
                    <label for="meta_title">Meta Title</label>
                    <input id="meta_title" type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}">
                </div>

                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3">{{ old('meta_description') }}</textarea>
                </div>

                <div class="form-group" style="display: flex; gap: 24px; margin-top: 8px;">
                    <label style="display: inline-flex; gap: 8px; align-items: center;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                        Active
                    </label>
                    <label style="display: inline-flex; gap: 8px; align-items: center;">
                        <input type="checkbox" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                        Featured
                    </label>
                </div>
            </div>
            <div class="card-header" style="justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Category</button>
            </div>
        </form>
    </div>
@endsection
