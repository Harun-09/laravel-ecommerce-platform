<?php

namespace App\Notifications;

use App\Domains\ECommerce\Models\EmailTemplate;
use App\Domains\ECommerce\Models\Order;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Messages\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderLifecycleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 120;

    private ?Order $resolvedOrder = null;

    public function __construct(
        public int $orderId,
        public string $event,
        public ?float $refundAmount = null,
    ) {
        $this->event = strtolower(trim($event));
        $this->afterCommit();
        $this->onQueue('notifications');
    }

    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public static function eventPlaced(): string
    {
        return 'placed';
    }

    public static function eventShipped(): string
    {
        return 'shipped';
    }

    public static function eventDelivered(): string
    {
        return 'delivered';
    }

    public static function eventRefund(): string
    {
        return 'refund';
    }

    public function via(mixed $notifiable): array
    {
        $channels = ['mail'];

        if (!($notifiable instanceof AnonymousNotifiable)) {
            $channels[] = 'database';
        }

        $phone = $notifiable->routeNotificationFor('sms', $this);
        if (is_string($phone) && trim($phone) !== '') {
            $channels[] = SmsChannel::class;
        }

        return $channels;
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $order = $this->resolveOrder();
        [$subject, $bodyHtml, $actionUrl] = $this->resolveEmailContent($order, $notifiable);

        return (new MailMessage())
            ->subject($subject)
            ->view('emails.order_lifecycle', [
                'subject' => $subject,
                'greeting' => 'Hello,',
                'bodyHtml' => $bodyHtml,
                'actionUrl' => $actionUrl,
                'actionText' => 'View Order',
            ]);
    }

    public function toArray(mixed $notifiable): array
    {
        $order = $this->resolveOrder();

        return [
            'event' => $this->event,
            'order_id' => $this->orderId,
            'order_number' => $order?->order_number,
            'status' => $order?->status,
            'payment_status' => $order?->payment_status,
            'refund_amount' => $this->resolveRefundAmount($order),
            'message' => $this->lineForDatabase($order),
        ];
    }

    public function toSms(mixed $notifiable): SmsMessage
    {
        $order = $this->resolveOrder();
        return SmsMessage::make($this->lineForSms($order));
    }

    private function resolveOrder(): ?Order
    {
        if ($this->resolvedOrder) {
            return $this->resolvedOrder;
        }

        $this->resolvedOrder = Order::query()->find($this->orderId);
        return $this->resolvedOrder;
    }

    private function subjectFor(string $orderNumber): string
    {
        return match ($this->event) {
            self::eventPlaced() => "Order Placed: {$orderNumber}",
            self::eventShipped() => "Order Shipped: {$orderNumber}",
            self::eventDelivered() => "Order Delivered: {$orderNumber}",
            self::eventRefund() => "Refund Update: {$orderNumber}",
            default => "Order Update: {$orderNumber}",
        };
    }

    private function lineForMail(?Order $order): string
    {
        return match ($this->event) {
            self::eventPlaced() => 'Your order has been placed successfully and is now pending confirmation.',
            self::eventShipped() => $this->shipmentLine($order),
            self::eventDelivered() => 'Your order has been delivered successfully.',
            self::eventRefund() => $this->refundLine($order),
            default => 'Your order status has been updated.',
        };
    }

    private function lineForDatabase(?Order $order): string
    {
        return match ($this->event) {
            self::eventPlaced() => 'Order placed successfully.',
            self::eventShipped() => 'Order shipped.',
            self::eventDelivered() => 'Order delivered.',
            self::eventRefund() => $this->refundLine($order),
            default => 'Order updated.',
        };
    }

    private function lineForSms(?Order $order): string
    {
        $orderNumber = $order?->order_number ?? (string) $this->orderId;

        return match ($this->event) {
            self::eventPlaced() => "NovaMart: Order #{$orderNumber} placed successfully.",
            self::eventShipped() => "NovaMart: Order #{$orderNumber} shipped. " . $this->trackingSummary($order),
            self::eventDelivered() => "NovaMart: Order #{$orderNumber} delivered. Thank you for shopping with us.",
            self::eventRefund() => "NovaMart: Refund update for order #{$orderNumber}. " . $this->refundSummary($order),
            default => "NovaMart: Order #{$orderNumber} status updated.",
        };
    }

    private function shipmentLine(?Order $order): string
    {
        if (!$order) {
            return 'Your order has been shipped.';
        }

        $tracking = $this->trackingSummary($order);
        return 'Your order has been shipped. ' . $tracking;
    }

    private function trackingSummary(?Order $order): string
    {
        if (!$order) {
            return '';
        }

        $parts = [];
        if (!empty($order->shipping_carrier)) {
            $parts[] = 'Carrier: ' . $order->shipping_carrier;
        }

        if (!empty($order->tracking_number)) {
            $parts[] = 'Tracking: ' . $order->tracking_number;
        }

        return trim(implode(' | ', $parts));
    }

    private function refundLine(?Order $order): string
    {
        $amount = $this->resolveRefundAmount($order);
        $formatted = 'BDT ' . number_format($amount, 2);

        if ($amount > 0) {
            return "A refund of {$formatted} has been processed for your order.";
        }

        return 'A refund update has been processed for your order.';
    }

    private function refundSummary(?Order $order): string
    {
        $amount = $this->resolveRefundAmount($order);
        if ($amount <= 0) {
            return 'Please check your account for details.';
        }

        return 'Amount: BDT ' . number_format($amount, 2) . '.';
    }

    private function resolveRefundAmount(?Order $order): float
    {
        if ($this->refundAmount !== null) {
            return (float) $this->refundAmount;
        }

        if (!$order) {
            return 0;
        }

        return (float) ($order->refunded_amount ?? 0);
    }

    private function resolveEmailContent(?Order $order, mixed $notifiable): array
    {
        $orderNumber = $order?->order_number ?? ('#' . $this->orderId);
        $subject = $this->subjectFor($orderNumber);
        $actionUrl = $this->resolveOrderUrl($order, $notifiable);
        $bodyHtml = $this->defaultEmailBodyHtml($order);

        $template = EmailTemplate::query()
            ->active()
            ->where('slug', $this->templateSlugForEvent())
            ->first();

        if (!$template) {
            return [$subject, $bodyHtml, $actionUrl];
        }

        $templateData = $this->templateData($order, $notifiable, $actionUrl);

        return [
            $template->getSubjectRendered($templateData),
            $template->render($templateData),
            $actionUrl,
        ];
    }

    private function templateSlugForEvent(): string
    {
        return match ($this->event) {
            self::eventPlaced() => 'order-placed',
            self::eventShipped() => 'order-shipped',
            self::eventDelivered() => 'order-delivered',
            self::eventRefund() => 'order-refund',
            default => 'order-update',
        };
    }

    private function templateData(?Order $order, mixed $notifiable, ?string $actionUrl): array
    {
        return [
            'customer_name' => (string) ($notifiable->name ?? 'Customer'),
            'order_number' => (string) ($order?->order_number ?? $this->orderId),
            'order_total' => 'BDT ' . number_format((float) ($order?->total ?? 0), 2),
            'order_status' => $order ? Order::statusLabel((string) $order->status) : '',
            'payment_status' => $order ? ucfirst(str_replace('_', ' ', (string) $order->payment_status)) : '',
            'tracking_number' => (string) ($order?->tracking_number ?? ''),
            'shipping_carrier' => (string) ($order?->shipping_carrier ?? ''),
            'refund_amount' => 'BDT ' . number_format($this->resolveRefundAmount($order), 2),
            'order_url' => (string) ($actionUrl ?: config('app.url')),
        ];
    }

    private function defaultEmailBodyHtml(?Order $order): string
    {
        $segments = [
            '<p>' . e($this->lineForMail($order)) . '</p>',
        ];

        if ($order) {
            $segments[] = '<p><strong>Order Number:</strong> #' . e((string) $order->order_number) . '</p>';
            $segments[] = '<p><strong>Order Total:</strong> BDT ' . number_format((float) $order->total, 2) . '</p>';
        }

        $segments[] = '<p>Thank you for shopping with NovaMart.</p>';

        return implode("\n", $segments);
    }

    private function resolveOrderUrl(?Order $order, mixed $notifiable): ?string
    {
        if (
            !$order ||
            $notifiable instanceof AnonymousNotifiable ||
            !in_array($this->event, [self::eventPlaced(), self::eventShipped(), self::eventDelivered(), self::eventRefund()], true)
        ) {
            return null;
        }

        $router = app('router');
        if (!$router->has('account.orders.detail')) {
            return null;
        }

        try {
            return route('account.orders.detail', $order->order_number);
        } catch (\Throwable) {
            return null;
        }
    }
}
