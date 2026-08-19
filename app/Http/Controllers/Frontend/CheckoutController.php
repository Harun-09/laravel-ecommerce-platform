<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\ShippingMethod;
use App\Domains\ECommerce\Models\ShippingZone;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\StripeGatewayService;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected OrderService $orderService;
    protected SslCommerzService $sslCommerz;
    protected StripeGatewayService $stripeGateway;

    public function __construct(
        CartService $cartService,
        OrderService $orderService,
        SslCommerzService $sslCommerz,
        StripeGatewayService $stripeGateway
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->sslCommerz = $sslCommerz;
        $this->stripeGateway = $stripeGateway;
    }

    public function index()
    {
        $cart = $this->cartService->getCart();
        $cart->refreshAppliedCoupon();
        $cart->loadMissing('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = auth()->user();
        $addresses = $user ? $user->addresses : collect();
        $selectedCity = old(
            'shipping_city',
            optional($addresses->firstWhere('is_default', true))->city
            ?? optional($addresses->first())->city
            ?? 'Dhaka'
        );
        $isSslcommerzConfigured = $this->sslCommerz->isConfigured();
        $isStripeConfigured = $this->stripeGateway->isConfigured();
        $selectedPaymentMethod = old('payment_method', 'cod');

        if (
            ($selectedPaymentMethod === 'sslcommerz' && !$isSslcommerzConfigured)
            || ($selectedPaymentMethod === 'stripe' && !$isStripeConfigured)
        ) {
            $selectedPaymentMethod = 'cod';
        }
        $shippingZone = ShippingZone::resolveByCity($selectedCity);

        $shippingMethods = $shippingZone
            ? $shippingZone->methods()->active()->ordered()->get()
            : collect();

        if ($shippingMethods->isEmpty()) {
            $shippingMethods = ShippingMethod::active()->ordered()->get();
        }

        $selectedShippingMethod = $shippingMethods->firstWhere('id', (int) old('shipping_method'));
        if (!$selectedShippingMethod) {
            $selectedShippingMethod = $shippingMethods->first();
        }

        $shippingQuote = $this->buildShippingQuote(
            $cart,
            $selectedShippingMethod,
            $selectedPaymentMethod === 'cod'
        );

        return view('frontend.checkout.index', compact(
            'cart',
            'addresses',
            'shippingMethods',
            'shippingZone',
            'selectedCity',
            'selectedPaymentMethod',
            'selectedShippingMethod',
            'shippingQuote',
            'isSslcommerzConfigured',
            'isStripeConfigured'
        ));
    }

    public function process(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_email' => 'nullable|email|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:20',
            'shipping_method' => 'required|integer|exists:shipping_methods,id',
            'payment_method' => 'required|in:cod,sslcommerz,stripe',
            'customer_notes' => 'nullable|string|max:500',
        ]);

        $cart = $this->cartService->getCart();
        $cart->refreshAppliedCoupon();
        $cart->loadMissing('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $shippingZone = ShippingZone::resolveByCity($request->shipping_city);
        if (!$shippingZone) {
            return back()->withInput()->with('error', 'Delivery zone not found for selected city.');
        }

        $shippingMethod = ShippingMethod::query()
            ->active()
            ->whereKey((int) $request->shipping_method)
            ->where(function ($query) use ($shippingZone) {
                $query->where('shipping_zone_id', $shippingZone->id)
                    ->orWhereNull('shipping_zone_id');
            })
            ->first();

        if (!$shippingMethod) {
            return back()->withInput()->with('error', 'Selected shipping method is not available for your delivery zone.');
        }

        $isCod = $request->payment_method === 'cod';
        if ($isCod && !$shippingMethod->is_cod_available) {
            return back()->withInput()->with('error', 'Cash on delivery is not available for this shipping method.');
        }

        if ($request->payment_method === 'sslcommerz' && !$this->sslCommerz->isConfigured()) {
            return back()->withInput()->with('error', 'SSLCOMMERZ is not configured yet. Please choose Cash on Delivery.');
        }
        if ($request->payment_method === 'stripe' && !$this->stripeGateway->isConfigured()) {
            return back()->withInput()->with('error', 'Stripe is not configured yet. Please choose Cash on Delivery.');
        }

        $shippingQuote = $this->buildShippingQuote($cart, $shippingMethod, $isCod);

        $orderData = [
            ...$request->all(),
            'shipping_method' => $shippingMethod->name,
            'delivery_zone' => $shippingZone->name,
            'shipping_cost' => $shippingQuote['shipping_cost'],
            'cod_fee' => $shippingQuote['cod_fee'],
        ];

        try {
            $orders = $this->orderService->createOrders($cart, $orderData);
            $primaryOrder = $orders->first();

            if (!$primaryOrder) {
                return back()->with('error', 'Failed to create order records.');
            }

            $orderCount = $orders->count();

            // Handle payment
            if ($request->payment_method === 'cod') {
                $message = $orderCount > 1
                    ? "Orders placed successfully ({$orderCount} vendor orders)."
                    : 'Order placed successfully!';

                return redirect()->route('checkout.success', [
                    'orderNumber' => $primaryOrder->order_number,
                    'access_token' => (string) ($primaryOrder->checkout_token ?? ''),
                ])
                    ->with('success', $message);
            }

            // For online payment, redirect to payment gateway flow
            return redirect()->route('payment.process', $primaryOrder->order_number);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process order: ' . $e->getMessage());
        }
    }

    public function success(Request $request, string $orderNumber)
    {
        $accessToken = trim((string) $request->query('access_token', ''));
        $order = null;

        if (auth()->check()) {
            $order = auth()->user()
                ->orders()
                ->where('order_number', $orderNumber)
                ->first();
        }

        if (!$order && $accessToken !== '') {
            $order = Order::query()
                ->where('order_number', $orderNumber)
                ->where('checkout_token', $accessToken)
                ->first();
        }

        if (!$order) {
            if (auth()->check()) {
                abort(404);
            }

            return redirect()->route('login')
                ->with('error', 'Your session expired during payment verification. Please sign in and open your orders.');
        }

        $checkoutOrders = $this->resolveCheckoutOrdersForSuccess($order, $accessToken);
        $paymentSummary = $this->buildCheckoutPaymentSummary($checkoutOrders);

        return view('frontend.checkout.success', compact('order', 'checkoutOrders', 'paymentSummary'));
    }

    public function getShippingMethods(Request $request)
    {
        $city = trim((string) $request->query('city', ''));
        $paymentMethod = (string) $request->query('payment_method', 'cod');
        $paymentMethod = in_array($paymentMethod, ['cod', 'sslcommerz', 'stripe'], true) ? $paymentMethod : 'cod';

        $cart = $this->cartService->getCart();
        $cart->refreshAppliedCoupon();
        $cart->loadMissing('items.product');
        $cartCoupon = $cart->coupon();
        $zone = ShippingZone::resolveByCity($city);

        $methods = $zone
            ? $zone->methods()->active()->ordered()->get()
            : collect();

        if ($methods->isEmpty()) {
            $methods = ShippingMethod::active()->ordered()->get();
        }

        $isCod = $paymentMethod === 'cod';

        return response()->json([
            'coupon' => [
                'code' => $cartCoupon?->code,
                'type' => $cartCoupon?->normalizedType(),
                'is_free_shipping' => (bool) $cartCoupon?->isFreeShippingType(),
            ],
            'zone' => $zone ? [
                'id' => $zone->id,
                'name' => $zone->name,
                'code' => $zone->code,
                'is_inside_dhaka' => $zone->isInsideDhaka(),
            ] : null,
            'methods' => $methods->map(function ($m) use ($cart, $isCod) {
                $quote = $m->calculateQuote($cart, $isCod);

                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'description' => $m->description,
                    'is_cod_available' => $m->is_cod_available,
                    'shipping_cost' => $quote['shipping_cost'],
                    'shipping_discount' => $quote['shipping_discount'],
                    'cod_fee' => $quote['cod_fee'],
                    'total_cost' => $quote['total_shipping_cost'],
                    'applied_coupon_code' => $quote['applied_coupon_code'] ?? null,
                    'applied_coupon_type' => $quote['applied_coupon_type'] ?? null,
                    'is_free_shipping_applied' => (bool) ($quote['is_free_shipping_applied'] ?? false),
                    'estimated_days' => $m->estimated_days,
                ];
            }),
        ]);
    }

    private function buildShippingQuote(Cart $cart, ?ShippingMethod $method, bool $includeCodFee): array
    {
        if (!$method) {
            return [
                'shipping_cost' => 0.0,
                'shipping_discount' => 0.0,
                'cod_fee' => 0.0,
                'total_shipping_cost' => 0.0,
                'applied_coupon_code' => null,
                'applied_coupon_type' => null,
                'is_free_shipping_applied' => false,
            ];
        }

        return $method->calculateQuote($cart, $includeCodFee);
    }

    private function resolveCheckoutOrdersForSuccess(Order $order, ?string $accessToken = null): Collection
    {
        $checkoutToken = trim((string) $order->checkout_token);

        if ($checkoutToken === '') {
            return collect([
                $order->loadMissing(['items.product', 'vendor']),
            ]);
        }

        $ordersQuery = Order::query()
            ->where('checkout_token', $checkoutToken)
            ->with(['items.product', 'vendor'])
            ->orderBy('id');

        if (auth()->check()) {
            $ordersQuery->where('user_id', $order->user_id);
        } elseif ($accessToken === '' || $accessToken !== $checkoutToken) {
            return collect([
                $order->loadMissing(['items.product', 'vendor']),
            ]);
        }

        $orders = $ordersQuery->get();

        if ($orders->isNotEmpty()) {
            return $orders;
        }

        return collect([
            $order->loadMissing(['items.product', 'vendor']),
        ]);
    }

    private function buildCheckoutPaymentSummary(Collection $orders): array
    {
        $isOrderPaymentComplete = static function (Order $order): bool {
            if ((string) $order->payment_method === 'cod') {
                return true;
            }

            return (string) $order->payment_status === 'paid';
        };

        $pendingOrders = $orders
            ->filter(fn(Order $checkoutOrder) => !$isOrderPaymentComplete($checkoutOrder))
            ->values();

        $hasFailures = $orders->contains(function (Order $checkoutOrder): bool {
            if ((string) $checkoutOrder->payment_method === 'cod') {
                return false;
            }

            return (string) $checkoutOrder->payment_status === 'failed';
        });

        $isCompleted = $pendingOrders->isEmpty();

        $status = $isCompleted
            ? 'completed'
            : ($hasFailures ? 'failed' : 'pending');

        $retryOrder = $pendingOrders->first(function (Order $checkoutOrder): bool {
            return in_array((string) $checkoutOrder->payment_method, ['stripe', 'sslcommerz'], true);
        });

        $totalAmount = (float) $orders->sum(fn(Order $checkoutOrder) => (float) $checkoutOrder->total);
        $paidAmount = (float) $orders
            ->filter($isOrderPaymentComplete)
            ->sum(fn(Order $checkoutOrder) => (float) $checkoutOrder->total);

        return [
            'status' => $status,
            'is_completed' => $isCompleted,
            'is_split' => $orders->count() > 1,
            'total_orders' => (int) $orders->count(),
            'total_amount' => round($totalAmount, 2),
            'paid_amount' => round($paidAmount, 2),
            'pending_amount' => round(max(0, $totalAmount - $paidAmount), 2),
            'pending_orders' => $pendingOrders,
            'retry_order_number' => $retryOrder?->order_number,
        ];
    }
}
