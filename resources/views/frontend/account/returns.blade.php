@extends('layouts.app')

@section('content')
    <div class="container section">
        <div class="account-layout-grid">
            @include('frontend.account.partials.sidebar', ['user' => auth()->user()])

            <div>
                <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 16px;">My Returns</h1>

                <div class="card">
                    @if($returns->isEmpty())
                        <div style="padding: 50px 24px; text-align: center;">
                            <i class="fas fa-undo-alt" style="font-size: 42px; color: #d1d5db; margin-bottom: 12px;"></i>
                            <p style="color: #6b7280;">No return request found.</p>
                        </div>
                    @else
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 14px 16px; text-align: left;">RMA</th>
                                        <th style="padding: 14px 16px; text-align: left;">Order</th>
                                        <th style="padding: 14px 16px; text-align: left;">Reason</th>
                                        <th style="padding: 14px 16px; text-align: left;">Status</th>
                                        <th style="padding: 14px 16px; text-align: right;">Requested</th>
                                        <th style="padding: 14px 16px; text-align: right;">Approved</th>
                                        <th style="padding: 14px 16px; text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($returns as $returnRequest)
                                        <tr style="border-bottom: 1px solid #f3f4f6;">
                                            <td style="padding: 14px 16px; font-weight: 600;">{{ $returnRequest->rma_number }}</td>
                                            <td style="padding: 14px 16px;">
                                                <a href="{{ route('account.orders.detail', $returnRequest->order->order_number) }}"
                                                    style="color: #4f46e5; font-weight: 600;">
                                                    #{{ $returnRequest->order->order_number }}
                                                </a>
                                            </td>
                                            <td style="padding: 14px 16px; color: #6b7280;">{{ $returnRequest->reason }}</td>
                                            <td style="padding: 14px 16px;">
                                                <span class="badge badge-{{ $returnRequest->status_badge }}">
                                                    {{ $returnRequest->status_label }}
                                                </span>
                                            </td>
                                            <td style="padding: 14px 16px; text-align: right;">
                                                {{ store_money($returnRequest->requested_refund_amount) }}
                                            </td>
                                            <td style="padding: 14px 16px; text-align: right;">
                                                {{ store_money($returnRequest->approved_refund_amount ?? 0) }}
                                            </td>
                                            <td style="padding: 14px 16px; text-align: right;">
                                                <a href="{{ route('account.returns.show', $returnRequest) }}" class="btn btn-outline"
                                                    style="padding: 8px 12px;">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($returns->hasPages())
                            <div style="padding: 16px 20px; border-top: 1px solid #e5e7eb;">
                                {{ $returns->withQueryString()->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


