@extends('layouts.auth')

@section('form')
    <div class="auth-form">
        <h2>Create Account</h2>
        <p class="subtitle">Join NovaMart and start shopping</p>

        @if(session('error'))
            <p class="error-text" style="margin-bottom: 16px;">{{ session('error') }}</p>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'error' : '' }}"
                    value="{{ old('name') }}" placeholder="Enter your full name" required autofocus>
                @error('name')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'error' : '' }}"
                    value="{{ old('email') }}" placeholder="Enter your email" required>
                @error('email')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}"
                    placeholder="Enter your phone number">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                    class="form-control {{ $errors->has('password') ? 'error' : '' }}" placeholder="Create a password"
                    required>
                @error('password')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                    placeholder="Confirm your password" required>
            </div>

            <div class="checkbox-group" style="justify-content: flex-start; gap: 8px;">
                <label>
                    <input type="checkbox" name="terms" required>
                    I agree to the <a href="{{ route('page.show', 'terms-conditions') }}" style="color: #6366f1;">Terms &
                        Conditions</a>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                Create Account <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div class="divider">
            <span>or continue with</span>
        </div>

        <div class="social-login">
            <a href="{{ route('auth.social.redirect', ['provider' => 'google']) }}" class="social-btn google" aria-label="Continue with Google">
                <i class="fab fa-google"></i>
            </a>
            <a href="{{ route('auth.social.redirect', ['provider' => 'facebook']) }}" class="social-btn facebook" aria-label="Continue with Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
        </div>

        <p class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </p>
    </div>
@endsection
