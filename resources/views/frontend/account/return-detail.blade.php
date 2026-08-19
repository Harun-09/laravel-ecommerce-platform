@extends('layouts.app')

@section('content')
    <div class="container section">
        <div class="account-layout-grid">
            @include('frontend.account.partials.sidebar', ['user' => auth()->user()])

            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div>
                        <h1 style="font-size: 24px; font-weight: 700;">Return {{ $returnRequest->rma_number }}</h1>
                        <p style="font-size: 14px; color: #6b7280;">Order #{{ $returnRequest->order->order_number }}</p>
                    </div>
                    <a href="{{ route('account.returns') }}" class="btn btn-outline">Back to Returns</a>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 18px;">
                    <div class="card" style="padding: 14px;">
                        <p style="font-size: 12px; color: #6b7280;">Current Status</p>
                        <div style="margin-top: 6px;">
                            <span class="badge badge-{{ $returnRequest->status_badge }}">{{ $returnRequest->status_label }}</span>
                        </div>
                    </div>
                    <div class="card" style="padding: 14px;">
                        <p style="font-size: 12px; color: #6b7280;">Requested Amount</p>
                        <p style="font-weight: 700; margin-top: 6px;">{{ store_money($returnRequest->requested_refund_amount) }}
                        </p>
                    </div>
                    <div class="card" style="padding: 14px;">
                        <p style="font-size: 12px; color: #6b7280;">Approved Amount</p>
                        <p style="font-weight: 700; margin-top: 6px;">
                            {{ store_money($returnRequest->approved_refund_amount ?? 0) }}
                        </p>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 18px;">
                    <div class="card-header">
                        <h3>Return Details</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Reason:</strong> {{ $returnRequest->reason }}</p>
                        @if($returnRequest->details)
                            <p style="margin-top: 8px; color: #4b5563;">{{ $returnRequest->details }}</p>
                        @endif
                        @if($returnRequest->rejection_reason)
                            <p style="margin-top: 8px; color: #b91c1c;"><strong>Rejection Reason:</strong>
                                {{ $returnRequest->rejection_reason }}</p>
                        @endif
                        @if($returnRequest->pickup_note)
                            <p style="margin-top: 8px; color: #1f2937;"><strong>Pickup Note:</strong>
                                {{ $returnRequest->pickup_note }}</p>
                        @endif
                        @if($returnRequest->refund_transaction_id)
                            <p style="margin-top: 8px;"><strong>Refund Txn:</strong> {{ $returnRequest->refund_transaction_id }}</p>
                        @endif
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
                                    style="position: relative; padding-bottom: 20px; border-left: 2px solid #e2e8f0; margin-left: 8px; padding-left: 20px;">
                                    <div
                                        style="position: absolute; left: -9px; top: 0; width: 16px; height: 16px; background: {{ $loop->first ? '#4f46e5' : '#cbd5e1' }}; border-radius: 50%; border: 3px solid white;">
                                    </div>
                                    <div style="font-weight: 600;">
                                        {{ $history->new_status_label }}
                                        @if($history->old_status_label)
                                            <span style="color: #6b7280; font-weight: 400;">(from {{ $history->old_status_label }})</span>
                                        @endif
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280;">
                                        {{ $history->created_at->format('M d, Y h:i A') }}
                                        @if($history->user)
                                            | by {{ $history->user->name }}
                                        @endif
                                    </div>
                                    @if($history->comment)
                                        <div style="font-size: 13px; color: #475569; margin-top: 4px;">{{ $history->comment }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
