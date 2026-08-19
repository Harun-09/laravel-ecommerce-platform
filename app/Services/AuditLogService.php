<?php

namespace App\Services;

use App\Domains\ECommerce\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AuditLogService
{
    public function log(
        string $event,
        ?Model $auditable = null,
        array $oldValues = [],
        array $newValues = [],
        array $meta = [],
        ?User $actor = null,
        ?int $vendorId = null
    ): void {
        if (!Schema::hasTable('audit_logs')) {
            return;
        }

        try {
            $actor ??= auth()->user();
            $vendorId ??= $this->resolveVendorId($auditable, $meta);

            AuditLog::query()->create([
                'actor_id' => $actor?->id,
                'vendor_id' => $vendorId,
                'event' => $event,
                'auditable_type' => $auditable ? $auditable::class : null,
                'auditable_id' => $auditable?->getKey(),
                'old_values' => empty($oldValues) ? null : $oldValues,
                'new_values' => empty($newValues) ? null : $newValues,
                'meta' => empty($meta) ? null : $meta,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);

            app(MonitoringAlertService::class)->safeHandleAuditEvent($event, $vendorId, $meta);
        } catch (Throwable $exception) {
            Log::error('Audit log write failed.', [
                'event' => $event,
                'error' => $exception->getMessage(),
                'auditable_type' => $auditable ? $auditable::class : null,
                'auditable_id' => $auditable?->getKey(),
            ]);
        }
    }

    private function resolveVendorId(?Model $auditable, array $meta): ?int
    {
        if (isset($meta['vendor_id']) && is_numeric($meta['vendor_id'])) {
            $vendorId = (int) $meta['vendor_id'];
            return $vendorId > 0 ? $vendorId : null;
        }

        if (!$auditable) {
            return null;
        }

        if (isset($auditable->vendor_id) && is_numeric($auditable->vendor_id)) {
            $vendorId = (int) $auditable->vendor_id;
            return $vendorId > 0 ? $vendorId : null;
        }

        if (method_exists($auditable, 'vendor') && $auditable->relationLoaded('vendor') && $auditable->vendor) {
            $vendorId = (int) ($auditable->vendor->id ?? 0);
            return $vendorId > 0 ? $vendorId : null;
        }

        return null;
    }
}
