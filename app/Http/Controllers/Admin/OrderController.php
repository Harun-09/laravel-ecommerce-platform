<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Vendor;
use App\Services\OrderNotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view orders')->only(['index', 'show']);
        $this->middleware('can:process orders')->only(['updateStatus', 'cancel']);
        $this->middleware('can:process payments')->only(['updatePaymentStatus']);
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'vendor', 'items']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', Order::normalizeStatus($request->status));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('vendor')) {
            $query->where('vendor_id', $request->vendor);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);
        $vendors = Vendor::approved()->get();

        $stats = [
            'pending' => Order::pending()->count(),
            'paid' => Order::paidStatus()->count(),
            'shipped' => Order::shipped()->count(),
            'delivered' => Order::delivered()->count(),
        ];

        return view('admin.orders.index', compact('orders', 'vendors', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'vendor',
            'items.product',
            'items.variation',
            'payments',
            'returnRequests',
            'statusHistories' => fn($query) => $query->with('user')->latest(),
        ]);

        $allowedNextStatuses = $order->getAllowedNextStatuses();

        return view('admin.orders.show', compact('order', 'allowedNextStatuses'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'string', Rule::in(Order::allStatuses(true))],
            'comment' => 'nullable|string|max:500',
            'tracking_number' => 'nullable|string|max:100',
            'shipping_carrier' => 'nullable|string|max:100',
        ]);

        $normalizedStatus = Order::normalizeStatus($request->status);

        if (
            $normalizedStatus === Order::STATUS_SHIPPED &&
            !$request->filled('tracking_number') &&
            empty($order->tracking_number)
        ) {
            return back()->with('error', 'Tracking number is required before marking an order as shipped.');
        }

        if ($request->filled('tracking_number')) {
            $order->update([
                'tracking_number' => $request->tracking_number,
                'shipping_carrier' => $request->shipping_carrier,
            ]);
        }

        try {
            $order->updateStatus($normalizedStatus, auth()->user(), $request->comment, true);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Order status updated.');
    }

    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if ($order->cancel($request->reason, auth()->user())) {
            return back()->with('success', 'Order cancelled.');
        }

        return back()->with('error', 'Order cannot be cancelled.');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded,partially_refunded',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $refundAmount = (float) ($request->refund_amount ?? 0);
        $paymentStatus = $request->payment_status;

        if ($paymentStatus === 'refunded') {
            $refundAmount = $refundAmount > 0 ? min($refundAmount, (float) $order->total) : (float) $order->total;
        }

        if ($paymentStatus === 'partially_refunded') {
            if ($refundAmount <= 0) {
                return back()->with('error', 'Refund amount is required for partially refunded status.');
            }

            if ($refundAmount > (float) $order->total) {
                return back()->with('error', 'Refund amount cannot be greater than order total.');
            }
        }

        if (!in_array($paymentStatus, ['refunded', 'partially_refunded'], true)) {
            $refundAmount = 0;
        }

        $order->update([
            'payment_status' => $paymentStatus,
            'refunded_amount' => $refundAmount,
        ]);

        if ($paymentStatus === 'paid' && $order->status === Order::STATUS_PENDING) {
            try {
                $order->updateStatus(Order::STATUS_PAID, auth()->user(), 'Payment marked as paid by admin.', true);
            } catch (InvalidArgumentException $exception) {
                return back()->with('error', $exception->getMessage());
            }
        }

        if (
            in_array($paymentStatus, ['refunded', 'partially_refunded'], true) &&
            $order->canTransitionTo(Order::STATUS_RETURNED)
        ) {
            try {
                $order->updateStatus(Order::STATUS_RETURNED, auth()->user(), 'Order marked returned due to refund.', true);
            } catch (InvalidArgumentException) {
                // keep payment update even if lifecycle transition is not available
            }
        }

        if (in_array($paymentStatus, ['refunded', 'partially_refunded'], true) && $refundAmount > 0) {
            app(OrderNotificationService::class)->sendOrderRefunded($order->fresh('user'), $refundAmount);
        }

        return back()->with('success', 'Payment status updated.');
    }
}
