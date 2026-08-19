<aside>
    <div class="card" style="padding: 24px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <div
                style="width: 80px; height: 80px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: white; font-size: 32px; font-weight: 600;">
                {{ substr($user->name, 0, 1) }}
            </div>
            <h3 style="font-weight: 600;">{{ $user->name }}</h3>
            <p style="font-size: 14px; color: #6b7280;">{{ $user->email }}</p>
        </div>

        <nav style="margin-top: 24px;">
            <a href="{{ route('account.dashboard') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; color: #374151; font-weight: 500; {{ request()->routeIs('account.dashboard') ? 'background: #f3f4f6;' : '' }}">
                <i class="fas fa-home" style="width: 20px;"></i> Dashboard
            </a>
            <a href="{{ route('account.orders') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; color: #374151; font-weight: 500; {{ request()->routeIs('account.orders*') ? 'background: #f3f4f6;' : '' }}">
                <i class="fas fa-shopping-bag" style="width: 20px;"></i> My Orders
            </a>
            <a href="{{ route('account.returns') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; color: #374151; font-weight: 500; {{ request()->routeIs('account.returns*') ? 'background: #f3f4f6;' : '' }}">
                <i class="fas fa-undo-alt" style="width: 20px;"></i> My Returns
            </a>
            <a href="{{ route('wishlist.index') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; color: #374151; font-weight: 500;">
                <i class="fas fa-heart" style="width: 20px;"></i> Wishlist
            </a>
            <a href="{{ route('account.addresses') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; color: #374151; font-weight: 500; {{ request()->routeIs('account.addresses') ? 'background: #f3f4f6;' : '' }}">
                <i class="fas fa-map-marker-alt" style="width: 20px;"></i> Addresses
            </a>
            <a href="{{ route('account.profile') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; color: #374151; font-weight: 500; {{ request()->routeIs('account.profile') ? 'background: #f3f4f6;' : '' }}">
                <i class="fas fa-user" style="width: 20px;"></i> Profile
            </a>
            <a href="{{ route('account.password') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; color: #374151; font-weight: 500; {{ request()->routeIs('account.password') ? 'background: #f3f4f6;' : '' }}">
                <i class="fas fa-lock" style="width: 20px;"></i> Change Password
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; color: #ef4444; font-weight: 500; width: 100%; border: none; background: none; cursor: pointer; text-align: left;">
                    <i class="fas fa-sign-out-alt" style="width: 20px;"></i> Logout
                </button>
            </form>
        </nav>
    </div>
</aside>
