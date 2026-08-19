<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} - NovaMart</title>

    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/fontawesome/css/all.min.css">
    @vite(['resources/css/auth.css', 'resources/js/auth.js'])
</head>

<body>
    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-left-content">
                <h1>Nova<span>Mart</span></h1>
                <p>Bangladesh's leading multi-vendor e-commerce platform. Shop from thousands of verified sellers.</p>

                <div class="features">
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <h4>Secure Shopping</h4>
                            <p>100% secure payment processing</p>
                        </div>
                    </div>
                    <div class="feature">
                        <i class="fas fa-truck"></i>
                        <div>
                            <h4>Fast Delivery</h4>
                            <p>Free shipping on orders over ৳2000</p>
                        </div>
                    </div>
                    <div class="feature">
                        <i class="fas fa-undo"></i>
                        <div>
                            <h4>Easy Returns</h4>
                            <p>7 days hassle-free return policy</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-right">
            @yield('form')
        </div>
    </div>
</body>

</html>

