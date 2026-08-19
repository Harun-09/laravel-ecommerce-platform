<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Product;
use App\Models\User;
use App\Domains\ECommerce\Models\Vendor;
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
            'pending_orders' => Order::pending()->count(),
            'total_revenue' => Order::paid()->sum('total'),
            'total_products' => Product::active()->count(),
            'total_vendors' => Vendor::approved()->count(),
            'pending_vendors' => Vendor::pending()->count(),
            'total_customers' => User::role('customer')->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())->paid()->sum('total'),
        ];

        // Recent orders
        $recentOrders = Order::with(['user', 'vendor'])
            ->latest()
            ->take(10)
            ->get();

        // Monthly revenue chart data
        $monthlyRevenue = Order::paid()
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as revenue')
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
