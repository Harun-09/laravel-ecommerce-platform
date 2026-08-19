<?php

namespace Tests\Feature;

use App\Models\MonitoringAlert;
use App\Services\PaymentEventLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class AdminObservabilityTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
    }

    public function test_super_admin_can_view_and_resolve_observability_alerts(): void
    {
        $superAdmin = $this->createUserWithRole('super-admin');
        $alert = MonitoringAlert::query()->create([
            'type' => 'payment_failures',
            'severity' => 'critical',
            'source' => 'payment:stripe',
            'title' => 'Payment failure spike detected',
            'description' => 'Failures crossed threshold',
            'status' => MonitoringAlert::STATUS_OPEN,
            'triggered_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->get(route('admin.observability.index'))
            ->assertOk()
            ->assertSee('Observability')
            ->assertSee($alert->title);

        $this->actingAs($superAdmin)
            ->patch(route('admin.observability.alerts.resolve', $alert))
            ->assertRedirect();

        $this->assertDatabaseHas('monitoring_alerts', [
            'id' => $alert->id,
            'status' => MonitoringAlert::STATUS_RESOLVED,
            'resolved_by' => $superAdmin->id,
        ]);
    }

    public function test_admin_without_super_admin_role_cannot_access_observability_dashboard(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.observability.index'))
            ->assertForbidden();
    }

    public function test_payment_event_logger_triggers_payment_failure_alert_after_threshold(): void
    {
        config()->set('observability.payment_failure_alert.threshold', 2);
        config()->set('observability.payment_failure_alert.window_minutes', 60);

        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $order = $this->createOrderForUser($customer, $vendor, [
            'payment_method' => 'stripe',
            'payment_status' => 'pending',
        ]);
        $payment = $this->createPaymentForOrder($order, [
            'payment_method' => 'stripe',
            'status' => 'pending',
        ]);

        $logger = app(PaymentEventLogger::class);

        $logger->log('stripe.checkout_initiation_failed', [
            'order' => $order,
            'payment' => $payment,
            'provider' => 'stripe',
            'status' => 'failed',
            'severity' => 'error',
            'message' => 'First failure',
        ]);

        $logger->log('stripe.checkout_initiation_failed', [
            'order' => $order,
            'payment' => $payment,
            'provider' => 'stripe',
            'status' => 'failed',
            'severity' => 'error',
            'message' => 'Second failure',
        ]);

        $this->assertDatabaseCount('payment_event_logs', 2);
        $this->assertDatabaseHas('monitoring_alerts', [
            'type' => 'payment_failures',
            'source' => 'payment:stripe',
            'status' => MonitoringAlert::STATUS_OPEN,
        ]);
    }
}
