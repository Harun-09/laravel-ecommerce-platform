<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\ECommerce\Models\OrderStatusHistory;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
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
        $query = Order::with(['buyer', 'supplierOrders.supplier', 'items']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                    ->orWhereHas('buyer', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('vendor')) {
            $query->whereHas('supplierOrders', function($q) use ($request) {
                $q->where('supplier_id', $request->vendor);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);
        $vendors = Supplier::where('status', SupplierStatus::Approved->value)->get();

        $stats = [
            'pending' => Order::where('status', OrderStatus::Pending->value)->count(),
            'paid' => Order::where('payment_status', PaymentStatus::Completed->value)->count(),
            'shipped' => Order::where('status', OrderStatus::Shipped->value)->count(),
            'delivered' => Order::where('status', OrderStatus::Completed->value)->count(),
        ];

        return view('admin.orders.index', compact('orders', 'vendors', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load([
            'buyer',
            'supplierOrders.supplier',
            'items.product',
            'items.variation',
            'payments',
            'returnRequests',
            'statusHistories' => fn($query) => $query->latest(),
        ]);

        $allowedNextStatuses = array_column(OrderStatus::cases(), 'value');

        return view('admin.orders.show', compact('order', 'allowedNextStatuses'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'string', Rule::in(array_column(OrderStatus::cases(), 'value'))],
            'comment' => 'nullable|string|max:500',
            'tracking_number' => 'nullable|string|max:100',
            'shipping_carrier' => 'nullable|string|max:100',
        ]);

        $normalizedStatus = $request->status;

        if (
            $normalizedStatus === OrderStatus::Shipped->value &&
            !$request->filled('tracking_number') &&
            empty($order->tracking_number)
        ) {
            return back()->with('error', 'Tracking number is required before marking an order as shipped.');
        }

        $updateData = ['status' => $normalizedStatus];

        if ($request->filled('tracking_number')) {
            $updateData['tracking_number'] = $request->tracking_number;
            $updateData['shipping_carrier'] = $request->shipping_carrier;
        }

        $oldStatus = collect(OrderStatus::cases())->firstWhere('value', $order->status->value ?? $order->status)?->value ?? $order->status;
        $order->update($updateData);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $normalizedStatus,
            'comment' => $request->comment,
            'notify_customer' => true,
        ]);

        return back()->with('success', 'Order status updated.');
    }

    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $oldStatus = collect(OrderStatus::cases())->firstWhere('value', $order->status->value ?? $order->status)?->value ?? $order->status;
        $order->update(['status' => OrderStatus::Cancelled->value]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => OrderStatus::Cancelled->value,
            'comment' => $request->reason,
            'notify_customer' => true,
        ]);

        return back()->with('success', 'Order cancelled.');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => ['required', Rule::in(array_column(PaymentStatus::cases(), 'value'))],
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $refundAmount = (float) ($request->refund_amount ?? 0);
        $paymentStatus = $request->payment_status;

        if ($paymentStatus === PaymentStatus::Refunded->value) {
            $refundAmount = $refundAmount > 0 ? min($refundAmount, (float) $order->grand_total) : (float) $order->grand_total;
        }

        if ($paymentStatus === PaymentStatus::PartiallyRefunded->value) {
            if ($refundAmount <= 0) {
                return back()->with('error', 'Refund amount is required for partially refunded status.');
            }

            if ($refundAmount > (float) $order->grand_total) {
                return back()->with('error', 'Refund amount cannot be greater than order total.');
            }
        }

        if (!in_array($paymentStatus, [PaymentStatus::Refunded->value, PaymentStatus::PartiallyRefunded->value], true)) {
            $refundAmount = 0;
        }

        $order->update([
            'payment_status' => $paymentStatus,
            'refunded_amount' => $refundAmount,
        ]);

        if (in_array($paymentStatus, [PaymentStatus::Refunded->value, PaymentStatus::PartiallyRefunded->value], true) && $refundAmount > 0) {
            if (class_exists(OrderNotificationService::class) && method_exists(OrderNotificationService::class, 'sendOrderRefunded')) {
                app(OrderNotificationService::class)->sendOrderRefunded($order->fresh('buyer'), $refundAmount);
            }
        }

        return back()->with('success', 'Payment status updated.');
    }
}
