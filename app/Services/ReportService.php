<?php

namespace App\Services;

use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReportService
{
    public const TYPE_SALES = 'sales';
    public const TYPE_STOCK = 'stock';
    public const TYPE_PAYOUT = 'payout';

    public function resolveType(?string $type): string
    {
        $type = strtolower(trim((string) $type));

        return in_array($type, [self::TYPE_SALES, self::TYPE_STOCK, self::TYPE_PAYOUT], true)
            ? $type
            : self::TYPE_SALES;
    }

    public function normalizeFilters(array $input): array
    {
        return [
            'date_from' => $this->normalizedDate($input['date_from'] ?? null),
            'date_to' => $this->normalizedDate($input['date_to'] ?? null),
            'status' => $this->normalizedString($input['status'] ?? null),
            'payment_status' => $this->normalizedString($input['payment_status'] ?? null),
            'stock_filter' => $this->normalizedString($input['stock_filter'] ?? null),
            'payout_state' => $this->normalizedString($input['payout_state'] ?? null),
            'vendor_id' => $this->normalizedInt($input['vendor_id'] ?? null),
        ];
    }

    public function titleFor(string $type): string
    {
        return match ($type) {
            self::TYPE_STOCK => 'Stock Report',
            self::TYPE_PAYOUT => 'Payout Ledger Report',
            default => 'Sales Report',
        };
    }

    public function headersFor(string $type): array
    {
        return match ($type) {
            self::TYPE_STOCK => [
                'SKU',
                'Product',
                'Vendor',
                'Category',
                'Price (BDT)',
                'Quantity',
                'Low Threshold',
                'Stock State',
                'Status',
            ],
            self::TYPE_PAYOUT => [
                'Date',
                'Order',
                'Vendor',
                'Payment Status',
                'Gross (BDT)',
                'Commission (BDT)',
                'Refund (BDT)',
                'Payable (BDT)',
                'Payout State',
            ],
            default => [
                'Date',
                'Order',
                'Customer',
                'Vendor',
                'Order Status',
                'Payment Status',
                'Gross (BDT)',
                'Commission (BDT)',
                'Refund (BDT)',
                'Payable (BDT)',
            ],
        };
    }

    public function query(string $type, array $filters, ?int $vendorId = null, ?int $adminVendorFilter = null): Builder
    {
        $effectiveVendorId = $vendorId ?: ($adminVendorFilter ?: null);

        return match ($type) {
            self::TYPE_STOCK => $this->stockQuery($filters, $effectiveVendorId),
            self::TYPE_PAYOUT => $this->payoutQuery($filters, $effectiveVendorId),
            default => $this->salesQuery($filters, $effectiveVendorId),
        };
    }

    public function summary(string $type, array $filters, ?int $vendorId = null, ?int $adminVendorFilter = null): array
    {
        $query = $this->query($type, $filters, $vendorId, $adminVendorFilter);

        return match ($type) {
            self::TYPE_STOCK => $this->stockSummary($query, $filters, $vendorId ?: $adminVendorFilter),
            self::TYPE_PAYOUT => $this->payoutSummary($filters, $vendorId ?: $adminVendorFilter),
            default => $this->salesSummary($query),
        };
    }

    public function mapRows(string $type, Collection $records): array
    {
        return $records
            ->map(fn($record) => $this->mapRow($type, $record))
            ->values()
            ->all();
    }

    private function salesQuery(array $filters, ?int $vendorId = null): Builder
    {
        return Order::query()
            ->with(['user:id,name', 'vendor:id,shop_name'])
            ->when($vendorId, fn(Builder $query) => $query->where('vendor_id', $vendorId))
            ->when($filters['date_from'], fn(Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn(Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['status'], fn(Builder $query, $status) => $query->where('status', Order::normalizeStatus($status)))
            ->when($filters['payment_status'], fn(Builder $query, $status) => $query->where('payment_status', $status))
            ->latest();
    }

    private function stockQuery(array $filters, ?int $vendorId = null): Builder
    {
        return Product::query()
            ->with(['vendor:id,shop_name', 'category:id,name'])
            ->when($vendorId, fn(Builder $query) => $query->where('vendor_id', $vendorId))
            ->when($filters['date_from'], fn(Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn(Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['status'], fn(Builder $query, $status) => $query->where('status', $status))
            ->when($filters['stock_filter'], function (Builder $query, string $stockFilter): void {
                if ($stockFilter === 'low_stock') {
                    $query->where('track_quantity', true)
                        ->whereColumn('quantity', '<=', 'low_stock_threshold')
                        ->where('quantity', '>', 0);
                    return;
                }

                if ($stockFilter === 'out_of_stock') {
                    $query->where('track_quantity', true)->where('quantity', '<=', 0);
                    return;
                }

                if ($stockFilter === 'in_stock') {
                    $query->where(function (Builder $builder): void {
                        $builder->where(function (Builder $inner): void {
                            $inner->where('track_quantity', true)->where('quantity', '>', 0);
                        })->orWhere('track_quantity', false);
                    });
                }
            })
            ->latest();
    }

    private function payoutQuery(array $filters, ?int $vendorId = null): Builder
    {
        return Order::query()
            ->with(['vendor:id,shop_name'])
            ->withCount('postedPayoutItems')
            ->whereIn('payment_status', ['paid', 'refunded', 'partially_refunded'])
            ->when($vendorId, fn(Builder $query) => $query->where('vendor_id', $vendorId))
            ->when($filters['date_from'], fn(Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn(Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['payment_status'], fn(Builder $query, $status) => $query->where('payment_status', $status))
            ->when($filters['payout_state'], function (Builder $query, string $state): void {
                if ($state === 'processed') {
                    $query->has('postedPayoutItems');
                    return;
                }

                if ($state === 'pending') {
                    $query->doesntHave('postedPayoutItems');
                }
            })
            ->latest();
    }

    private function mapRow(string $type, mixed $record): array
    {
        return match ($type) {
            self::TYPE_STOCK => [
                'SKU' => (string) $record->sku,
                'Product' => (string) $record->name,
                'Vendor' => (string) ($record->vendor->shop_name ?? 'N/A'),
                'Category' => (string) ($record->category->name ?? 'N/A'),
                'Price (BDT)' => number_format((float) $record->price, 2),
                'Quantity' => (string) (int) $record->quantity,
                'Low Threshold' => (string) (int) $record->low_stock_threshold,
                'Stock State' => $this->stockStateLabel($record),
                'Status' => ucfirst((string) $record->status),
            ],
            self::TYPE_PAYOUT => [
                'Date' => $record->created_at?->format('Y-m-d') ?? '',
                'Order' => '#' . (string) $record->order_number,
                'Vendor' => (string) ($record->vendor->shop_name ?? 'N/A'),
                'Payment Status' => ucfirst(str_replace('_', ' ', (string) $record->payment_status)),
                'Gross (BDT)' => number_format((float) $record->total, 2),
                'Commission (BDT)' => number_format((float) $record->commission_amount, 2),
                'Refund (BDT)' => number_format((float) ($record->refunded_amount ?? 0), 2),
                'Payable (BDT)' => number_format((float) $record->payout_payable_amount, 2),
                'Payout State' => (int) ($record->posted_payout_items_count ?? 0) > 0 ? 'Processed' : 'Pending',
            ],
            default => [
                'Date' => $record->created_at?->format('Y-m-d') ?? '',
                'Order' => '#' . (string) $record->order_number,
                'Customer' => (string) ($record->user->name ?? 'Guest'),
                'Vendor' => (string) ($record->vendor->shop_name ?? 'N/A'),
                'Order Status' => (string) $record->status_label,
                'Payment Status' => ucfirst(str_replace('_', ' ', (string) $record->payment_status)),
                'Gross (BDT)' => number_format((float) $record->total, 2),
                'Commission (BDT)' => number_format((float) $record->commission_amount, 2),
                'Refund (BDT)' => number_format((float) ($record->refunded_amount ?? 0), 2),
                'Payable (BDT)' => number_format((float) $record->payout_payable_amount, 2),
            ],
        };
    }

    private function salesSummary(Builder $query): array
    {
        $stats = (clone $query)->selectRaw(
            'COUNT(*) as total_orders,
            COALESCE(SUM(total), 0) as gross_sales,
            COALESCE(SUM(commission_amount), 0) as commission_total,
            COALESCE(SUM(refunded_amount), 0) as refund_total,
            COALESCE(SUM(CASE WHEN (total - commission_amount - COALESCE(refunded_amount, 0)) > 0 THEN (total - commission_amount - COALESCE(refunded_amount, 0)) ELSE 0 END), 0) as payable_total'
        )->first();

        return [
            ['label' => 'Total Orders', 'value' => number_format((float) ($stats->total_orders ?? 0))],
            ['label' => 'Gross Sales', 'value' => 'BDT ' . number_format((float) ($stats->gross_sales ?? 0), 2)],
            ['label' => 'Commission', 'value' => 'BDT ' . number_format((float) ($stats->commission_total ?? 0), 2)],
            ['label' => 'Refund', 'value' => 'BDT ' . number_format((float) ($stats->refund_total ?? 0), 2)],
            ['label' => 'Payable', 'value' => 'BDT ' . number_format((float) ($stats->payable_total ?? 0), 2)],
        ];
    }

    private function stockSummary(Builder $query, array $filters, ?int $vendorId = null): array
    {
        $base = Product::query()->when($vendorId, fn(Builder $builder) => $builder->where('vendor_id', $vendorId));

        if (!empty($filters['status'])) {
            $base->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $base->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $base->whereDate('created_at', '<=', $filters['date_to']);
        }

        return [
            ['label' => 'Total Products', 'value' => number_format((float) (clone $base)->count())],
            ['label' => 'In Stock', 'value' => number_format((float) (clone $base)->where(function (Builder $builder): void {
                $builder->where(function (Builder $inner): void {
                    $inner->where('track_quantity', true)->where('quantity', '>', 0);
                })->orWhere('track_quantity', false);
            })->count())],
            ['label' => 'Low Stock', 'value' => number_format((float) (clone $base)->where('track_quantity', true)
                ->whereColumn('quantity', '<=', 'low_stock_threshold')
                ->where('quantity', '>', 0)->count())],
            ['label' => 'Out of Stock', 'value' => number_format((float) (clone $base)->where('track_quantity', true)->where('quantity', '<=', 0)->count())],
            ['label' => 'Tracked Units', 'value' => number_format((float) (clone $base)->where('track_quantity', true)->sum('quantity'))],
        ];
    }

    private function payoutSummary(array $filters, ?int $vendorId = null): array
    {
        $base = Order::query()
            ->whereIn('payment_status', ['paid', 'refunded', 'partially_refunded'])
            ->when($vendorId, fn(Builder $query) => $query->where('vendor_id', $vendorId))
            ->when($filters['date_from'] ?? null, fn(Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn(Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['payment_status'] ?? null, fn(Builder $query, $status) => $query->where('payment_status', $status))
            ->when($filters['payout_state'] ?? null, function (Builder $query, string $state): void {
                if ($state === 'processed') {
                    $query->has('postedPayoutItems');
                    return;
                }

                if ($state === 'pending') {
                    $query->doesntHave('postedPayoutItems');
                }
            });

        $stats = (clone $base)->selectRaw(
            'COUNT(*) as total_orders,
            COALESCE(SUM(total), 0) as gross_total,
            COALESCE(SUM(commission_amount), 0) as commission_total,
            COALESCE(SUM(refunded_amount), 0) as refund_total,
            COALESCE(SUM(CASE WHEN (total - commission_amount - COALESCE(refunded_amount, 0)) > 0 THEN (total - commission_amount - COALESCE(refunded_amount, 0)) ELSE 0 END), 0) as payable_total'
        )->first();

        $processed = (clone $base)->has('postedPayoutItems')->count();
        $pending = (clone $base)->doesntHave('postedPayoutItems')->count();

        return [
            ['label' => 'Ledger Orders', 'value' => number_format((float) ($stats->total_orders ?? 0))],
            ['label' => 'Gross', 'value' => 'BDT ' . number_format((float) ($stats->gross_total ?? 0), 2)],
            ['label' => 'Commission', 'value' => 'BDT ' . number_format((float) ($stats->commission_total ?? 0), 2)],
            ['label' => 'Refund', 'value' => 'BDT ' . number_format((float) ($stats->refund_total ?? 0), 2)],
            ['label' => 'Payable', 'value' => 'BDT ' . number_format((float) ($stats->payable_total ?? 0), 2)],
            ['label' => 'Processed', 'value' => number_format((float) $processed)],
            ['label' => 'Pending', 'value' => number_format((float) $pending)],
        ];
    }

    private function stockStateLabel(Product $product): string
    {
        if (!$product->track_quantity) {
            return 'Not Tracked';
        }

        if ((int) $product->quantity <= 0) {
            return 'Out of Stock';
        }

        if ((int) $product->quantity <= (int) $product->low_stock_threshold) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    private function normalizedDate(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
    }

    private function normalizedString(mixed $value): ?string
    {
        $value = strtolower(trim((string) $value));
        return $value !== '' ? $value : null;
    }

    private function normalizedInt(mixed $value): ?int
    {
        if (!is_numeric($value)) {
            return null;
        }

        $int = (int) $value;
        return $int > 0 ? $int : null;
    }
}
