<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Product;
use App\Models\User;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view dashboard')->only(['index']);
    }

    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', OrderStatus::Pending->value)->count(),
            'total_revenue' => Order::where('payment_status', PaymentStatus::Completed->value)->sum('grand_total'),
            'total_products' => Product::active()->count(),
            'total_vendors' => Supplier::where('status', SupplierStatus::Approved->value)->count(),
            'pending_vendors' => Supplier::where('status', SupplierStatus::Pending->value)->count(),
            'total_customers' => User::role('customer')->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())
                ->where('payment_status', PaymentStatus::Completed->value)
                ->sum('grand_total'),
        ];

        // Recent orders
        $recentOrders = Order::with(['user', 'supplierOrders.supplier'])
            ->latest()
            ->take(10)
            ->get();

        // Monthly revenue chart data
        $monthlyRevenue = Order::where('payment_status', PaymentStatus::Completed->value)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(grand_total) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top selling products
        $topProducts = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'monthlyRevenue', 'topProducts'));
    }
}
