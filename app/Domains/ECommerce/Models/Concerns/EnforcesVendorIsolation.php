<?php

namespace App\Domains\ECommerce\Models\Concerns;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;

trait EnforcesVendorIsolation
{
    protected static function bootEnforcesVendorIsolation(): void
    {
        static::addGlobalScope('vendor_isolation', function (Builder $query): void {
            $user = auth()->user();

            if (!$user instanceof User || !$user->hasRole('vendor') || $user->hasAnyRole(['admin', 'super-admin'])) {
                return;
            }

            $request = request();
            $isVendorContext = $request?->routeIs('vendor.*') || $request?->is('vendor/*');

            if (!$isVendorContext) {
                return;
            }

            $vendorId = (int) ($user->vendor?->id ?? 0);
            if ($vendorId <= 0) {
                $query->whereRaw('1 = 0');
                return;
            }

            $query->where($query->qualifyColumn('vendor_id'), $vendorId);
        });

        static::creating(function ($model): void {
            $user = auth()->user();

            if (!$user instanceof User || !$user->hasRole('vendor') || $user->hasAnyRole(['admin', 'super-admin'])) {
                return;
            }

            $vendorId = (int) ($user->vendor?->id ?? 0);
            if ($vendorId <= 0) {
                throw new AuthorizationException('Vendor account not found for authenticated user.');
            }

            if (empty($model->vendor_id)) {
                $model->vendor_id = $vendorId;
                return;
            }

            if ((int) $model->vendor_id !== $vendorId) {
                throw new AuthorizationException('You cannot create records for another vendor.');
            }
        });
    }

    public function scopeForVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where($query->qualifyColumn('vendor_id'), $vendorId);
    }

    public function scopeForCurrentVendor(Builder $query, ?User $user = null): Builder
    {
        $user ??= auth()->user();

        if (!$user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        $request = request();
        $isVendorContext = $request?->routeIs('vendor.*') || $request?->is('vendor/*');

        if ($user->hasRole('vendor') && (!$user->hasAnyRole(['admin', 'super-admin']) || $isVendorContext)) {
            $vendorId = (int) ($user->vendor?->id ?? 0);
            if ($vendorId <= 0) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where($query->qualifyColumn('vendor_id'), $vendorId);
        }

        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return $query;
        }

        return $query->whereRaw('1 = 0');
    }
}

