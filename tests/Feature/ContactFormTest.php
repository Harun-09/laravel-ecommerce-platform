<?php

namespace Tests\Feature;

use App\Mail\ContactMessageAcknowledgementMail;
use App\Mail\ContactMessageSubmittedMail;
use App\Domains\ECommerce\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
        config()->set('mail.contact.address', 'support@example.test');
        config()->set('mail.contact.name', 'Support Team');
    }

    public function test_guest_can_submit_contact_message(): void
    {
        Mail::fake();

        $response = $this->post(route('contact.submit'), [
            'name' => 'Guest User',
            'email' => 'guest@example.test',
            'phone' => '+8801711111111',
            'subject' => 'General Inquiry',
            'message' => 'I need help regarding delivery options for my location.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Your message has been received. Our support team will contact you soon.');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Guest User',
            'email' => 'guest@example.test',
            'subject' => 'General Inquiry',
            'status' => ContactMessage::STATUS_NEW,
            'user_id' => null,
        ]);

        Mail::assertSent(ContactMessageSubmittedMail::class, function (ContactMessageSubmittedMail $mail): bool {
            return $mail->hasTo('support@example.test')
                && $mail->contactMessage->email === 'guest@example.test';
        });

        Mail::assertSent(ContactMessageAcknowledgementMail::class, function (ContactMessageAcknowledgementMail $mail): bool {
            return $mail->hasTo('guest@example.test')
                && $mail->contactMessage->subject === 'General Inquiry';
        });
    }

    public function test_authenticated_user_submission_is_linked_with_user_id(): void
    {
        Mail::fake();

        $customer = $this->createUserWithRole('customer', [
            'name' => 'Auth Customer',
            'email' => 'auth-customer@example.test',
        ]);

        $response = $this->actingAs($customer)->post(route('contact.submit'), [
            'name' => 'Auth Customer',
            'email' => 'auth-customer@example.test',
            'phone' => '',
            'subject' => 'Order Support',
            'message' => 'My latest order needs an update from support team.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'user_id' => $customer->id,
            'email' => 'auth-customer@example.test',
            'subject' => 'Order Support',
        ]);

        Mail::assertSent(ContactMessageSubmittedMail::class, function (ContactMessageSubmittedMail $mail): bool {
            return $mail->hasTo('support@example.test')
                && $mail->contactMessage->user_id !== null;
        });

        Mail::assertSent(ContactMessageAcknowledgementMail::class, function (ContactMessageAcknowledgementMail $mail): bool {
            return $mail->hasTo('auth-customer@example.test');
        });
    }

    public function test_contact_submission_requires_valid_payload(): void
    {
        Mail::fake();

        $response = $this->from(route('contact'))->post(route('contact.submit'), [
            'name' => '',
            'email' => 'not-an-email',
            'subject' => '',
            'message' => 'short',
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }
}
