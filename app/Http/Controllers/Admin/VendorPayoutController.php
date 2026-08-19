<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Setting;
use App\Domains\ECommerce\Models\Vendor;
use App\Domains\ECommerce\Models\VendorPayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorPayoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view payouts')->only(['index', 'show']);
        $this->middleware('can:create payouts')->only(['store']);
        $this->middleware('can:process payouts')->only(['process']);
    }

    public function index(Request $request): View
    {
        $query = VendorPayout::query()
            ->with(['vendor.user', 'processor'])
            ->withCount([
                'items',
                'items as posted_items_count' => fn(Builder $builder) => $builder->whereNotNull('posted_at'),
            ])
            ->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('payout_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('vendor', fn(Builder $vendorQuery) => $vendorQuery->where('shop_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', trim((string) $request->status));
        }

        $selectedVendorId = $request->filled('vendor') ? (int) $request->vendor : null;
        if ($selectedVendorId) {
            $query->where('vendor_id', $selectedVendorId);
        }

        $payouts = $query->paginate(20);
        $vendors = Vendor::query()->approved()->orderBy('shop_name')->get(['id', 'shop_name']);

        $stats = [
            'total' => VendorPayout::query()->count(),
            'pending' => VendorPayout::query()->pending()->count(),
            'completed' => VendorPayout::query()->completed()->count(),
            'pending_value' => (float) VendorPayout::query()->pending()->sum('net_amount'),
        ];

        $pendingPreviewOrders = collect();
        $pendingPreviewSummary = null;

        if ($selectedVendorId) {
            $dateFrom = $request->filled('period_start') ? (string) $request->period_start : null;
            $dateTo = $request->filled('period_end') ? (string) $request->period_end : null;

            $pendingQuery = $this->eligibleOrdersQuery($selectedVendorId, $dateFrom, $dateTo);
            $pendingPreviewOrders = (clone $pendingQuery)
                ->latest()
                ->limit(10)
                ->get(['id', 'order_number', 'total', 'commission_amount', 'refunded_amount', 'payment_status', 'created_at']);

            $pendingPreviewSummary = [
                'count' => (clone $pendingQuery)->count(),
                'gross' => (float) (clone $pendingQuery)->sum('total'),
                'commission' => (float) (clone $pendingQuery)->sum('commission_amount'),
                'refund' => (float) (clone $pendingQuery)->sum('refunded_amount'),
                'payable' => (float) (clone $pendingQuery)->get()->sum(fn(Order $order) => (float) $order->payout_payable_amount),
            ];
        }

        return view('admin.payouts.index', [
            'payouts' => $payouts,
            'vendors' => $vendors,
            'stats' => $stats,
            'minPayoutAmount' => Setting::minPayoutAmount(),
            'pendingPreviewOrders' => $pendingPreviewOrders,
            'pendingPreviewSummary' => $pendingPreviewSummary,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_details' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'order_ids' => ['nullable', 'array'],
            'order_ids.*' => ['integer', 'distinct'],
        ]);

        $vendor = Vendor::query()
            ->approved()
            ->find($validated['vendor_id']);

        if (!$vendor) {
            return back()->withInput()->with('error', 'Only approved vendors are eligible for payout processing.');
        }

        $orderIds = collect($validated['order_ids'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $ordersQuery = $this->eligibleOrdersQuery(
            (int) $vendor->id,
            $validated['period_start'] ?? null,
            $validated['period_end'] ?? null,
        );

        if ($orderIds->isNotEmpty()) {
            $ordersQuery->whereIn('id', $orderIds->all());
        }

        $orders = $ordersQuery->get();

        if ($orderIds->isNotEmpty() && $orders->count() !== $orderIds->count()) {
            return back()
                ->withInput()
                ->with('error', 'Some selected orders are no longer eligible for payout. Refresh and try again.');
        }

        if ($orders->isEmpty()) {
            return back()->withInput()->with('error', 'No eligible payout ledger orders found for the selected vendor and period.');
        }

        $grossAmount = round((float) $orders->sum(fn(Order $order) => (float) $order->total), 2);
        $platformFee = round((float) $orders->sum(fn(Order $order) => (float) $order->commission_amount), 2);
        $netAmount = round((float) $orders->sum(fn(Order $order) => (float) $order->payout_payable_amount), 2);

        if ($netAmount <= 0) {
            return back()->withInput()->with('error', 'Calculated payout amount is zero after commission and refund deductions.');
        }

        $minPayoutAmount = Setting::minPayoutAmount();
        if ($netAmount < $minPayoutAmount) {
            return back()->withInput()->with('error', 'Net payout amount must be at least BDT ' . number_format($minPayoutAmount, 2) . '.');
        }

        $payout = DB::transaction(function () use ($vendor, $validated, $orders, $grossAmount, $platformFee, $netAmount): VendorPayout {
            $payout = VendorPayout::create([
                'vendor_id' => (int) $vendor->id,
                'amount' => $grossAmount,
                'platform_fee' => $platformFee,
                'net_amount' => $netAmount,
                'payment_method' => (string) $validated['payment_method'],
                'payment_details' => $validated['payment_details'] ?? null,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'period_start' => $validated['period_start'] ?? null,
                'period_end' => $validated['period_end'] ?? null,
            ]);

            $payout->items()->createMany(
                $orders->map(fn(Order $order): array => [
                    'order_id' => (int) $order->id,
                    'order_total' => (float) $order->total,
                    'commission_amount' => (float) $order->commission_amount,
                    'refund_amount' => (float) ($order->refunded_amount ?? 0),
                    'vendor_earning' => (float) $order->vendor_earning,
                    'payable_amount' => (float) $order->payout_payable_amount,
                ])->all()
            );

            return $payout;
        });

        return redirect()
            ->route('admin.payouts.show', $payout)
            ->with('success', 'Payout ' . $payout->payout_number . ' created successfully.');
    }

    public function show(VendorPayout $payout): View
    {
        $payout->load([
            'vendor.user',
            'processor',
            'items.order',
            'items.postedBy',
        ]);

        return view('admin.payouts.show', compact('payout'));
    }

    public function process(Request $request, VendorPayout $payout): RedirectResponse
    {
        if ($payout->status === 'completed') {
            return back()->with('warning', 'This payout is already processed.');
        }

        if (in_array($payout->status, ['failed', 'cancelled'], true)) {
            return back()->with('error', 'Only pending or processing payouts can be marked as completed.');
        }

        $validated = $request->validate([
            'reference_number' => ['nullable', 'string', 'max:255'],
            'payment_details' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $referenceNumber = trim((string) ($validated['reference_number'] ?? ''));
        $referenceNumber = $referenceNumber !== '' ? $referenceNumber : (string) ($payout->reference_number ?? '');
        $referenceNumber = $referenceNumber !== '' ? $referenceNumber : null;

        DB::transaction(function () use ($request, $payout, $validated, $referenceNumber): void {
            $payout->markAsProcessed($request->user(), $referenceNumber);
            $payout->markLedgerItemsPosted($request->user());

            $updates = [];
            if ($request->filled('payment_details')) {
                $updates['payment_details'] = $validated['payment_details'];
            }
            if ($request->filled('notes')) {
                $updates['notes'] = $validated['notes'];
            }

            if ($updates !== []) {
                $payout->update($updates);
            }
        });

        return back()->with('success', 'Payout marked as completed successfully.');
    }

    private function eligibleOrdersQuery(int $vendorId, ?string $periodStart = null, ?string $periodEnd = null): Builder
    {
        return Order::query()
            ->where('vendor_id', $vendorId)
            ->where('status', Order::STATUS_DELIVERED)
            ->whereIn('payment_status', ['paid', 'refunded', 'partially_refunded'])
            ->whereDoesntHave('payoutItems')
            ->when($periodStart, fn(Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($periodEnd, fn(Builder $query, string $date) => $query->whereDate('created_at', '<=', $date));
    }
}
