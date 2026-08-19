@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Return {{ $returnRequest->rma_number }}</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('admin.returns.index') }}">Returns</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>{{ $returnRequest->rma_number }}</span>
            </div>
        </div>
        <a href="{{ route('admin.orders.show', $returnRequest->order) }}" class="btn btn-outline">
            <i class="fas fa-shopping-bag"></i> View Order
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Return Request Details</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <div style="font-size: 13px; color: #64748b;">Reason</div>
                            <div style="font-weight: 600; margin-top: 4px;">{{ $returnRequest->reason }}</div>
                        </div>
                        <div>
                            <div style="font-size: 13px; color: #64748b;">Current Status</div>
                            <div style="margin-top: 4px;">
                                <span class="badge badge-{{ $returnRequest->status_badge }}">
                                    {{ $returnRequest->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($returnRequest->details)
                        <div style="margin-top: 12px; padding: 10px 12px; background: #f8fafc; border-radius: 8px;">
                            {{ $returnRequest->details }}
                        </div>
                    @endif

                    @if($returnRequest->rejection_reason)
                        <div style="margin-top: 12px; padding: 10px 12px; background: #fef2f2; border-radius: 8px; color: #b91c1c;">
                            <strong>Rejection Reason:</strong> {{ $returnRequest->rejection_reason }}
                        </div>
                    @endif

                    @if($returnRequest->pickup_note)
                        <div style="margin-top: 12px; padding: 10px 12px; background: #f8fafc; border-radius: 8px;">
                            <strong>Pickup Note:</strong> {{ $returnRequest->pickup_note }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Update Return Status</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.returns.update-status', $returnRequest) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="{{ $returnRequest->status }}">
                                    {{ $returnRequest->status_label }} (Current)
                                </option>
                                @foreach($allowedNextStatuses as $status)
                                    <option value="{{ $status }}">{{ \App\Models\ReturnRequest::statusLabel($status) }}</option>
                                @endforeach
                            </select>
                            @if(empty($allowedNextStatuses))
                                <small style="color: #64748b;">No more transition available for this return request.</small>
                            @else
                                <small style="color: #64748b;">Flow: Requested -> Approved/Rejected -> Picked Up -> Refunded</small>
                            @endif
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label>Approved Refund Amount</label>
                                <input type="number" name="approved_refund_amount" class="form-control" min="0"
                                    max="{{ $returnRequest->order->total }}" step="0.01"
                                    value="{{ $returnRequest->approved_refund_amount ?? $returnRequest->requested_refund_amount }}">
                            </div>
                            <div class="form-group">
                                <label>Refund Method</label>
                                <input type="text" name="refund_method" class="form-control"
                                    value="{{ $returnRequest->refund_method }}"
                                    placeholder="stripe / cod reversal / bank transfer">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label>Refund Transaction ID</label>
                                <input type="text" name="refund_transaction_id" class="form-control"
                                    value="{{ $returnRequest->refund_transaction_id }}" placeholder="Optional reference">
                            </div>
                            <div class="form-group">
                                <label>Pickup Note</label>
                                <input type="text" name="pickup_note" class="form-control" value="{{ $returnRequest->pickup_note }}"
                                    placeholder="Courier pickup details">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Rejection Reason (required when rejecting)</label>
                            <textarea name="rejection_reason" class="form-control" rows="2"
                                placeholder="Write reason if you reject this request">{{ $returnRequest->rejection_reason }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Comment</label>
                            <textarea name="comment" class="form-control" rows="2"
                                placeholder="Internal comment / note for timeline"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Return Status</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Return Timeline</h3>
                </div>
                <div class="card-body">
                    <div style="position: relative; padding-left: 24px;">
                        @foreach($returnRequest->statusHistories as $history)
                            <div
                                style="position: relative; padding-bottom: 24px; border-left: 2px solid #e2e8f0; margin-left: 8px; padding-left: 24px;">
                                <div
                                    style="position: absolute; left: -9px; top: 0; width: 16px; height: 16px; background: {{ $loop->first ? 'var(--primary)' : '#e2e8f0' }}; border-radius: 50%; border: 3px solid white;">
                                </div>
                                <div style="font-weight: 600;">
                                    {{ $history->new_status_label }}
                                    @if($history->old_status_label)
                                        <span style="font-weight: 400; color: #64748b;">(from {{ $history->old_status_label }})</span>
                                    @endif
                                </div>
                                <div style="font-size: 13px; color: #64748b;">{{ $history->created_at->format('M d, Y h:i A') }}</div>
                                @if($history->comment)
                                    <div style="margin-top: 8px; padding: 8px 12px; background: #f8fafc; border-radius: 6px; font-size: 14px;">
                                        {{ $history->comment }}
                                    </div>
                                @endif
                                @if($history->user)
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">By {{ $history->user->name }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Summary</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">RMA</span>
                        <span style="font-weight: 600;">{{ $returnRequest->rma_number }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Order</span>
                        <span style="font-weight: 600;">#{{ $returnRequest->order->order_number }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Requested</span>
                        <span>BDT {{ number_format($returnRequest->requested_refund_amount, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Approved</span>
                        <span>BDT {{ number_format($returnRequest->approved_refund_amount ?? 0, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Refund Txn</span>
                        <span>{{ $returnRequest->refund_transaction_id ?: 'N/A' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Last Update</span>
                        <span>{{ $returnRequest->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Customer</h3>
                </div>
                <div class="card-body">
                    <p style="font-weight: 600;">{{ $returnRequest->user->name ?? 'N/A' }}</p>
                    <p style="font-size: 13px; color: #64748b;">{{ $returnRequest->user->email ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Order Payment</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Order Status</span>
                        <span class="badge badge-{{ $returnRequest->order->status_badge }}">
                            {{ $returnRequest->order->status_label }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Payment Status</span>
                        <span class="badge badge-{{ $returnRequest->order->payment_status_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $returnRequest->order->payment_status)) }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Order Total</span>
                        <span>BDT {{ number_format($returnRequest->order->total, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Refunded Amount</span>
                        <span>BDT {{ number_format($returnRequest->order->refunded_amount ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
