<?php

namespace App\Policies;

use App\Domains\ECommerce\Models\Product;
use App\Models\User;
use App\Policies\Concerns\AuthorizesVendorOwnership;

class ProductPolicy
{
    use AuthorizesVendorOwnership;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->hasRole('vendor');
    }

    public function view(User $user, Product $product): bool
    {
        return $this->ownsVendorRecord($user, (int) $product->vendor_id);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user) || $user->hasRole('vendor');
    }

    public function update(User $user, Product $product): bool
    {
        return $this->ownsVendorRecord($user, (int) $product->vendor_id);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->ownsVendorRecord($user, (int) $product->vendor_id);
    }
}
