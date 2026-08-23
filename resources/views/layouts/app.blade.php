<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', store_locale()) }}" dir="{{ store_is_rtl() ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }} - NovaMart</title>

    <!-- Icons (local copy) -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/fontawesome/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/frontend.css', 'resources/js/frontend.js'])

    @stack('styles')
</head>

<body data-route-name="{{ request()->route()?->getName() }}">
    @php
        $storefrontLocale = $storefront['locale'] ?? store_locale();
        $storefrontCurrency = $storefront['currency'] ?? store_currency();
        $storefrontLocaleMeta = $storefront['locale_meta'] ?? store_locale_meta();
        $storefrontCurrencyMeta = $storefront['currency_meta'] ?? store_currency_meta();
        $storefrontLocales = $storefront['locales'] ?? \App\Support\StorefrontPreferences::locales();
        $storefrontCurrencies = $storefront['currencies'] ?? \App\Support\StorefrontPreferences::currencies();
        $isAuthenticated = auth()->check();
        $outletUrl = route('products.index', ['featured' => 1, 'sort' => 'price_low']);
        $businessUrl = route('products.index', ['category' => 'office-supplies', 'sort' => 'popular']);
        $topDealsUrl = route('products.index', ['featured' => 1, 'sort' => 'popular']);
        $dealOfTheDayUrl = route('products.index', ['deal' => 'today', 'sort' => 'latest']);
        $discoverUrl = route('products.index', ['sort' => 'latest']);
        $giftIdeasUrl = route('products.index', ['category' => 'fashion', 'sort' => 'popular']);
        $membershipUrl = route('register');
        $orderStatusUrl = route('login');
        $savedItemsUrl = route('login');
        if ($isAuthenticated) {
            $authUser = auth()->user();
            $membershipUrl = $authUser->hasAnyRole(['super-admin', 'admin'])
                ? route('admin.dashboard')
                : ($authUser->hasRole('vendor') ? route('vendor.dashboard') : route('account.dashboard'));

            $orderStatusUrl = $authUser->hasAnyRole(['super-admin', 'admin'])
                ? route('admin.orders.index')
                : ($authUser->hasRole('vendor') ? route('vendor.dashboard') : route('account.orders'));

            $savedItemsUrl = $authUser->hasRole('customer')
                ? route('wishlist.index')
                : route('products.index');
        }
        $creditCardsUrl = route('credit-cards');
        $giftCardsUrl = route('gift-cards');
        $recentlyViewedUrl = route('products.recently-viewed');
    @endphp

    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="container header-top-row">
                <div class="header-top-links">
                    <a href="{{ $outletUrl }}">{{ __('Best Buy Outlet') }}</a>
                    <a href="{{ $businessUrl }}">{{ __('Best Buy Business') }}</a>
                </div>
                <div class="header-top-right">
                    <details class="pref-switcher">
                        <summary class="pref-trigger" aria-label="Storefront language and currency settings">
                            <span class="pref-current">{{ strtoupper((string) ($storefrontLocaleMeta['short'] ?? $storefrontLocale)) }}</span>
                            <span class="pref-current">{{ $storefrontCurrency }}</span>
                            <i class="fas fa-angle-down"></i>
                        </summary>
                        <div class="pref-dropdown-menu">
                            <p class="pref-section-title">{{ __('Change Language') }}</p>
                            @foreach($storefrontLocales as $localeCode => $localeMeta)
                                <form method="POST" action="{{ route('preferences.language') }}" class="pref-form">
                                    @csrf
                                    <button type="submit" name="locale" value="{{ $localeCode }}"
                                        class="pref-option {{ $storefrontLocale === $localeCode ? 'active' : '' }}">
                                        <span class="pref-radio">
                                            @if($storefrontLocale === $localeCode)
                                                <i class="fas fa-dot-circle"></i>
                                            @else
                                                <i class="far fa-circle"></i>
                                            @endif
                                        </span>
                                        <span>
                                            {{ $localeMeta['native'] ?? $localeMeta['name'] ?? strtoupper($localeCode) }}
                                            <small>- {{ strtoupper((string) ($localeMeta['short'] ?? $localeCode)) }}</small>
                                        </span>
                                    </button>
                                </form>
                            @endforeach

                            <div class="pref-divider"></div>
                            <p class="pref-section-title">{{ __('Change Currency') }}</p>
                            @foreach($storefrontCurrencies as $currencyCode => $currencyMeta)
                                <form method="POST" action="{{ route('preferences.currency') }}" class="pref-form">
                                    @csrf
                                    <button type="submit" name="currency" value="{{ $currencyCode }}"
                                        class="pref-option {{ $storefrontCurrency === $currencyCode ? 'active' : '' }}">
                                        <span class="pref-radio">
                                            @if($storefrontCurrency === $currencyCode)
                                                <i class="fas fa-dot-circle"></i>
                                            @else
                                                <i class="far fa-circle"></i>
                                            @endif
                                        </span>
                                        <span>
                                            {{ $currencyMeta['symbol'] ?? ($currencyCode . ' ') }} - {{ $currencyCode }}
                                            <small>- {{ $currencyMeta['name'] ?? $currencyCode }}</small>
                                        </span>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </details>

                    <a href="tel:+8801701885707"><i class="fas fa-phone"></i> +8801701885707</a>
                    <a href="{{ $orderStatusUrl }}">{{ __('Order Status') }}</a>
                    <a href="{{ route('page.show', 'terms-conditions') }}">{{ __('Help') }}</a>
                </div>
            </div>
        </div>

        <div class="header-main">
            <div class="container header-main-row">
                <div class="header-main-left">
                    <a href="{{ route('home') }}" class="logo">Nova<span>Mart</span></a>
                    <a href="{{ route('products.index') }}" class="menu-launcher">
                        <i class="fas fa-bars"></i> {{ __('Menu') }}
                    </a>
                </div>

                <form action="{{ route('products.search') }}" method="GET" class="search-box js-live-search"
                    data-suggestion-url="{{ route('products.suggestions') }}">
                    <input type="text" name="q" placeholder="{{ __('Search NovaMart') }}" value="{{ request('q') }}"
                        autocomplete="off" aria-autocomplete="list" aria-expanded="false">
                    <button type="submit"><i class="fas fa-search"></i></button>
                    <div class="search-suggestions" hidden></div>
                </form>

                <div class="header-actions">
                    <a href="{{ $giftIdeasUrl }}">
                        <i class="fas fa-gift"></i>
                        <span>{{ __('Gift Ideas') }}</span>
                    </a>

                    @auth
                        @php
                            $dashboardRoute = auth()->user()->hasAnyRole(['super-admin', 'admin'])
                                ? route('admin.dashboard')
                                : (auth()->user()->hasRole('vendor') ? route('vendor.dashboard') : route('account.dashboard'));
                        @endphp
                        <a href="{{ $dashboardRoute }}">
                            <i class="fas fa-user"></i>
                            <span>{{ __('Account') }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}">
                            <i class="fas fa-user"></i>
                            <span>{{ __('Sign in') }}</span>
                        </a>
                    @endauth

                    <a href="{{ $savedItemsUrl }}">
                        <i class="fas fa-heart"></i>
                        <span>{{ __('Saved Items') }}</span>
                    </a>

                    <a href="{{ route('cart.index') }}" class="cart-badge">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart-count">0</span>
                        <small>{{ __('Cart') }}</small>
                    </a>
                </div>
            </div>
        </div>

        <div class="header-utility">
            <div class="container header-utility-row">
                <div class="header-utility-links">
                    <a href="{{ $topDealsUrl }}">{{ __('Top Deals') }}</a>
                    <a href="{{ $dealOfTheDayUrl }}">{{ __('Deal of the Day') }}</a>
                    <a href="{{ $discoverUrl }}">{{ __('Discover') }}</a>
                    <a href="{{ $membershipUrl }}">{{ __('My NovaMart Memberships') }}</a>
                    <a href="{{ $creditCardsUrl }}">{{ __('Credit Cards') }}</a>
                    <a href="{{ $giftCardsUrl }}">{{ __('Gift Cards') }}</a>
                </div>
                <div class="header-utility-links">
                    <a href="{{ $recentlyViewedUrl }}">{{ __('Recently Viewed') }}</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    @unless(request()->routeIs('home'))
        <nav class="nav">
            <div class="container">
                <ul class="nav-menu">
                    <li><a href="{{ route('products.index') }}"><i class="fas fa-th-large"></i> {{ __('Shop All Departments') }}</a></li>
                    @php
                        $navCategories = \App\Domains\ECommerce\Models\Category::active()->parents()->ordered()->take(6)->get();
                    @endphp
                    @foreach($navCategories as $cat)
                        <li><a href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
        </nav>
    @endunless

    <!-- Alerts -->
    @if(session('success') || session('error') || session('warning'))
        <div class="container" style="margin-top: 20px;">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ session('warning') }}
                </div>
            @endif
        </div>
    @endif

    <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a href="{{ route('home') }}" class="logo" style="font-size: 32px;">Nova<span
                            style="color: var(--secondary);">Mart</span></a>
                    <p style="color: #9ca3af; margin-top: 16px; line-height: 1.8;">{{ __('Bangladesh\'s leading multi-vendor NovaMart platform. Shop with confidence and enjoy the best deals on thousands of products.') }}</p>
                    @php
                        $socialLinks = [
                            [
                                'url' => \App\Domains\ECommerce\Models\Setting::get('facebook_url', 'https://facebook.com/novamart'),
                                'icon' => 'fab fa-facebook-f',
                                'label' => 'Facebook',
                            ],
                            [
                                'url' => \App\Domains\ECommerce\Models\Setting::get('instagram_url', 'https://instagram.com/novamart'),
                                'icon' => 'fab fa-instagram',
                                'label' => 'Instagram',
                            ],
                            [
                                'url' => \App\Domains\ECommerce\Models\Setting::get('youtube_url', 'https://youtube.com/@novamart'),
                                'icon' => 'fab fa-youtube',
                                'label' => 'YouTube',
                            ],
                            [
                                'url' => \App\Domains\ECommerce\Models\Setting::get('twitter_url', 'https://x.com/novamart'),
                                'icon' => 'fab fa-twitter',
                                'label' => 'Twitter',
                            ],
                        ];
                    @endphp
                    <div class="social-links">
                        @foreach($socialLinks as $social)
                            @if(filled($social['url']))
                                <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                    aria-label="{{ $social['label'] }}">
                                    <i class="{{ $social['icon'] }}"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div>
                    <h4>{{ __('Quick Links') }}</h4>
                    <ul>
                        <li><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
                        <li><a href="{{ route('contact') }}">{{ __('Contact Us') }}</a></li>
                        <li><a href="{{ route('page.show', 'terms-conditions') }}">{{ __('Terms & Conditions') }}</a></li>
                        <li><a href="{{ route('page.show', 'privacy-policy') }}">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ route('page.show', 'return-refund-policy') }}">{{ __('Return Policy') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4>{{ __('My Account') }}</h4>
                    <ul>
                        @auth
                            @if(auth()->user()->hasAnyRole(['super-admin', 'admin']))
                                <li><a href="{{ route('admin.dashboard') }}">{{ __('Admin Dashboard') }}</a></li>
                            @elseif(auth()->user()->hasRole('vendor'))
                                <li><a href="{{ route('vendor.dashboard') }}">{{ __('Vendor Dashboard') }}</a></li>
                                <li><a href="{{ route('vendor.reports.index') }}">{{ __('Reports') }}</a></li>
                                <li><a href="{{ route('products.index') }}">{{ __('Browse Products') }}</a></li>
                                <li><a href="{{ route('cart.index') }}">{{ __('Shopping Cart') }}</a></li>
                            @else
                                <li><a href="{{ route('account.dashboard') }}">{{ __('My Account') }}</a></li>
                                <li><a href="{{ route('account.orders') }}">{{ __('Order History') }}</a></li>
                                <li><a href="{{ route('wishlist.index') }}">{{ __('Wishlist') }}</a></li>
                                <li><a href="{{ route('cart.index') }}">{{ __('Shopping Cart') }}</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}">{{ __('Login') }}</a></li>
                            <li><a href="{{ route('register') }}">{{ __('Register') }}</a></li>
                            <li><a href="{{ route('products.index') }}">{{ __('Browse Products') }}</a></li>
                            <li><a href="{{ route('cart.index') }}">{{ __('Shopping Cart') }}</a></li>
                        @endauth
                    </ul>
                </div>

                <div>
                    <h4>{{ __('Contact Info') }}</h4>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> {{ __('Gulshan, Dhaka, Bangladesh') }}</li>
                        <li><i class="fas fa-phone"></i> +8801701885707</li>
                        <li><i class="fas fa-envelope"></i> info@novamart.com</li>
                        <li><i class="fas fa-clock"></i> {{ __('Sat-Thu: 9:00 AM - 9:00 PM') }}</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} NovaMart. {{ __('All Rights Reserved.') }}</p>
            </div>
        </div>
    </footer>

    <script>
        window.storefrontConfig = {
            currency: @json(\App\Support\StorefrontPreferences::toClientConfig($storefrontCurrency)),
            endpoints: {
                cartCount: @json(route('cart.count')),
                cartAdd: @json(route('cart.add')),
                wishlistToggle: @json(route('wishlist.toggle')),
            },
        };
    </script>

    @stack('scripts')
</body>

</html>


