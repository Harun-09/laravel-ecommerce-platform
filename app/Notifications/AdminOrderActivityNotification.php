<?php

namespace App\Notifications;

use App\Domains\ECommerce\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminOrderActivityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $orderId,
        public string $event,
        public ?float $refundAmount = null,
    ) {
        $this->event = strtolower(trim($event));
        $this->afterCommit();
        $this->onQueue('notifications');
    }

    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    public function toArray(mixed $notifiable): array
    {
        $order = Order::query()->find($this->orderId);
        $orderNumber = (string) ($order?->order_number ?? $this->orderId);

        $message = match ($this->event) {
            'placed' => "Order #{$orderNumber} has been placed.",
            'shipped' => "Order #{$orderNumber} has been shipped.",
            'delivered' => "Order #{$orderNumber} has been delivered.",
            'refund' => $this->refundMessage($orderNumber),
            default => "Order #{$orderNumber} has been updated.",
        };

        return [
            'event' => $this->event,
            'order_id' => $order?->id ?? $this->orderId,
            'order_number' => $order?->order_number,
            'message' => $message,
        ];
    }

    private function refundMessage(string $orderNumber): string
    {
        $amount = $this->refundAmount;
        if ($amount !== null && $amount > 0) {
            return "Refund processed for order #{$orderNumber} (BDT " . number_format($amount, 2) . ").";
        }

        return "Refund status updated for order #{$orderNumber}.";
    }
}
