<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $recentOrders = $user->orders()->with('vendor')->latest()->take(5)->get();
        $wishlistCount = $user->wishlist()->count();
        $addressCount = $user->addresses()->count();

        return view('frontend.account.dashboard', compact('user', 'recentOrders', 'wishlistCount', 'addressCount'));
    }

    public function orders(Request $request)
    {
        $orders = auth()->user()->orders()
            ->with(['vendor', 'items', 'returnRequests'])
            ->when($request->filled('search'), fn($q) => $q->where('order_number', 'like', '%' . trim((string) $request->search) . '%'))
            ->when($request->filled('status'), fn($q) => $q->where('status', \App\Domains\ECommerce\Models\Order::normalizeStatus($request->status)))
            ->latest()
            ->paginate(10);

        return view('frontend.account.orders', compact('orders'));
    }

    public function orderDetail($orderNumber)
    {
        $order = auth()->user()->orders()
            ->where('order_number', $orderNumber)
            ->with([
                'vendor',
                'payments',
                'items.product',
                'statusHistories' => fn($query) => $query->with('user')->latest(),
                'returnRequests' => fn($query) => $query->with(['statusHistories' => fn($history) => $history->with('user')->latest()])->latest(),
            ])
            ->firstOrFail();

        $canRequestReturn = $order->canRequestReturn();
        $canRetryPayment = $order->canRetryOnlinePayment();

        return view('frontend.account.order-detail', compact('order', 'canRequestReturn', 'canRetryPayment'));
    }

    public function cancelOrder(Request $request, $orderNumber)
    {
        $order = auth()->user()->orders()
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($order->cancel($request->reason, auth()->user())) {
            return back()->with('success', 'Order cancelled successfully.');
        }

        return back()->with('error', 'This order cannot be cancelled.');
    }

    public function profile()
    {
        if (!view()->exists('frontend.account.profile')) {
            return $this->missingViewFallback('Profile');
        }

        return view('frontend.account.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'phone']);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        if (!view()->exists('frontend.account.change-password')) {
            return $this->missingViewFallback('Change Password');
        }

        return view('frontend.account.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function addresses()
    {
        if (!view()->exists('frontend.account.addresses')) {
            return $this->missingViewFallback('Addresses');
        }

        $addresses = auth()->user()->addresses;
        return view('frontend.account.addresses', compact('addresses'));
    }

    private function missingViewFallback(string $section)
    {
        return redirect()
            ->route('account.dashboard')
            ->with('warning', "{$section} page is temporarily unavailable. You have been redirected to dashboard.");
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'type' => 'required|in:shipping,billing,both',
        ]);

        $address = auth()->user()->addresses()->create($request->all());

        if ($request->boolean('is_default')) {
            $address->setAsDefault();
        }

        return back()->with('success', 'Address added successfully.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:500',
            'city' => 'required|string|max:100',
        ]);

        $address->update($request->all());

        if ($request->boolean('is_default')) {
            $address->setAsDefault();
        }

        return back()->with('success', 'Address updated successfully.');
    }

    public function deleteAddress(Address $address)
    {
        $this->authorize('delete', $address);
        $address->delete();

        return back()->with('success', 'Address deleted successfully.');
    }
}
