@extends('layouts.app')

@section('content')
    <div class="container section">
        <div class="account-layout-grid">
            @include('frontend.account.partials.sidebar', ['user' => $user])

            <div>
                <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 20px;">My Profile</h1>

                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="card" style="padding: 24px;">
                    <form action="{{ route('account.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px;">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb;">
                            <div>
                                <p style="font-weight: 600; margin-bottom: 6px;">Profile Photo</p>
                                <input type="file" name="avatar" accept="image/*" class="form-control">
                                <p style="font-size: 12px; color: #6b7280; margin-top: 6px;">JPG, PNG, GIF (max 2MB)</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input id="name" type="text" name="name" class="form-control"
                                value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" class="form-control"
                                value="{{ $user->email }}" readonly style="background: #f9fafb; color: #6b7280;">
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" type="text" name="phone" class="form-control"
                                value="{{ old('phone', $user->phone) }}" placeholder="01XXXXXXXXX">
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
