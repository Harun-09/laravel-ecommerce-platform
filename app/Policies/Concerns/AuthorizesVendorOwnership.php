<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait AuthorizesVendorOwnership
{
    protected function isAdmin(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    protected function vendorIdOf(User $user): ?int
    {
        $vendorId = $user->vendor?->id;
        return $vendorId ? (int) $vendorId : null;
    }

    protected function ownsVendorRecord(User $user, ?int $vendorId): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if (!$user->hasRole('vendor') || !$vendorId) {
            return false;
        }

        return $this->vendorIdOf($user) === (int) $vendorId;
    }
}
