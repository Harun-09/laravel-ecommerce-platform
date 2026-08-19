<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\ReturnRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = auth()->user();

        $returns = $user->returnRequests()
            ->with(['order', 'vendor'])
            ->latest()
            ->paginate(10);

        return view('frontend.account.returns', compact('returns'));
    }

    public function show(ReturnRequest $returnRequest): View
    {
        abort_if((int) $returnRequest->user_id !== (int) auth()->id(), 403);

        $returnRequest->load([
            'order.items.product',
            'vendor',
            'statusHistories' => fn($query) => $query->with('user')->latest(),
        ]);

        return view('frontend.account.return-detail', compact('returnRequest'));
    }

    public function store(Request $request, string $orderNumber): RedirectResponse
    {
        $order = auth()->user()->orders()
            ->where('order_number', $orderNumber)
            ->with('returnRequests')
            ->firstOrFail();

        if (!$order->canRequestReturn()) {
            return back()->with('error', 'Return request is not allowed for this order right now.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:150',
            'details' => 'nullable|string|max:1000',
            'requested_refund_amount' => 'nullable|numeric|min:0.01|max:' . (float) $order->total,
        ]);

        $requestedRefundAmount = (float) ($validated['requested_refund_amount'] ?? 0);
        if ($requestedRefundAmount <= 0) {
            $requestedRefundAmount = (float) $order->total;
        }

        $returnRequest = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'vendor_id' => (int) $order->vendor_id,
            'status' => ReturnRequest::STATUS_REQUESTED,
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
            'requested_refund_amount' => min($requestedRefundAmount, (float) $order->total),
        ]);

        $returnRequest->statusHistories()->create([
            'user_id' => auth()->id(),
            'old_status' => null,
            'new_status' => ReturnRequest::STATUS_REQUESTED,
            'comment' => 'Customer submitted a return request.',
            'notify_customer' => true,
        ]);

        return redirect()->route('account.orders.detail', $order->order_number)
            ->with('success', 'Return request submitted successfully.');
    }
}
