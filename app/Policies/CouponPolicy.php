<?php

namespace App\Policies;

use App\Domains\ECommerce\Models\Coupon;
use App\Models\User;
use App\Policies\Concerns\AuthorizesVendorOwnership;

class CouponPolicy
{
    use AuthorizesVendorOwnership;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->hasRole('vendor');
    }

    public function view(User $user, Coupon $coupon): bool
    {
        return $this->ownsVendorRecord($user, (int) $coupon->vendor_id);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user) || $user->hasRole('vendor');
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $this->ownsVendorRecord($user, (int) $coupon->vendor_id);
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $this->ownsVendorRecord($user, (int) $coupon->vendor_id);
    }
}
