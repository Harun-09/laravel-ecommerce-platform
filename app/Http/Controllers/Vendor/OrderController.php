<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view orders')->only(['index', 'show']);
        $this->middleware('can:process orders')->only(['updateStatus', 'cancel']);
    }

    public function index(Request $request): View
    {
        $vendorId = $this->vendorId();

        $query = Order::query()
            ->where('vendor_id', $vendorId)
            ->with(['user', 'items']);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', Order::normalizeStatus((string) $request->status));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', (string) $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', (string) $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', (string) $request->date_to);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $baseStatsQuery = Order::query()->where('vendor_id', $vendorId);
        $stats = [
            'pending' => (clone $baseStatsQuery)->where('status', Order::STATUS_PENDING)->count(),
            'confirmed' => (clone $baseStatsQuery)->where('status', Order::STATUS_PAID)->count(),
            'processing' => (clone $baseStatsQuery)->where('status', Order::STATUS_PROCESSING)->count(),
            'shipped' => (clone $baseStatsQuery)->where('status', Order::STATUS_SHIPPED)->count(),
            'delivered' => (clone $baseStatsQuery)->where('status', Order::STATUS_DELIVERED)->count(),
        ];

        return view('vendor.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order): View
    {
        $order = $this->resolveVendorOrder($order);

        $order->load([
            'user',
            'items.product',
            'items.variation',
            'returnRequests',
            'statusHistories' => fn($query) => $query->with('user')->latest(),
        ]);

        $allowedNextStatuses = $order->getAllowedNextStatuses();

        return view('vendor.orders.show', compact('order', 'allowedNextStatuses'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $order = $this->resolveVendorOrder($order);

        $request->validate([
            'status' => ['required', 'string', Rule::in(Order::allStatuses(true))],
            'comment' => 'nullable|string|max:500',
            'tracking_number' => 'nullable|string|max:100',
            'shipping_carrier' => 'nullable|string|max:100',
        ]);

        $normalizedStatus = Order::normalizeStatus((string) $request->status);

        if (
            $normalizedStatus === Order::STATUS_SHIPPED &&
            !$request->filled('tracking_number') &&
            empty($order->tracking_number)
        ) {
            return back()->with('error', 'Tracking number is required before marking an order as shipped.');
        }

        if ($request->filled('tracking_number')) {
            $order->update([
                'tracking_number' => (string) $request->tracking_number,
                'shipping_carrier' => (string) $request->shipping_carrier,
            ]);
        }

        try {
            $order->updateStatus($normalizedStatus, $this->actor(), (string) $request->comment, true);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Order status updated.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $order = $this->resolveVendorOrder($order);

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if ($order->cancel($request->reason, $this->actor())) {
            return back()->with('success', 'Order cancelled.');
        }

        return back()->with('error', 'Order cannot be cancelled.');
    }

    private function resolveVendorOrder(Order $order): Order
    {
        if ((int) $order->vendor_id !== $this->vendorId()) {
            abort(404);
        }

        return $order;
    }

    private function vendorId(): int
    {
        $vendorId = (int) (auth()->user()?->vendor?->id ?? 0);
        abort_if($vendorId <= 0, 403, 'Vendor account not found.');

        return $vendorId;
    }

    private function actor(): ?User
    {
        return auth()->user();
    }
}

