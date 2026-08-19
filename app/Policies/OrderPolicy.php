<?php

namespace App\Policies;

use App\Domains\ECommerce\Models\Order;
use App\Models\User;
use App\Policies\Concerns\AuthorizesVendorOwnership;

class OrderPolicy
{
    use AuthorizesVendorOwnership;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->hasAnyRole(['vendor', 'customer']);
    }

    public function view(User $user, Order $order): bool
    {
        if ((int) $order->user_id === (int) $user->id) {
            return true;
        }

        return $this->ownsVendorRecord($user, (int) $order->vendor_id);
    }

    public function update(User $user, Order $order): bool
    {
        return $this->ownsVendorRecord($user, (int) $order->vendor_id);
    }

    public function cancel(User $user, Order $order): bool
    {
        if ((int) $order->user_id === (int) $user->id) {
            return true;
        }

        return $this->ownsVendorRecord($user, (int) $order->vendor_id);
    }
}
