@extends('layouts.app')

@section('content')
    <div class="container section">
        <div class="account-layout-grid">
            @include('frontend.account.partials.sidebar', ['user' => auth()->user()])

            <div>
                <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 20px;">Change Password</h1>

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
                    <form action="{{ route('account.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input id="current_password" type="password" name="current_password" class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input id="password" type="password" name="password" class="form-control" minlength="8"
                                required>
                            <p style="font-size: 12px; color: #6b7280; margin-top: 6px;">Minimum 8 characters</p>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="form-control" minlength="8" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
