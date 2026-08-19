<?php

namespace Tests\Feature;

use App\Notifications\OrderLifecycleNotification;
use App\Services\OrderNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class OrderNotificationQueueTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
    }

    public function test_order_lifecycle_notifications_are_queued_on_notifications_queue(): void
    {
        Queue::fake();

        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $order = $this->createOrderForUser($customer, $vendor, [
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
        ]);

        app(OrderNotificationService::class)->sendOrderPlaced($order->fresh('user'));

        Queue::assertPushed(SendQueuedNotifications::class, function (SendQueuedNotifications $job) use ($customer): bool {
            return $job->queue === 'notifications'
                && $job->notification instanceof OrderLifecycleNotification
                && $job->notifiables->contains(fn($notifiable) => (int) $notifiable->id === (int) $customer->id);
        });
    }

    public function test_order_lifecycle_notification_retry_policy_is_configured(): void
    {
        $notification = new OrderLifecycleNotification(101, OrderLifecycleNotification::eventPlaced());

        $this->assertSame('notifications', (string) $notification->queue);
        $this->assertSame(3, $notification->tries);
        $this->assertSame(120, $notification->timeout);
        $this->assertSame([10, 30, 60], $notification->backoff());
    }
}
