@extends('layouts.auth')

@section('form')
    <div class="auth-form">
        <h2>Welcome Back!</h2>
        <p class="subtitle">Sign in to continue shopping</p>

        @if(session('error'))
            <p class="error-text" style="margin-bottom: 16px;">{{ session('error') }}</p>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'error' : '' }}"
                    value="{{ old('email', 'admin@novamart.com') }}" placeholder="Enter your email" required autofocus>
                @error('email')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                    class="form-control {{ $errors->has('password') ? 'error' : '' }}" placeholder="Enter your password"
                    value="{{ old('password', 'password') }}"
                    required>
                @error('password')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
                <a href="#">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary">
                Sign In <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
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
            Don't have an account? <a href="{{ route('register') }}">Create Account</a>
        </p>
    </div>
@endsection
