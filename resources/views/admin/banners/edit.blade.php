@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Banner</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('admin.banners.index') }}">Banners</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>{{ $banner->title }}</span>
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
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input id="title" type="text" name="title" class="form-control"
                        value="{{ old('title', $banner->title) }}" required>
                </div>

                <div class="form-group">
                    <label for="subtitle">Subtitle</label>
                    <input id="subtitle" type="text" name="subtitle" class="form-control"
                        value="{{ old('subtitle', $banner->subtitle) }}">
                </div>

                <div class="form-group">
                    <label for="image">Desktop Banner Image *</label>
                    <input id="image" type="file" name="image" class="form-control" accept="image/*">
                </div>

                @if($banner->image)
                    <div class="form-group">
                        <label style="display: inline-flex; align-items: center; gap: 12px;">
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                style="width: 96px; height: 64px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0;"
                                onerror="this.onerror=null;this.src='{{ asset('images/placeholders/no-banner-image.svg') }}';">
                            <span style="display: inline-flex; gap: 8px; align-items: center;">
                                <input type="checkbox" name="remove_image" value="1" {{ old('remove_image') ? 'checked' : '' }}>
                                Remove desktop image
                            </span>
                        </label>
                    </div>
                @endif

                <div class="form-group">
                    <label for="mobile_image">Mobile Banner Image</label>
                    <input id="mobile_image" type="file" name="mobile_image" class="form-control" accept="image/*">
                </div>

                @if($banner->mobile_image)
                    <div class="form-group">
                        <label style="display: inline-flex; align-items: center; gap: 12px;">
                            <img src="{{ $banner->mobile_image_url }}" alt="{{ $banner->title }} mobile"
                                style="width: 70px; height: 70px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0;"
                                onerror="this.onerror=null;this.src='{{ asset('images/placeholders/no-banner-image.svg') }}';">
                            <span style="display: inline-flex; gap: 8px; align-items: center;">
                                <input type="checkbox" name="remove_mobile_image" value="1"
                                    {{ old('remove_mobile_image') ? 'checked' : '' }}>
                                Remove mobile image
                            </span>
                        </label>
                    </div>
                @endif

                <div class="form-group">
                    <label for="link">Link</label>
                    <input id="link" type="text" name="link" class="form-control"
                        value="{{ old('link', $banner->link) }}">
                </div>

                <div class="form-group">
                    <label for="button_text">Button Text</label>
                    <input id="button_text" type="text" name="button_text" class="form-control"
                        value="{{ old('button_text', $banner->button_text) }}">
                </div>

                <div class="form-group">
                    <label for="position">Position *</label>
                    <select id="position" name="position" class="form-control" required>
                        @foreach($positions as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('position', $banner->position) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="order">Order</label>
                    <input id="order" type="number" name="order" class="form-control" min="0"
                        value="{{ old('order', $banner->order) }}">
                </div>

                <div class="form-group">
                    <label for="starts_at">Starts At</label>
                    <input id="starts_at" type="datetime-local" name="starts_at" class="form-control"
                        value="{{ old('starts_at', optional($banner->starts_at)->format('Y-m-d\\TH:i')) }}">
                </div>

                <div class="form-group">
                    <label for="ends_at">Ends At</label>
                    <input id="ends_at" type="datetime-local" name="ends_at" class="form-control"
                        value="{{ old('ends_at', optional($banner->ends_at)->format('Y-m-d\\TH:i')) }}">
                </div>

                <div class="form-group" style="margin-top: 8px;">
                    <label style="display: inline-flex; gap: 8px; align-items: center;">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                        Active
                    </label>
                </div>
            </div>
            <div class="card-header" style="justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Banner</button>
            </div>
        </form>
    </div>
@endsection
