@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Payout {{ $payout->payout_number }}</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('admin.payouts.index') }}">Payouts</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>{{ $payout->payout_number }}</span>
            </div>
        </div>
        <a href="{{ route('admin.payouts.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Payout Ledger Items</h3>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Payment</th>
                                <th>Gross</th>
                                <th>Commission</th>
                                <th>Refund</th>
                                <th>Payable</th>
                                <th>Ledger</th>
                                <th>Posted By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payout->items as $item)
                                <tr>
                                    <td>
                                        @if($item->order)
                                            @can('view orders')
                                                <a href="{{ route('admin.orders.show', $item->order) }}" style="color: var(--primary); font-weight: 600;">
                                                    #{{ $item->order->order_number }}
                                                </a>
                                            @else
                                                #{{ $item->order->order_number }}
                                            @endcan
                                        @else
                                            <span style="color: #64748b;">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->order)
                                            <span class="badge badge-{{ $item->order->payment_status_badge }}">
                                                {{ ucfirst(str_replace('_', ' ', $item->order->payment_status)) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>BDT {{ number_format((float) $item->order_total, 2) }}</td>
                                    <td>BDT {{ number_format((float) $item->commission_amount, 2) }}</td>
                                    <td>BDT {{ number_format((float) ($item->refund_amount ?? 0), 2) }}</td>
                                    <td style="font-weight: 700; color: #15803d;">BDT {{ number_format((float) ($item->payable_amount ?? 0), 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->is_posted ? 'success' : 'warning' }}">
                                            {{ $item->is_posted ? 'Posted' : 'Pending' }}
                                        </span>
                                        @if($item->posted_at)
                                            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                                {{ $item->posted_at->format('M d, Y h:i A') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="font-size: 13px; color: #334155;">
                                        {{ $item->postedBy->name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; color: #64748b; padding: 28px;">
                                        No payout ledger items found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot style="background: #f8fafc;">
                            <tr>
                                <td colspan="2" style="text-align: right; font-weight: 600;">Totals</td>
                                <td style="font-weight: 600;">BDT {{ number_format((float) $payout->ledger_order_amount, 2) }}</td>
                                <td style="font-weight: 600;">BDT {{ number_format((float) $payout->ledger_commission_amount, 2) }}</td>
                                <td style="font-weight: 600;">BDT {{ number_format((float) $payout->ledger_refund_amount, 2) }}</td>
                                <td style="font-weight: 700; color: #15803d;">BDT {{ number_format((float) $payout->ledger_payable_amount, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @can('process payouts')
                @if(in_array($payout->status, ['pending', 'processing'], true))
                    <div class="card">
                        <div class="card-header">
                            <h3>Process Payout</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.payouts.process', $payout) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="form-group">
                                    <label>Reference Number</label>
                                    <input type="text" name="reference_number" class="form-control"
                                        value="{{ old('reference_number', $payout->reference_number) }}"
                                        placeholder="Bank/transaction reference">
                                </div>

                                <div class="form-group">
                                    <label>Payment Details</label>
                                    <input type="text" name="payment_details" class="form-control"
                                        value="{{ old('payment_details', $payout->payment_details) }}"
                                        placeholder="Account or wallet settlement details">
                                </div>

                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="3"
                                        placeholder="Optional processing note">{{ old('notes', $payout->notes) }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Confirm vendor payout is settled and mark as completed?')">
                                    <i class="fas fa-check"></i> Mark As Completed
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endcan
        </div>

        <div>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Payout Summary</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Status</span>
                        <span class="badge badge-{{ $payout->status_badge }}">{{ ucfirst($payout->status) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Method</span>
                        <span>{{ ucfirst(str_replace('_', ' ', (string) $payout->payment_method)) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Gross Amount</span>
                        <span>BDT {{ number_format((float) $payout->amount, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Platform Fee</span>
                        <span>BDT {{ number_format((float) $payout->platform_fee, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Net Amount</span>
                        <span style="font-weight: 700; color: #15803d;">BDT {{ number_format((float) $payout->net_amount, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Ledger Posted</span>
                        <span>{{ $payout->ledger_posted_items_count }}/{{ (int) $payout->items->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Ledger Pending</span>
                        <span>{{ $payout->ledger_pending_items_count }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Created At</span>
                        <span>{{ $payout->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Processed At</span>
                        <span>{{ $payout->processed_at?->format('M d, Y h:i A') ?? '-' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #64748b;">Reference</span>
                        <span>{{ $payout->reference_number ?: '-' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Processed By</span>
                        <span>{{ $payout->processor->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Vendor</h3>
                </div>
                <div class="card-body">
                    <div style="font-weight: 700;">{{ $payout->vendor->shop_name ?? 'N/A' }}</div>
                    <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                        {{ $payout->vendor->email ?? '' }}
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h3>Period</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: #64748b;">Start</span>
                        <span>{{ $payout->period_start?->format('M d, Y') ?? '-' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">End</span>
                        <span>{{ $payout->period_end?->format('M d, Y') ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Additional Notes</h3>
                </div>
                <div class="card-body">
                    <div style="font-size: 14px; color: #334155; margin-bottom: 12px;">
                        <strong>Payment Details:</strong><br>
                        {{ $payout->payment_details ?: 'N/A' }}
                    </div>
                    <div style="font-size: 14px; color: #334155;">
                        <strong>Notes:</strong><br>
                        {{ $payout->notes ?: 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
