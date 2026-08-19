<?php

namespace App\Policies;

use App\Models\User;
use App\Domains\ECommerce\Models\VendorPayout;
use App\Policies\Concerns\AuthorizesVendorOwnership;

class VendorPayoutPolicy
{
    use AuthorizesVendorOwnership;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->hasRole('vendor');
    }

    public function view(User $user, VendorPayout $vendorPayout): bool
    {
        return $this->ownsVendorRecord($user, (int) $vendorPayout->vendor_id);
    }
}
