<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Vendor;
use App\Http\Controllers\Frontend;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\Frontend\WebSetupController;
use App\Http\Controllers\SupplierOnboardingController;
use Inertia\Inertia;

use App\Http\Controllers\RfqRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\Supplier\AnalyticsController as SupplierAnalyticsController;
use App\Domains\ECommerce\Models\Order;
use App\Http\Controllers\OrderSubscriptionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AdminTrashController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])->name('auth.social.redirect');
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('auth.social.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

if ((bool) env('WEB_SETUP_ENABLED', false)) {
    Route::get('/__setup/{token}', [WebSetupController::class, 'run'])->name('web-setup.run');
}

// Storefront Preferences
Route::post('/preferences/language', [Frontend\PreferenceController::class, 'updateLanguage'])->name('preferences.language');
Route::post('/preferences/currency', [Frontend\PreferenceController::class, 'updateCurrency'])->name('preferences.currency');
Route::post('/deals/subscribe', [Frontend\DealSubscriptionController::class, 'subscribe'])->name('deals.subscribe');

// Frontend Routes
Route::get('/', [Frontend\HomeController::class, 'index'])->name('home');
Route::get('/about', [Frontend\HomeController::class, 'about'])->name('about');
Route::get('/contact', [Frontend\HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [Frontend\HomeController::class, 'submitContact'])->name('contact.submit');
Route::get('/credit-cards', [Frontend\HomeController::class, 'creditCards'])->name('credit-cards');
Route::get('/gift-cards', [Frontend\HomeController::class, 'giftCards'])->name('gift-cards');
Route::get('/page/{slug}', [Frontend\HomeController::class, 'page'])->name('page.show');

// Products
Route::get('/products', [Frontend\ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [Frontend\ProductController::class, 'search'])->name('products.search');
Route::get('/products/suggestions', [Frontend\ProductController::class, 'suggestions'])->name('products.suggestions');
Route::get('/products/recently-viewed', [Frontend\ProductController::class, 'recentlyViewed'])->name('products.recently-viewed');
Route::post('/products/{product:slug}/reviews', [Frontend\ProductController::class, 'storeReview'])
    ->name('products.reviews.store')
    ->middleware(['auth', \App\Http\Middleware\RedirectAdminFromFrontend::class]);
Route::post('/products/{product:slug}/questions', [Frontend\ProductController::class, 'storeQuestion'])
    ->name('products.questions.store')
    ->middleware(['auth', \App\Http\Middleware\RedirectAdminFromFrontend::class]);
Route::post('/ai-assistant/query', [Frontend\AiAssistantController::class, 'query'])->name('ai-assistant.query');
Route::get('/products/{slug}', [Frontend\ProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [Frontend\ProductController::class, 'category'])->name('category.show');
Route::get('/products/{product}/quick-view', [Frontend\ProductController::class, 'quickView'])->name('products.quick-view');

// Public checkout success page (supports secure token fallback when session is lost after gateway callback)
Route::get('/checkout/success/{orderNumber}', [Frontend\CheckoutController::class, 'success'])->name('checkout.success');

// Cart
Route::get('/cart', [Frontend\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [Frontend\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [Frontend\CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [Frontend\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/apply-coupon', [Frontend\CartController::class, 'applyCoupon'])->name('cart.apply-coupon');
Route::post('/cart/remove-coupon', [Frontend\CartController::class, 'removeCoupon'])->name('cart.remove-coupon');
Route::get('/cart/count', [Frontend\CartController::class, 'getCartCount'])->name('cart.count');

// Checkout (customer permission required)
Route::middleware(['auth', \App\Http\Middleware\CustomerCheckoutMiddleware::class, 'can:create orders'])->group(function () {
    Route::get('/checkout', [Frontend\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [Frontend\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/shipping-methods', [Frontend\CheckoutController::class, 'getShippingMethods'])->name('checkout.shipping-methods');

    // Payment Routes
    Route::get('/payment/process/{orderNumber}', [PaymentController::class, 'process'])->name('payment.process');

    // --- SSLCOMMERZ cancel route (GET — user's browser navigates here, session intact) ---
    Route::get('/payment/sslcommerz/cancel/{orderNumber}', [PaymentController::class, 'sslcommerzCancel'])->name('payment.sslcommerz.cancel');

    Route::get('/payment/stripe/success/{orderNumber}', [PaymentController::class, 'stripeSuccess'])->name('payment.stripe.success');
    Route::get('/payment/stripe/cancel/{orderNumber}', [PaymentController::class, 'stripeCancel'])->name('payment.stripe.cancel');
});

// SSLCOMMERZ POST callbacks (no auth — SSLCOMMERZ server sends these, no user session)
Route::post('/payment/sslcommerz/success/{orderNumber}', [PaymentController::class, 'sslcommerzSuccess'])->name('payment.sslcommerz.success');
Route::post('/payment/sslcommerz/fail/{orderNumber}', [PaymentController::class, 'sslcommerzFail'])->name('payment.sslcommerz.fail');

// SSLCOMMERZ IPN webhook (no auth, no CSRF)
Route::post('/payment/sslcommerz/ipn', [PaymentController::class, 'sslcommerzIPN'])->name('payment.sslcommerz.ipn');

Route::post('/payment/stripe/webhook', [PaymentController::class, 'stripeWebhook'])->name('payment.stripe.webhook');

// Wishlist (customer permission required)
Route::middleware(['auth', \App\Http\Middleware\RedirectAdminFromFrontend::class, 'can:manage wishlist'])->prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [Frontend\WishlistController::class, 'index'])->name('index');
    Route::post('/toggle', [Frontend\WishlistController::class, 'toggle'])->name('toggle');
    Route::post('/remove', [Frontend\WishlistController::class, 'remove'])->name('remove');
});

Route::post('/vendors/{vendor}/follow', [Frontend\VendorFollowController::class, 'toggle'])
    ->name('vendors.follow.toggle')
    ->middleware(['auth', \App\Http\Middleware\RedirectAdminFromFrontend::class]);

// Customer account (permission-based)
Route::middleware(['auth', \App\Http\Middleware\RedirectAdminFromFrontend::class])->prefix('account')->name('account.')->group(function () {
    Route::get('/', [Frontend\AccountController::class, 'dashboard'])->name('dashboard')
        ->middleware('can:view customer dashboard');

    Route::get('/orders', [Frontend\AccountController::class, 'orders'])->name('orders')
        ->middleware('can:view own orders');
    Route::get('/orders/{orderNumber}', [Frontend\AccountController::class, 'orderDetail'])->name('orders.detail')
        ->middleware('can:view own orders');
    Route::post('/orders/{orderNumber}/cancel', [Frontend\AccountController::class, 'cancelOrder'])->name('orders.cancel')
        ->middleware('can:cancel own orders');
    Route::get('/orders/{orderNumber}/invoice/a4', [Frontend\InvoiceController::class, 'a4'])->name('orders.invoice.a4')
        ->middleware('can:view own orders');
    Route::get('/orders/{orderNumber}/receipt/thermal', [Frontend\InvoiceController::class, 'thermal'])->name('orders.receipt.thermal')
        ->middleware('can:view own orders');
    Route::post('/orders/{orderNumber}/returns', [Frontend\ReturnRequestController::class, 'store'])->name('orders.returns.store')
        ->middleware('can:view own orders');

    Route::get('/returns', [Frontend\ReturnRequestController::class, 'index'])->name('returns')
        ->middleware('can:view own orders');
    Route::get('/returns/{returnRequest}', [Frontend\ReturnRequestController::class, 'show'])->name('returns.show')
        ->middleware('can:view own orders');

    Route::get('/profile', [Frontend\AccountController::class, 'profile'])->name('profile')
        ->middleware('can:manage profile');
    Route::put('/profile', [Frontend\AccountController::class, 'updateProfile'])->name('profile.update')
        ->middleware('can:manage profile');
    Route::get('/change-password', [Frontend\AccountController::class, 'changePassword'])->name('password')
        ->middleware('can:manage profile');
    Route::put('/change-password', [Frontend\AccountController::class, 'updatePassword'])->name('password.update')
        ->middleware('can:manage profile');

    Route::get('/addresses', [Frontend\AccountController::class, 'addresses'])->name('addresses')
        ->middleware('can:manage addresses');
    Route::post('/addresses', [Frontend\AccountController::class, 'storeAddress'])->name('addresses.store')
        ->middleware('can:manage addresses');
    Route::put('/addresses/{address}', [Frontend\AccountController::class, 'updateAddress'])->name('addresses.update')
        ->middleware('can:manage addresses');
    Route::delete('/addresses/{address}', [Frontend\AccountController::class, 'deleteAddress'])->name('addresses.delete')
        ->middleware('can:manage addresses');
});

// Admin Routes
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard')
            ->middleware('can:view dashboard');

        // Notifications
        Route::get('notifications', [Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/feed', [Admin\NotificationController::class, 'feed'])->name('notifications.feed');
        Route::post('notifications/mark-all-read', [Admin\NotificationController::class, 'markAllRead'])
            ->name('notifications.read-all');
        Route::post('notifications/{notification}/mark-read', [Admin\NotificationController::class, 'markAsRead'])
            ->name('notifications.read');

        // Messages
        Route::get('messages', [Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/feed', [Admin\MessageController::class, 'feed'])->name('messages.feed');
        Route::post('messages/mark-all-read', [Admin\MessageController::class, 'markAllRead'])
            ->name('messages.read-all');
        Route::post('messages/{message}/mark-read', [Admin\MessageController::class, 'markAsRead'])
            ->name('messages.read');
        Route::post('messages/{message}/reply', [Admin\MessageController::class, 'reply'])
            ->name('messages.reply');

        // Users
        Route::resource('users', Admin\UserController::class);

        // Vendors
        Route::get('vendors', [Admin\VendorController::class, 'index'])->name('vendors.index');
        Route::get('vendors/{vendor}', [Admin\VendorController::class, 'show'])->name('vendors.show');
        Route::post('vendors/{vendor}/approve', [Admin\VendorController::class, 'approve'])->name('vendors.approve');
        Route::post('vendors/{vendor}/reject', [Admin\VendorController::class, 'reject'])->name('vendors.reject');
        Route::post('vendors/{vendor}/suspend', [Admin\VendorController::class, 'suspend'])->name('vendors.suspend');
        Route::put('vendors/{vendor}/commission', [Admin\VendorController::class, 'updateCommission'])->name('vendors.commission');
        Route::delete('vendors/{vendor}', [Admin\VendorController::class, 'destroy'])->name('vendors.destroy');

        // Categories
        Route::patch('categories/{category}/restore', [Admin\CategoryController::class, 'restore'])
            ->whereNumber('category')
            ->name('categories.restore');
        Route::delete('categories/{category}/force', [Admin\CategoryController::class, 'forceDestroy'])
            ->whereNumber('category')
            ->name('categories.force-destroy');
        Route::resource('categories', Admin\CategoryController::class)->except(['show']);

        // Banners
        Route::patch('banners/{banner}/restore', [Admin\BannerController::class, 'restore'])
            ->whereNumber('banner')
            ->name('banners.restore');
        Route::delete('banners/{banner}/force', [Admin\BannerController::class, 'forceDestroy'])
            ->whereNumber('banner')
            ->name('banners.force-destroy');
        Route::resource('banners', Admin\BannerController::class)->except(['show']);

        // Products
        Route::get('products', [Admin\ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [Admin\ProductController::class, 'show'])->name('products.show');
        Route::post('products/{product}/images', [Admin\ProductImageController::class, 'store'])->name('products.images.store');
        Route::put('products/{product}/images/{image}', [Admin\ProductImageController::class, 'update'])
            ->whereNumber('image')
            ->name('products.images.update');
        Route::delete('products/{product}/images/{image}', [Admin\ProductImageController::class, 'destroy'])
            ->whereNumber('image')
            ->name('products.images.destroy');
        Route::patch('products/{product}/images/{image}/restore', [Admin\ProductImageController::class, 'restore'])
            ->whereNumber('image')
            ->name('products.images.restore');
        Route::delete('products/{product}/images/{image}/force', [Admin\ProductImageController::class, 'forceDestroy'])
            ->whereNumber('image')
            ->name('products.images.force-destroy');
        Route::post('products/{product}/approve', [Admin\ProductController::class, 'approve'])->name('products.approve');
        Route::post('products/{product}/reject', [Admin\ProductController::class, 'reject'])->name('products.reject');
        Route::post('products/{product}/toggle-featured', [Admin\ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
        Route::delete('products/{product}', [Admin\ProductController::class, 'destroy'])->name('products.destroy');

        // Orders
        Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('orders/{order}/cancel', [Admin\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::put('orders/{order}/payment-status', [Admin\OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
        Route::get('orders/{order}/invoice/a4', [Admin\InvoiceController::class, 'a4'])->name('orders.invoice.a4');
        Route::get('orders/{order}/receipt/thermal', [Admin\InvoiceController::class, 'thermal'])->name('orders.receipt.thermal');

        // Returns / RMA
        Route::get('returns', [Admin\ReturnRequestController::class, 'index'])->name('returns.index');
        Route::get('returns/{returnRequest}', [Admin\ReturnRequestController::class, 'show'])->name('returns.show');
        Route::put('returns/{returnRequest}/status', [Admin\ReturnRequestController::class, 'updateStatus'])->name('returns.update-status');

        // Payout Operations
        Route::get('payouts', [Admin\VendorPayoutController::class, 'index'])->name('payouts.index');
        Route::post('payouts', [Admin\VendorPayoutController::class, 'store'])->name('payouts.store');
        Route::get('payouts/{payout}', [Admin\VendorPayoutController::class, 'show'])->name('payouts.show');
        Route::patch('payouts/{payout}/process', [Admin\VendorPayoutController::class, 'process'])->name('payouts.process');

        // Review Moderation
        Route::get('reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::get('reviews/{review}', [Admin\ReviewController::class, 'show'])->name('reviews.show');
        Route::patch('reviews/{review}/approve', [Admin\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('reviews/{review}/reject', [Admin\ReviewController::class, 'reject'])->name('reviews.reject');
        Route::put('reviews/{review}/reply', [Admin\ReviewController::class, 'reply'])->name('reviews.reply');
        Route::delete('reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

        // Reports
        Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [Admin\ReportController::class, 'export'])->name('reports.export');
        Route::get('audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('observability', [Admin\ObservabilityController::class, 'index'])->name('observability.index');
        Route::patch('observability/alerts/{alert}/resolve', [Admin\ObservabilityController::class, 'resolveAlert'])
            ->name('observability.alerts.resolve');

        // Courier & Shipping
        Route::get('shipping', [Admin\ShippingController::class, 'index'])->name('shipping.index');
        Route::post('shipping/zones', [Admin\ShippingController::class, 'storeZone'])->name('shipping.zones.store');
        Route::put('shipping/zones/{shippingZone}', [Admin\ShippingController::class, 'updateZone'])->name('shipping.zones.update');
        Route::delete('shipping/zones/{shippingZone}', [Admin\ShippingController::class, 'destroyZone'])->name('shipping.zones.destroy');
        Route::post('shipping/methods', [Admin\ShippingController::class, 'storeMethod'])->name('shipping.methods.store');
        Route::put('shipping/methods/{shippingMethod}', [Admin\ShippingController::class, 'updateMethod'])->name('shipping.methods.update');
        Route::delete('shipping/methods/{shippingMethod}', [Admin\ShippingController::class, 'destroyMethod'])->name('shipping.methods.destroy');
    });

// Vendor Pending Page (for unapproved vendors)
Route::middleware('auth')->get('/vendor/pending', function () {
    return view('vendor.pending');
})->name('vendor.pending');

// Vendor Routes
Route::middleware(['auth', \App\Http\Middleware\VendorMiddleware::class, 'can:view vendor dashboard'])
    ->prefix('vendor')
    ->name('vendor.')
    ->group(function () {
        Route::get('/', function () {
            return redirect()->route('vendor.dashboard');
        });
        Route::get('/dashboard', function () {
            $user = auth()->user();
            $vendor = $user->vendor;

            $stats = [
                'total_products' => \App\Models\Product::query()->forCurrentVendor($user)->count(),
                'active_products' => \App\Models\Product::query()->forCurrentVendor($user)->active()->count(),
                'total_orders' => \App\Models\Order::query()->forCurrentVendor($user)->count(),
                'pending_orders' => \App\Models\Order::query()->forCurrentVendor($user)->pending()->count(),
                'total_sales' => \App\Models\Order::query()->forCurrentVendor($user)->paid()->sum('total'),
                'pending_payout' => $vendor->getPendingBalance(),
            ];
            $recentOrders = \App\Models\Order::query()
                ->forCurrentVendor($user)
                ->with('user')
                ->latest()
                ->take(10)
                ->get();

            $payoutLedger = $vendor->getPendingPayoutLedger(10);
            $payoutSummary = [
                'gross' => $payoutLedger->sum('total'),
                'commission' => $payoutLedger->sum('commission_amount'),
                'refund' => $payoutLedger->sum('refunded_amount'),
                'payable' => $payoutLedger->sum(fn($order) => $order->payout_payable_amount),
            ];

            return view('vendor.dashboard', compact('vendor', 'stats', 'recentOrders', 'payoutLedger', 'payoutSummary'));
        })->name('dashboard');
        Route::get('/reports', [Vendor\ReportController::class, 'index'])->name('reports.index')
            ->middleware('can:view reports');
        Route::get('/reports/export', [Vendor\ReportController::class, 'export'])->name('reports.export')
            ->middleware('can:view reports');
        Route::get('/orders', [Vendor\OrderController::class, 'index'])->name('orders.index')
            ->middleware('can:view orders');
        Route::get('/orders/{order}', [Vendor\OrderController::class, 'show'])->name('orders.show')
            ->middleware('can:view orders');
        Route::put('/orders/{order}/status', [Vendor\OrderController::class, 'updateStatus'])->name('orders.update-status')
            ->middleware('can:process orders');
        Route::post('/orders/{order}/cancel', [Vendor\OrderController::class, 'cancel'])->name('orders.cancel')
            ->middleware('can:process orders');
    });

Route::middleware('guest')->group(function (): void {
    Route::get('/supplier/apply', [SupplierOnboardingController::class, 'create'])
        ->name('supplier.apply');

    Route::post('/supplier/apply', [SupplierOnboardingController::class, 'store'])
        ->name('supplier.apply.store');

    Route::get('/register-supplier', [SupplierOnboardingController::class, 'create'])
        ->name('supplier.register');

    Route::post('/register-supplier', [SupplierOnboardingController::class, 'store'])
        ->name('supplier.register.store');
        
    Route::get('/b2b/register', function () {
        return Inertia\Inertia::render('Auth/B2BRegisterPlaceholder');
    })->name('b2b.register');
});

Route::get('/checkout/success/{orderNumber}', [\App\Http\Controllers\PaymentController::class, 'checkoutSuccess'])
    ->name('checkout.success');

Route::get('/rfq', [RfqRequestController::class, 'create'])
    ->name('rfq.create');

Route::get('/rfq/{product:slug}', [RfqRequestController::class, 'create'])
    ->name('rfq.product');

Route::post('/rfq', [RfqRequestController::class, 'store'])
    ->name('rfq.store');

Route::middleware('auth')->group(function () {
    Route::post('/checkout/{orderNumber}/payment', [\App\Http\Controllers\PaymentController::class, 'process'])
        ->name('payment.process');
});

Route::prefix('payments')->name('payment.')->group(function (): void {
    Route::get('/stripe/{orderNumber}/success', [\App\Http\Controllers\PaymentController::class, 'stripeSuccess'])
        ->name('stripe.success');

    Route::get('/stripe/{orderNumber}/cancel', [\App\Http\Controllers\PaymentController::class, 'stripeCancel'])
        ->name('stripe.cancel');

    Route::post('/stripe/webhook', [\App\Http\Controllers\PaymentController::class, 'stripeWebhook'])
        ->name('stripe.webhook');

    Route::match(['get', 'post'], '/sslcommerz/{orderNumber}/success', [\App\Http\Controllers\PaymentController::class, 'sslcommerzSuccess'])
        ->name('sslcommerz.success');

    Route::match(['get', 'post'], '/sslcommerz/{orderNumber}/fail', [\App\Http\Controllers\PaymentController::class, 'sslcommerzFail'])
        ->name('sslcommerz.fail');

    Route::match(['get', 'post'], '/sslcommerz/{orderNumber}/cancel', [\App\Http\Controllers\PaymentController::class, 'sslcommerzCancel'])
        ->name('sslcommerz.cancel');

    Route::post('/sslcommerz/ipn', [\App\Http\Controllers\PaymentController::class, 'sslcommerzIPN'])
        ->name('sslcommerz.ipn');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth:b2c')->group(function () {
    Route::get('/b2c/dashboard', [\App\Http\Controllers\B2CDashboardController::class, 'index'])->name('b2c.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::redirect('/customer/profile', '/profile')->name('customer.profile.alias');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/customer', [ProfileController::class, 'updateCustomer'])->name('profile.customer.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('supplier')->name('supplier.')->middleware('role:supplier|admin')->group(function (): void {
        Route::get('/products', [WorkspaceController::class, 'supplierProducts'])->name('products.index');
        Route::get('/products/create', [WorkspaceController::class, 'supplierProductCreate'])->middleware('role:supplier')->name('products.create');
        Route::get('/products/{product}/edit', [WorkspaceController::class, 'supplierProductEdit'])->middleware('role:supplier')->name('products.edit');
        Route::redirect('/rfq-responses', '/commerce/rfq-responses')->name('rfq-responses.alias');
        Route::get('/analytics', [\App\Http\Controllers\Supplier\AnalyticsController::class, 'index'])->name('analytics');
    });

    Route::redirect('/buyer/rfq-quotes', '/commerce/rfq-quotes')
        ->middleware('role:buyer|admin')
        ->name('buyer.rfq-quotes.alias');

    Route::redirect('/buyer/tickets', '/support/tickets')->name('buyer.tickets.alias');

    Route::redirect('/orders', '/commerce/orders')->name('orders.index.alias');
    Route::get('/orders/{order}', function (Order $order) {
        return redirect()->route('commerce.orders.index', ['search' => $order->order_number]);
    })->middleware('role:buyer|supplier|admin')->name('orders.show.alias');

    // Subscription & Repeat Orders
    Route::post('/orders/{order}/repeat', [\App\Http\Controllers\OrderSubscriptionController::class, 'repeat'])->name('orders.repeat');
    Route::post('/orders/{order}/toggle-subscription', [\App\Http\Controllers\OrderSubscriptionController::class, 'toggle'])->name('orders.toggle-subscription');

    // Invoice Routes
    Route::prefix('invoices')->name('invoices.')->group(function (): void {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::post('/generate/{orderId}', [InvoiceController::class, 'generate'])->name('generate');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])->name('download');
        Route::get('/{invoice}/preview', [InvoiceController::class, 'stream'])->name('preview');
    });

    Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');

    Route::middleware('role:admin')->prefix('admin/trash')->name('admin.trash.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminTrashController::class, 'index'])->name('index');
        Route::post('/{type}/{id}/restore', [\App\Http\Controllers\AdminTrashController::class, 'restore'])->name('restore');
        Route::delete('/{type}/{id}/force-delete', [\App\Http\Controllers\AdminTrashController::class, 'forceDelete'])->name('force-delete');
    });
});

Route::get('/template/{name}', function ($name) {
    $componentName = str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
    if ($componentName === 'Index') $componentName = 'Home';
    return Inertia::render('Frontend/' . $componentName);
})->name('template.page');

Route::get('/{name}.html', function ($name) {
    $componentName = str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
    if ($componentName === 'Index') $componentName = 'Home';
    return Inertia::render('Frontend/' . $componentName);
})->name('template.html.page');

require __DIR__.'/auth.php';
