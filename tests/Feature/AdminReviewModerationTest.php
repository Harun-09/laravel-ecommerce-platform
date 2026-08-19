<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class AdminReviewModerationTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();
    }

    public function test_admin_can_view_review_moderation_index_and_detail_pages(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor);
        $review = $this->createReview($customer, $product, null, [
            'title' => 'Solid item',
            'comment' => 'Shipping was fast and the product quality is reliable.',
        ]);

        $indexResponse = $this->actingAs($admin)->get(route('admin.reviews.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Review Moderation');
        $indexResponse->assertSee('Solid item');
        $indexResponse->assertSee(route('admin.reviews.show', $review) . '#reply-form', false);

        $showResponse = $this->actingAs($admin)->get(route('admin.reviews.show', $review));
        $showResponse->assertOk();
        $showResponse->assertSee('Review #' . $review->id);
        $showResponse->assertSee('Solid item');
        $showResponse->assertSee($customer->name);
    }

    public function test_admin_can_approve_and_reject_review(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor);
        $review = $this->createReview($customer, $product, null, [
            'rating' => 5,
            'is_approved' => false,
            'is_verified_purchase' => true,
        ]);

        $product->updateRating();
        $this->assertSame(0.0, (float) $product->fresh()->rating);
        $this->assertSame(0, (int) $product->fresh()->reviews_count);

        $approveResponse = $this->actingAs($admin)->patch(route('admin.reviews.approve', $review));
        $approveResponse->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_approved' => true,
        ]);

        $product->refresh();
        $this->assertSame(5.0, (float) $product->rating);
        $this->assertSame(1, (int) $product->reviews_count);

        $rejectResponse = $this->actingAs($admin)->patch(route('admin.reviews.reject', $review));
        $rejectResponse->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_approved' => false,
        ]);

        $product->refresh();
        $this->assertSame(0.0, (float) $product->rating);
        $this->assertSame(0, (int) $product->reviews_count);
    }

    public function test_admin_can_add_reply_to_review(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor);
        $review = $this->createReview($customer, $product);

        $response = $this->actingAs($admin)->put(route('admin.reviews.reply', $review), [
            'admin_reply' => 'Thank you for your feedback. Our team has noted your comments.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'admin_reply' => 'Thank you for your feedback. Our team has noted your comments.',
        ]);
        $this->assertNotNull($review->fresh()->admin_replied_at);
    }

    public function test_admin_can_filter_reviews_by_reply_status(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor);

        $repliedReview = $this->createReview($customer, $product, null, [
            'title' => 'Replied Review',
        ]);
        $repliedReview->addReply('Thanks for your feedback.');

        $pendingReplyReview = $this->createReview($customer, $product, null, [
            'title' => 'Pending Reply Review',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reviews.index', ['replied' => 'yes']))
            ->assertOk()
            ->assertSee('Replied Review')
            ->assertDontSee('Pending Reply Review');

        $this->actingAs($admin)
            ->get(route('admin.reviews.index', ['replied' => 'no']))
            ->assertOk()
            ->assertSee('Pending Reply Review')
            ->assertDontSee('Replied Review');
    }

    public function test_review_moderation_routes_require_proper_permissions(): void
    {
        Role::findByName('admin')->revokePermissionTo('view reviews');
        Role::findByName('admin')->revokePermissionTo('approve reviews');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = $this->createUserWithRole('admin');
        $customer = $this->createUserWithRole('customer');
        $vendor = $this->createApprovedVendor();
        $product = $this->createProduct($vendor);
        $review = $this->createReview($customer, $product);

        $this->actingAs($admin)
            ->get(route('admin.reviews.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('admin.reviews.approve', $review))
            ->assertForbidden();
    }
}
