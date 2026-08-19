<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\ReturnRequest;
use App\Services\OrderNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;

class ReturnRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:process refunds');
    }

    public function index(Request $request): View
    {
        $query = ReturnRequest::query()
            ->with(['order.user', 'vendor', 'user'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($builder) use ($search) {
                $builder->where('rma_number', 'like', "%{$search}%")
                    ->orWhereHas('order', fn($orderQuery) => $orderQuery->where('order_number', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', strtolower(trim((string) $request->status)));
        }

        $returns = $query->paginate(20);

        $stats = [
            'requested' => ReturnRequest::query()->where('status', ReturnRequest::STATUS_REQUESTED)->count(),
            'approved' => ReturnRequest::query()->where('status', ReturnRequest::STATUS_APPROVED)->count(),
            'picked_up' => ReturnRequest::query()->where('status', ReturnRequest::STATUS_PICKED_UP)->count(),
            'refunded' => ReturnRequest::query()->where('status', ReturnRequest::STATUS_REFUNDED)->count(),
            'rejected' => ReturnRequest::query()->where('status', ReturnRequest::STATUS_REJECTED)->count(),
        ];

        return view('admin.returns.index', compact('returns', 'stats'));
    }

    public function show(ReturnRequest $returnRequest): View
    {
        $returnRequest->load([
            'order.items.product',
            'order.user',
            'vendor',
            'user',
            'processedBy',
            'statusHistories' => fn($query) => $query->with('user')->latest(),
        ]);

        $allowedNextStatuses = $returnRequest->getAllowedNextStatuses();

        return view('admin.returns.show', compact('returnRequest', 'allowedNextStatuses'));
    }

    public function updateStatus(Request $request, ReturnRequest $returnRequest)
    {
        $request->validate([
            'status' => ['required', 'string', Rule::in(ReturnRequest::allStatuses())],
            'comment' => 'nullable|string|max:500',
            'approved_refund_amount' => 'nullable|numeric|min:0',
            'rejection_reason' => 'nullable|string|max:1000',
            'pickup_note' => 'nullable|string|max:1000',
            'refund_method' => 'nullable|string|max:50',
            'refund_transaction_id' => 'nullable|string|max:120',
        ]);

        $newStatus = strtolower(trim((string) $request->status));

        if ($newStatus === $returnRequest->status) {
            return back()->with('error', 'Return request is already in the selected status.');
        }

        $approvedRefundAmount = (float) ($request->approved_refund_amount ?? 0);
        $order = $returnRequest->order;

        if ($newStatus === ReturnRequest::STATUS_APPROVED) {
            if ($approvedRefundAmount <= 0) {
                $approvedRefundAmount = (float) ($returnRequest->requested_refund_amount ?: $order->total);
            }

            $returnRequest->approved_refund_amount = min($approvedRefundAmount, (float) $order->total);
        }

        if ($newStatus === ReturnRequest::STATUS_REJECTED) {
            if (!$request->filled('rejection_reason')) {
                return back()->with('error', 'Rejection reason is required for rejected return request.');
            }

            $returnRequest->rejection_reason = $request->rejection_reason;
        }

        if ($newStatus === ReturnRequest::STATUS_PICKED_UP && $request->filled('pickup_note')) {
            $returnRequest->pickup_note = $request->pickup_note;
        }

        if ($newStatus === ReturnRequest::STATUS_REFUNDED) {
            if ($returnRequest->status !== ReturnRequest::STATUS_PICKED_UP) {
                return back()->with('error', 'Order pickup must be completed before refund.');
            }

            if ($approvedRefundAmount <= 0) {
                $approvedRefundAmount = (float) ($returnRequest->approved_refund_amount ?: $returnRequest->requested_refund_amount ?: $order->total);
            }

            $approvedRefundAmount = min($approvedRefundAmount, (float) $order->total);
            $returnRequest->approved_refund_amount = $approvedRefundAmount;
            $returnRequest->refund_method = $request->refund_method ?: $order->payment_method;
            $returnRequest->refund_transaction_id = $request->refund_transaction_id;
        }

        try {
            DB::transaction(function () use ($request, $returnRequest, $order, $newStatus): void {
                $returnRequest->save();
                $returnRequest->updateStatus($newStatus, auth()->user(), $request->comment, true);

                if ($newStatus !== ReturnRequest::STATUS_REFUNDED) {
                    return;
                }

                $refundAmount = (float) ($returnRequest->approved_refund_amount ?? 0);
                $currentRefunded = (float) ($order->refunded_amount ?? 0);
                $updatedRefunded = min((float) $order->total, $currentRefunded + $refundAmount);
                $paymentStatus = $updatedRefunded >= (float) $order->total ? 'refunded' : 'partially_refunded';

                $order->update([
                    'refunded_amount' => $updatedRefunded,
                    'payment_status' => $paymentStatus,
                ]);

                if ($order->canTransitionTo(Order::STATUS_RETURNED)) {
                    try {
                        $order->updateStatus(Order::STATUS_RETURNED, auth()->user(), 'Marked returned after RMA refund.', true);
                    } catch (InvalidArgumentException) {
                        // Keep refund state even if lifecycle transition is unavailable.
                    }
                }
            });
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if ($newStatus === ReturnRequest::STATUS_REFUNDED) {
            app(OrderNotificationService::class)->sendOrderRefunded(
                $order->fresh('user'),
                (float) ($returnRequest->approved_refund_amount ?? 0)
            );
        }

        return back()->with('success', 'Return request status updated successfully.');
    }
}
