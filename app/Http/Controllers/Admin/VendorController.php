<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view vendors')->only(['index', 'show']);
        $this->middleware('can:approve vendors')->only(['approve', 'reject', 'suspend']);
        $this->middleware('can:edit vendors')->only(['updateCommission']);
        $this->middleware('can:delete vendors')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Vendor::with('user');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('shop_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vendors = $query->latest()->paginate(20);

        return view('admin.vendors.index', compact('vendors'));
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['user', 'products', 'orders']);

        $stats = [
            'total_products' => $vendor->products()->count(),
            'active_products' => $vendor->products()->active()->count(),
            'total_orders' => $vendor->orders()->count(),
            'pending_orders' => $vendor->orders()->pending()->count(),
            'total_sales' => $vendor->orders()->paid()->sum('total'),
            'total_earnings' => $vendor->orders()->paid()->sum('vendor_earning'),
            'pending_payout' => $vendor->getPendingBalance(),
        ];

        return view('admin.vendors.show', compact('vendor', 'stats'));
    }

    public function approve(Vendor $vendor)
    {
        $vendor->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Vendor approved successfully.');
    }

    public function reject(Request $request, Vendor $vendor)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $vendor->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return back()->with('success', 'Vendor rejected.');
    }

    public function suspend(Vendor $vendor)
    {
        $vendor->update(['status' => 'suspended']);

        return back()->with('success', 'Vendor suspended.');
    }

    public function updateCommission(Request $request, Vendor $vendor)
    {
        $request->validate([
            'commission_type' => 'required|in:percentage,fixed',
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $vendor->update([
            'commission_type' => $request->commission_type,
            'commission_rate' => $request->commission_rate,
        ]);

        return back()->with('success', 'Commission updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }
}
