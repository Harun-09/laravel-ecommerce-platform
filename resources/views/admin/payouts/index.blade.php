@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Payout Operations</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Payouts</span>
            </div>
        </div>
        @can('view reports')
            <a href="{{ route('admin.reports.index', ['type' => 'payout']) }}" class="btn btn-outline">
                <i class="fas fa-chart-line"></i> Payout Report
            </a>
        @endcan
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['total'] }}</div>
            <div class="label">Total Payouts</div>
        </div>
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['pending'] }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">{{ $stats['completed'] }}</div>
            <div class="label">Completed</div>
        </div>
        <div class="stat-card" style="padding: 16px;">
            <div class="value" style="font-size: 24px;">BDT {{ number_format((float) $stats['pending_value'], 2) }}</div>
            <div class="label">Pending Value</div>
        </div>
    </div>

    @can('create payouts')
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>Create Vendor Payout</h3>
                <div style="font-size: 13px; color: #64748b;">
                    Minimum net payout: BDT {{ number_format((float) $minPayoutAmount, 2) }}
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payouts.store') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1.2fr 1fr 1fr; gap: 14px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Vendor *</label>
                            <select name="vendor_id" class="form-control" required>
                                <option value="">Select vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ (string) old('vendor_id', request('vendor')) === (string) $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->shop_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Payment Method *</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="bkash" {{ old('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                                <option value="nagad" {{ old('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                                <option value="rocket" {{ old('payment_method') === 'rocket' ? 'selected' : '' }}>Rocket</option>
                                <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Payment Details</label>
                            <input type="text" name="payment_details" class="form-control" placeholder="Account/wallet details"
                                value="{{ old('payment_details') }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 14px; margin-top: 14px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Period Start</label>
                            <input type="date" name="period_start" class="form-control"
                                value="{{ old('period_start', request('period_start')) }}">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Period End</label>
                            <input type="date" name="period_end" class="form-control"
                                value="{{ old('period_end', request('period_end')) }}">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Notes</label>
                            <input type="text" name="notes" class="form-control"
                                placeholder="Optional internal note for this payout batch"
                                value="{{ old('notes') }}">
                        </div>
                    </div>

                    <div style="margin-top: 12px; font-size: 13px; color: #64748b;">
                        This creates one payout batch from all eligible vendor orders that are paid/refunded and not already included in another payout.
                    </div>

                    <div style="margin-top: 14px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Payout Batch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form action="{{ route('admin.payouts.index') }}" method="GET"
                style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 220px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Search</label>
                    <input type="text" name="search" class="form-control"
                        placeholder="Payout number / reference / vendor" value="{{ request('search') }}">
                </div>
                <div style="width: 220px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Vendor</label>
                    <select name="vendor" class="form-control">
                        <option value="">All vendors</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ (string) request('vendor') === (string) $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->shop_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="width: 170px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All status</option>
                        @foreach(['pending', 'processing', 'completed', 'failed', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.payouts.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if($pendingPreviewSummary)
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>Selected Vendor Pending Ledger Preview</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 16px;">
                    <div style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <div style="font-size: 12px; color: #64748b;">Orders</div>
                        <div style="font-weight: 700; margin-top: 2px;">{{ $pendingPreviewSummary['count'] }}</div>
                    </div>
                    <div style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <div style="font-size: 12px; color: #64748b;">Gross</div>
                        <div style="font-weight: 700; margin-top: 2px;">BDT {{ number_format((float) $pendingPreviewSummary['gross'], 2) }}</div>
                    </div>
                    <div style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <div style="font-size: 12px; color: #64748b;">Commission</div>
                        <div style="font-weight: 700; margin-top: 2px;">BDT {{ number_format((float) $pendingPreviewSummary['commission'], 2) }}</div>
                    </div>
                    <div style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <div style="font-size: 12px; color: #64748b;">Refund</div>
                        <div style="font-weight: 700; margin-top: 2px;">BDT {{ number_format((float) $pendingPreviewSummary['refund'], 2) }}</div>
                    </div>
                    <div style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <div style="font-size: 12px; color: #64748b;">Payable</div>
                        <div style="font-weight: 700; margin-top: 2px; color: #15803d;">BDT {{ number_format((float) $pendingPreviewSummary['payable'], 2) }}</div>
                    </div>
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
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPreviewOrders as $order)
                                <tr>
                                    <td>
                                        @can('view orders')
                                            <a href="{{ route('admin.orders.show', $order) }}" style="color: var(--primary); font-weight: 600;">
                                                #{{ $order->order_number }}
                                            </a>
                                        @else
                                            #{{ $order->order_number }}
                                        @endcan
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $order->payment_status_badge }}">
                                            {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                        </span>
                                    </td>
                                    <td>BDT {{ number_format((float) $order->total, 2) }}</td>
                                    <td>BDT {{ number_format((float) $order->commission_amount, 2) }}</td>
                                    <td>BDT {{ number_format((float) ($order->refunded_amount ?? 0), 2) }}</td>
                                    <td style="font-weight: 700; color: #15803d;">BDT {{ number_format((float) $order->payout_payable_amount, 2) }}</td>
                                    <td style="font-size: 13px; color: #64748b;">{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #64748b; padding: 24px;">
                                        No pending payout ledger orders for this vendor.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Payout</th>
                        <th>Vendor</th>
                        <th>Orders</th>
                        <th>Ledger</th>
                        <th>Gross</th>
                        <th>Fee</th>
                        <th>Net</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Processed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payouts as $payout)
                        <tr>
                            <td>
                                <a href="{{ route('admin.payouts.show', $payout) }}" style="color: var(--primary); font-weight: 700;">
                                    {{ $payout->payout_number }}
                                </a>
                                @if(!empty($payout->reference_number))
                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Ref: {{ $payout->reference_number }}</div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $payout->vendor->shop_name ?? 'N/A' }}</div>
                                <div style="font-size: 12px; color: #64748b;">{{ $payout->vendor->user->email ?? '' }}</div>
                            </td>
                            <td>{{ (int) $payout->items_count }}</td>
                            <td>
                                @php
                                    $postedCount = (int) ($payout->posted_items_count ?? 0);
                                    $itemsCount = (int) $payout->items_count;
                                    $ledgerComplete = $itemsCount > 0 && $postedCount === $itemsCount;
                                @endphp
                                <div style="font-weight: 600;">{{ $postedCount }}/{{ $itemsCount }}</div>
                                <span class="badge badge-{{ $ledgerComplete ? 'success' : 'warning' }}" style="margin-top: 6px;">
                                    {{ $ledgerComplete ? 'Posted' : 'Pending' }}
                                </span>
                            </td>
                            <td>BDT {{ number_format((float) $payout->amount, 2) }}</td>
                            <td>BDT {{ number_format((float) $payout->platform_fee, 2) }}</td>
                            <td style="font-weight: 700; color: #15803d;">BDT {{ number_format((float) $payout->net_amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $payout->status_badge }}">
                                    {{ ucfirst($payout->status) }}
                                </span>
                            </td>
                            <td style="font-size: 13px; color: #64748b;">{{ $payout->created_at->format('M d, Y h:i A') }}</td>
                            <td style="font-size: 13px; color: #64748b;">
                                {{ $payout->processed_at?->format('M d, Y h:i A') ?? '-' }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <a href="{{ route('admin.payouts.show', $payout) }}" class="btn btn-sm btn-outline" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('process payouts')
                                        @if(in_array($payout->status, ['pending', 'processing'], true))
                                            <form action="{{ route('admin.payouts.process', $payout) }}" method="POST"
                                                onsubmit="return confirm('Mark this payout as completed?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" title="Process">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center; color: #64748b; padding: 40px;">
                                No payout records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payouts->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $payouts->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
