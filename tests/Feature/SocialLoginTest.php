<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\Feature\Concerns\BuildsEcommerceData;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;
    use BuildsEcommerceData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolePermissions();

        config()->set('services.google.client_id', 'google-client-id');
        config()->set('services.google.client_secret', 'google-client-secret');
        config()->set('services.google.redirect', 'http://localhost/auth/google/callback');
        config()->set('services.facebook.client_id', 'facebook-client-id');
        config()->set('services.facebook.client_secret', 'facebook-client-secret');
        config()->set('services.facebook.redirect', 'http://localhost/auth/facebook/callback');
    }

    public function test_social_redirect_route_redirects_to_provider_consent_page(): void
    {
        $providerMock = Mockery::mock();
        $providerMock->shouldReceive('stateless')->once()->andReturnSelf();
        $providerMock->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.test/oauth'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($providerMock);

        $response = $this->get(route('auth.social.redirect', ['provider' => 'google']));

        $response->assertRedirect('https://accounts.google.test/oauth');
    }

    public function test_social_callback_creates_customer_account_and_logs_user_in(): void
    {
        $socialUser = Mockery::mock(SocialiteUserContract::class);
        $socialUser->shouldReceive('getId')->once()->andReturn('google-user-101');
        $socialUser->shouldReceive('getEmail')->once()->andReturn('social-user@example.test');
        $socialUser->shouldReceive('getName')->once()->andReturn('Social User');
        $socialUser->shouldReceive('getAvatar')->once()->andReturn('https://avatar.example.test/social-user.png');

        $providerMock = Mockery::mock();
        $providerMock->shouldReceive('stateless')->once()->andReturnSelf();
        $providerMock->shouldReceive('user')->once()->andReturn($socialUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($providerMock);

        $response = $this->get(route('auth.social.callback', ['provider' => 'google']));

        $response->assertRedirect('/');
        $response->assertSessionHasNoErrors();

        $user = User::query()->where('email', 'social-user@example.test')->first();
        $this->assertNotNull($user);
        $this->assertSame('google-user-101', $user->google_id);
        $this->assertTrue($user->hasRole('customer'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_social_callback_links_existing_user_and_preserves_role_redirect(): void
    {
        $admin = $this->createUserWithRole('admin', [
            'name' => 'Admin User',
            'email' => 'admin-social@example.test',
            'google_id' => null,
        ]);

        $socialUser = Mockery::mock(SocialiteUserContract::class);
        $socialUser->shouldReceive('getId')->once()->andReturn('google-admin-501');
        $socialUser->shouldReceive('getEmail')->once()->andReturn('admin-social@example.test');
        $socialUser->shouldReceive('getName')->once()->andReturn('Admin User');
        $socialUser->shouldReceive('getAvatar')->once()->andReturn(null);

        $providerMock = Mockery::mock();
        $providerMock->shouldReceive('stateless')->once()->andReturnSelf();
        $providerMock->shouldReceive('user')->once()->andReturn($socialUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($providerMock);

        $response = $this->get(route('auth.social.callback', ['provider' => 'google']));

        $response->assertRedirect(route('admin.dashboard'));

        $admin->refresh();
        $this->assertSame('google-admin-501', $admin->google_id);
        $this->assertAuthenticatedAs($admin);
        $this->assertTrue($admin->hasRole('admin'));
    }

    public function test_facebook_social_callback_creates_customer_account(): void
    {
        $socialUser = Mockery::mock(SocialiteUserContract::class);
        $socialUser->shouldReceive('getId')->once()->andReturn('facebook-user-991');
        $socialUser->shouldReceive('getEmail')->once()->andReturn('fb-user@example.test');
        $socialUser->shouldReceive('getName')->once()->andReturn('Facebook User');
        $socialUser->shouldReceive('getAvatar')->once()->andReturn('https://avatar.example.test/fb-user.png');

        $providerMock = Mockery::mock();
        $providerMock->shouldReceive('stateless')->once()->andReturnSelf();
        $providerMock->shouldReceive('user')->once()->andReturn($socialUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('facebook')
            ->andReturn($providerMock);

        $response = $this->get(route('auth.social.callback', ['provider' => 'facebook']));

        $response->assertRedirect('/');
        $response->assertSessionHasNoErrors();

        $user = User::query()->where('email', 'fb-user@example.test')->first();
        $this->assertNotNull($user);
        $this->assertSame('facebook-user-991', $user->facebook_id);
        $this->assertTrue($user->hasRole('customer'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_social_provider_redirects_to_login_with_error(): void
    {
        $response = $this->from(route('login'))
            ->get(route('auth.social.redirect', ['provider' => 'twitter']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Unsupported social login provider.');
    }
}
