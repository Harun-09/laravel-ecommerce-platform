<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\AuditLog;
use App\Domains\ECommerce\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view reports');
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->hasRole('super-admin'), 403, 'Only super-admin can access audit logs.');

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $query = AuditLog::query()
            ->with(['actor:id,name', 'vendor:id,shop_name'])
            ->latest();

        if ($request->filled('event')) {
            $query->where('event', trim((string) $request->event));
        }

        if ($request->filled('vendor_id') && is_numeric($request->vendor_id)) {
            $query->where('vendor_id', (int) $request->vendor_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('actor')) {
            $actor = trim((string) $request->actor);
            $query->whereHas('actor', fn($builder) => $builder->where('name', 'like', "%{$actor}%"));
        }

        $logs = $query->paginate(30)->withQueryString();
        $vendors = Vendor::approved()->orderBy('shop_name')->get(['id', 'shop_name']);

        $events = AuditLog::query()
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        return view('admin.audit-logs.index', compact('logs', 'vendors', 'events'));
    }
}
