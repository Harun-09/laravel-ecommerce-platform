<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Throwable;

class AuthController extends Controller
{
    private const SOCIAL_PROVIDERS = ['google', 'facebook'];

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = auth()->user();

            // Redirect based on role
            if ($user->hasAnyRole(['super-admin', 'admin'])) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('vendor')) {
                return redirect()->intended(route('vendor.dashboard'));
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('customer');

        Auth::login($user);

        return redirect('/');
    }

    public function redirectToProvider(string $provider): RedirectResponse
    {
        $provider = $this->resolveProvider($provider);

        if (!$provider) {
            return redirect()->route('login')
                ->with('error', 'Unsupported social login provider.');
        }

        if (!$this->isProviderConfigured($provider)) {
            return redirect()->route('login')
                ->with('error', ucfirst($provider) . ' login is not configured yet.');
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider): RedirectResponse
    {
        $provider = $this->resolveProvider($provider);

        if (!$provider) {
            return redirect()->route('login')
                ->with('error', 'Unsupported social login provider.');
        }

        if (!$this->isProviderConfigured($provider)) {
            return redirect()->route('login')
                ->with('error', ucfirst($provider) . ' login is not configured yet.');
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (Throwable $exception) {
            Log::warning('Social login callback failed.', [
                'provider' => $provider,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Social login failed. Please try again.');
        }

        $providerId = trim((string) $socialUser->getId());
        $email = strtolower(trim((string) $socialUser->getEmail()));

        if ($providerId === '' || $email === '') {
            return redirect()->route('login')
                ->with('error', 'Unable to continue social login without a verified email address.');
        }

        $providerColumn = $provider . '_id';

        $user = User::query()
            ->where($providerColumn, $providerId)
            ->first();

        if (!$user) {
            $user = User::query()
                ->where('email', $email)
                ->first();
        }

        $name = trim((string) $socialUser->getName());
        if ($name === '') {
            $name = ucfirst((string) strstr($email, '@', true));
        }
        $avatar = trim((string) $socialUser->getAvatar());

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
                $providerColumn => $providerId,
                'avatar' => $avatar !== '' ? $avatar : null,
            ]);

            $this->assignDefaultCustomerRole($user);
        } else {
            $updates = [];

            if ((string) ($user->{$providerColumn} ?? '') === '') {
                $updates[$providerColumn] = $providerId;
            }

            if (empty($user->avatar) && $avatar !== '') {
                $updates['avatar'] = $avatar;
            }

            if (empty($user->name) && $name !== '') {
                $updates['name'] = $name;
            }

            if ($user->email_verified_at === null) {
                $updates['email_verified_at'] = now();
            }

            if ($updates !== []) {
                $user->update($updates);
            }
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function resolveProvider(string $provider): ?string
    {
        $provider = strtolower(trim($provider));
        return in_array($provider, self::SOCIAL_PROVIDERS, true) ? $provider : null;
    }

    private function isProviderConfigured(string $provider): bool
    {
        $clientId = trim((string) config("services.{$provider}.client_id"));
        $clientSecret = trim((string) config("services.{$provider}.client_secret"));
        $redirect = trim((string) config("services.{$provider}.redirect"));

        return $clientId !== '' && $clientSecret !== '' && $redirect !== '';
    }

    private function assignDefaultCustomerRole(User $user): void
    {
        $customerRoleExists = Role::query()
            ->where('name', 'customer')
            ->where('guard_name', 'web')
            ->exists();

        if ($customerRoleExists && !$user->hasRole('customer')) {
            $user->assignRole('customer');
        }
    }

    private function redirectByRole(User $user): RedirectResponse
    {
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('vendor')) {
            return redirect()->intended(route('vendor.dashboard'));
        }

        return redirect()->intended('/');
    }
}
