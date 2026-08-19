<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\MonitoringAlert;
use App\Domains\ECommerce\Models\PaymentEventLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObservabilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view reports');
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->hasRole('super-admin'), 403, 'Only super-admin can access observability.');

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $alertsQuery = MonitoringAlert::query()->with('resolver:id,name')->latest('triggered_at');
        $eventsQuery = PaymentEventLog::query()
            ->with(['order:id,order_number', 'payment:id,transaction_id'])
            ->latest('happened_at');

        if ($request->filled('alert_status')) {
            $alertsQuery->where('status', trim((string) $request->alert_status));
        }

        if ($request->filled('alert_severity')) {
            $alertsQuery->where('severity', trim((string) $request->alert_severity));
        }

        if ($request->filled('event_provider')) {
            $eventsQuery->where('provider', trim((string) $request->event_provider));
        }

        if ($request->filled('event_status')) {
            $eventsQuery->where('status', trim((string) $request->event_status));
        }

        if ($request->filled('event_severity')) {
            $eventsQuery->where('severity', trim((string) $request->event_severity));
        }

        if ($request->filled('date_from')) {
            $dateFrom = (string) $request->date_from;
            $alertsQuery->whereDate('triggered_at', '>=', $dateFrom);
            $eventsQuery->whereDate('happened_at', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            $dateTo = (string) $request->date_to;
            $alertsQuery->whereDate('triggered_at', '<=', $dateTo);
            $eventsQuery->whereDate('happened_at', '<=', $dateTo);
        }

        $alerts = $alertsQuery->paginate(15, ['*'], 'alerts_page')->withQueryString();
        $events = $eventsQuery->paginate(25, ['*'], 'events_page')->withQueryString();

        $stats = [
            'open_alerts' => MonitoringAlert::query()->open()->count(),
            'critical_open_alerts' => MonitoringAlert::query()->open()->where('severity', 'critical')->count(),
            'payment_failures_24h' => PaymentEventLog::query()
                ->where('happened_at', '>=', now()->subDay())
                ->where(function ($query): void {
                    $query->where('status', 'failed')
                        ->orWhereIn('severity', ['error', 'critical']);
                })
                ->count(),
        ];

        $providers = PaymentEventLog::query()
            ->select('provider')
            ->distinct()
            ->orderBy('provider')
            ->pluck('provider');

        return view('admin.observability.index', compact('alerts', 'events', 'stats', 'providers'));
    }

    public function resolveAlert(MonitoringAlert $alert): RedirectResponse
    {
        if ($alert->status === MonitoringAlert::STATUS_RESOLVED) {
            return back()->with('warning', 'This alert is already resolved.');
        }

        $alert->markResolved(auth()->user());

        return back()->with('success', 'Alert resolved successfully.');
    }
}
