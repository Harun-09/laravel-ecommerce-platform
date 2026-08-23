<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - Admin | NovaMart</title>
    
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/fontawesome/css/all.min.css">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])

    @stack('styles')
</head>

<body class="admin-layout">
    @php
        $user = auth()->user();
        $isVendorOnly = $user->hasRole('vendor') && !$user->hasAnyRole(['admin', 'super-admin']);
        $unreadNotificationsCount = $user->unreadNotifications()->count();
        $unreadMessagesCount = \App\Models\ContactMessage::query()
            ->where('status', \App\Models\ContactMessage::STATUS_NEW)
            ->count();
    @endphp

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">Nova<span>Mart</span></div>
            <p>{{ $isVendorOnly ? 'Vendor Panel' : 'Admin Panel' }}</p>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                @if($isVendorOnly)
                    @can('view vendor dashboard')
                        <a href="{{ route('vendor.dashboard') }}"
                            class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    @endcan
                @else
                    @can('view dashboard')
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    @endcan
                @endif
            </div>

            <div class="nav-section">
                <div class="nav-section-title">NovaMart</div>

                @if($isVendorOnly)
                    @can('view orders')
                        <a href="{{ route('vendor.orders.index') }}"
                            class="nav-link {{ request()->routeIs('vendor.orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Orders</span>
                        </a>
                    @endcan

                    @can('view reports')
                        <a href="{{ route('vendor.reports.index') }}"
                            class="nav-link {{ request()->routeIs('vendor.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Reports</span>
                        </a>
                    @endcan
                @else
                    @can('view orders')
                        <a href="{{ route('admin.orders.index') }}"
                            class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Orders</span>
                            @php $pendingOrders = \App\Models\Order::pending()->count(); @endphp
                            @if($pendingOrders > 0)
                                <span class="badge">{{ $pendingOrders }}</span>
                            @endif
                        </a>
                    @endcan

                    @can('view orders')
                        <a href="{{ route('admin.returns.index') }}"
                            class="nav-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">
                            <i class="fas fa-undo-alt"></i>
                            <span>Returns</span>
                            @php $pendingReturns = \App\Models\ReturnRequest::query()->where('status', \App\Models\ReturnRequest::STATUS_REQUESTED)->count(); @endphp
                            @if($pendingReturns > 0)
                                <span class="badge">{{ $pendingReturns }}</span>
                            @endif
                        </a>
                    @endcan

                    @can('view payouts')
                        <a href="{{ route('admin.payouts.index') }}"
                            class="nav-link {{ request()->routeIs('admin.payouts.*') ? 'active' : '' }}">
                            <i class="fas fa-wallet"></i>
                            <span>Payouts</span>
                            @php $pendingPayouts = \App\Models\VendorPayout::query()->whereIn('status', ['pending', 'processing'])->count(); @endphp
                            @if($pendingPayouts > 0)
                                <span class="badge">{{ $pendingPayouts }}</span>
                            @endif
                        </a>
                    @endcan

                    @can('view reports')
                        <a href="{{ route('admin.reports.index') }}"
                            class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Reports</span>
                        </a>
                    @endcan

                    <a href="{{ route('admin.messages.index') }}"
                        class="nav-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                        <i class="far fa-comment-dots"></i>
                        <span>Messages</span>
                        @if($unreadMessagesCount > 0)
                            <span class="badge">{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.notifications.index') }}"
                        class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                        @if($unreadNotificationsCount > 0)
                            <span class="badge">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                        @endif
                    </a>

                    @if($user->hasRole('super-admin'))
                        @can('view reports')
                            <a href="{{ route('admin.audit-logs.index') }}"
                                class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                                <i class="fas fa-shield-alt"></i>
                                <span>Audit Logs</span>
                            </a>

                            <a href="{{ route('admin.observability.index') }}"
                                class="nav-link {{ request()->routeIs('admin.observability.*') ? 'active' : '' }}">
                                <i class="fas fa-wave-square"></i>
                                <span>Observability</span>
                            </a>
                        @endcan
                    @endif

                    @can('view products')
                        <a href="{{ route('admin.products.index') }}"
                            class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i>
                            <span>Products</span>
                        </a>
                    @endcan

                    @can('view reviews')
                        <a href="{{ route('admin.reviews.index') }}"
                            class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                            <i class="fas fa-star-half-alt"></i>
                            <span>Reviews</span>
                            @php $pendingReviews = \App\Domains\ECommerce\Models\Review::query()->where('is_approved', false)->count(); @endphp
                            @if($pendingReviews > 0)
                                <span class="badge">{{ $pendingReviews }}</span>
                            @endif
                        </a>
                    @endcan

                    @can('view categories')
                        <a href="{{ route('admin.categories.index') }}"
                            class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-folder"></i>
                            <span>Categories</span>
                        </a>
                    @endcan

                    @can('view banners')
                        <a href="{{ route('admin.banners.index') }}"
                            class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                            <i class="fas fa-images"></i>
                            <span>Banners</span>
                        </a>
                    @endcan
                @endif
            </div>

            @if(!$isVendorOnly && ($user->can('view vendors') || $user->can('view users')))
                <div class="nav-section">
                    <div class="nav-section-title">Users</div>

                    @can('view vendors')
                        <a href="{{ route('admin.vendors.index') }}"
                            class="nav-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                            <i class="fas fa-store"></i>
                            <span>Vendors</span>
                            @php $pendingVendors = \App\Models\Vendor::pending()->count(); @endphp
                            @if($pendingVendors > 0)
                                <span class="badge">{{ $pendingVendors }}</span>
                            @endif
                        </a>
                    @endcan

                    @can('view users')
                        <a href="{{ route('admin.users.index') }}"
                            class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    @endcan
                </div>
            @endif

            <div class="nav-section">
                <div class="nav-section-title">Settings</div>

                @if(!$isVendorOnly)
                    @can('view settings')
                        <a href="{{ route('admin.shipping.index') }}"
                            class="nav-link {{ request()->routeIs('admin.shipping.*') ? 'active' : '' }}">
                            <i class="fas fa-truck"></i>
                            <span>Shipping</span>
                        </a>
                    @endcan
                @endif

                <a href="{{ route('home') }}" class="nav-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>View Store</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="nav-link"
                        style="width: 100%; border: none; background: none; cursor: pointer; text-align: left;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-left-tools">
                <button type="button" class="button-show-hide" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-box">
                    <i class="fas fa-search" style="color: #94a3b8;"></i>
                    <input type="text" placeholder="Search...">
                </div>
            </div>

            <div class="header-actions">
                @if(!$isVendorOnly)
                    <div class="message-dropdown" data-message-root data-feed-url="{{ route('admin.messages.feed') }}"
                        data-mark-all-url="{{ route('admin.messages.read-all') }}"
                        data-read-url-template="{{ route('admin.messages.read', ['message' => '__MESSAGE_ID__']) }}">
                        <button type="button" class="header-btn message-trigger" aria-label="Messages" aria-expanded="false"
                            data-message-trigger>
                            <i class="far fa-comment-dots"></i>
                            <span class="badge message-badge {{ $unreadMessagesCount > 0 ? '' : 'is-hidden' }}"
                                data-message-badge>{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</span>
                        </button>

                        <div class="message-menu" hidden data-message-menu>
                            <div class="message-menu-head">
                                <h4>Messages</h4>
                                <button type="button" class="message-mark-all" data-message-mark-all>
                                    Mark all read
                                </button>
                            </div>
                            <div class="message-menu-list" data-message-list>
                                <div class="message-empty">Loading messages...</div>
                            </div>
                            <a href="{{ route('admin.messages.index') }}" class="message-view-all">View all</a>
                        </div>
                    </div>

                    <div class="notification-dropdown" data-notification-root
                        data-feed-url="{{ route('admin.notifications.feed') }}"
                        data-mark-all-url="{{ route('admin.notifications.read-all') }}"
                        data-read-url-template="{{ route('admin.notifications.read', ['notification' => '__NOTIFICATION_ID__']) }}">
                        <button type="button" class="header-btn notification-trigger" aria-label="Notifications"
                            aria-expanded="false" data-notification-trigger>
                            <i class="fas fa-bell"></i>
                            <span
                                class="badge notification-badge {{ $unreadNotificationsCount > 0 ? '' : 'is-hidden' }}"
                                data-notification-badge>{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                        </button>

                        <div class="notification-menu" hidden data-notification-menu>
                            <div class="notification-menu-head">
                                <h4>Notifications</h4>
                                <button type="button" class="notification-mark-all" data-notification-mark-all>
                                    Mark all read
                                </button>
                            </div>
                            <div class="notification-menu-list" data-notification-list>
                                <div class="notification-empty">Loading notifications...</div>
                            </div>
                            <a href="{{ route('admin.notifications.index') }}" class="notification-view-all">View all</a>
                        </div>
                    </div>
                @endif

                <div class="user-menu">
                    <div class="info">
                        <div class="name">{{ auth()->user()->name }}</div>
                        <div class="role">{{ auth()->user()->getRoleNames()->first() }}</div>
                    </div>
                    <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="admin-content">
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

            @yield('content')
        </main>
    </div>
    <script>
        (function () {
            function isBlockingOverlay(element) {
                if (!(element instanceof HTMLElement)) return false;
                if (element.classList.contains('sidebar')) return false;
                if (element.closest('.sidebar') || element.closest('.main-wrapper')) return false;

                const style = window.getComputedStyle(element);
                const isFixedLayer = style.position === 'fixed' || style.position === 'absolute';
                const hasBackdropLikeClass =
                    /overlay|backdrop|modal/i.test(element.className || '') ||
                    element.id === 'overlay' ||
                    element.id === 'backdrop';

                const rect = element.getBoundingClientRect();
                const coversViewport =
                    rect.width >= window.innerWidth * 0.95 &&
                    rect.height >= window.innerHeight * 0.95 &&
                    rect.top <= 2 &&
                    rect.left <= 2;

                const zIndex = Number.parseInt(style.zIndex || '0', 10) || 0;
                const blocksPointer = style.pointerEvents !== 'none';

                return isFixedLayer && hasBackdropLikeClass && coversViewport && blocksPointer && zIndex >= 90;
            }

            function clearBlockingOverlays() {
                const candidates = document.querySelectorAll('body *');
                candidates.forEach((el) => {
                    if (isBlockingOverlay(el)) {
                        el.remove();
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', clearBlockingOverlays);
            window.addEventListener('load', clearBlockingOverlays);
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    clearBlockingOverlays();
                }
            });
        })();
    </script>
    @stack('scripts')
</body>

</html>

