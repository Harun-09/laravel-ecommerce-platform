@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Add Banner</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('admin.banners.index') }}">Banners</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Add</span>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="card">
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input id="title" type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label for="subtitle">Subtitle</label>
                    <input id="subtitle" type="text" name="subtitle" class="form-control" value="{{ old('subtitle') }}">
                </div>

                <div class="form-group">
                    <label for="image">Desktop Banner Image *</label>
                    <input id="image" type="file" name="image" class="form-control" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label for="mobile_image">Mobile Banner Image</label>
                    <input id="mobile_image" type="file" name="mobile_image" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="link">Link</label>
                    <input id="link" type="text" name="link" class="form-control" value="{{ old('link') }}"
                        placeholder="/category/fashion or https://example.com">
                </div>

                <div class="form-group">
                    <label for="button_text">Button Text</label>
                    <input id="button_text" type="text" name="button_text" class="form-control"
                        value="{{ old('button_text') }}">
                </div>

                <div class="form-group">
                    <label for="position">Position *</label>
                    <select id="position" name="position" class="form-control" required>
                        @foreach($positions as $value => $label)
                            <option value="{{ $value }}" {{ old('position', 'hero') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="order">Order</label>
                    <input id="order" type="number" name="order" class="form-control" min="0"
                        value="{{ old('order', 0) }}">
                </div>

                <div class="form-group">
                    <label for="starts_at">Starts At</label>
                    <input id="starts_at" type="datetime-local" name="starts_at" class="form-control"
                        value="{{ old('starts_at') }}">
                </div>

                <div class="form-group">
                    <label for="ends_at">Ends At</label>
                    <input id="ends_at" type="datetime-local" name="ends_at" class="form-control"
                        value="{{ old('ends_at') }}">
                </div>

                <div class="form-group" style="margin-top: 8px;">
                    <label style="display: inline-flex; gap: 8px; align-items: center;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                        Active
                    </label>
                </div>
            </div>
            <div class="card-header" style="justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Banner</button>
            </div>
        </form>
    </div>
@endsection
