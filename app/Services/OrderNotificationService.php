<?php

namespace App\Services;

use App\Domains\ECommerce\Models\Order;
use App\Models\User;
use App\Notifications\AdminOrderActivityNotification;
use App\Notifications\OrderLifecycleNotification;
use Illuminate\Support\Facades\Notification;

class OrderNotificationService
{
    public function sendOrderPlaced(Order $order): void
    {
        $event = OrderLifecycleNotification::eventPlaced();
        $this->notifyCustomer($order, $event);
        $this->notifyAdmins($order, $event);
    }

    public function sendOrderShipped(Order $order): void
    {
        $event = OrderLifecycleNotification::eventShipped();
        $this->notifyCustomer($order, $event);
        $this->notifyAdmins($order, $event);
    }

    public function sendOrderDelivered(Order $order): void
    {
        $event = OrderLifecycleNotification::eventDelivered();
        $this->notifyCustomer($order, $event);
        $this->notifyAdmins($order, $event);
    }

    public function sendOrderRefunded(Order $order, ?float $refundAmount = null): void
    {
        $event = OrderLifecycleNotification::eventRefund();
        $this->notifyCustomer($order, $event, $refundAmount);
        $this->notifyAdmins($order, $event, $refundAmount);
    }

    private function notifyCustomer(Order $order, string $event, ?float $refundAmount = null): void
    {
        $notification = new OrderLifecycleNotification((int) $order->id, $event, $refundAmount);

        $order->loadMissing('user');

        if ($order->user) {
            $order->user->notify($notification);
            return;
        }

        $anonymous = null;

        if (!empty($order->shipping_email)) {
            $anonymous = Notification::route('mail', $order->shipping_email);
        }

        if (!empty($order->shipping_phone)) {
            $anonymous = $anonymous
                ? $anonymous->route('sms', $order->shipping_phone)
                : Notification::route('sms', $order->shipping_phone);
        }

        if ($anonymous) {
            $anonymous->notify($notification);
        }
    }

    private function notifyAdmins(Order $order, string $event, ?float $refundAmount = null): void
    {
        User::query()
            ->role(['super-admin', 'admin'])
            ->select(['id'])
            ->get()
            ->each(function (User $admin) use ($order, $event, $refundAmount): void {
                $admin->notify(new AdminOrderActivityNotification((int) $order->id, $event, $refundAmount));
            });
    }
}
