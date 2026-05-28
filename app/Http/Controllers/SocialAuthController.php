<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected array $providers = ['facebook', 'google'];

    public function redirect(string $provider)
    {
        $this->ensureProvider($provider);
        $this->ensureConfigured($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request)
    {
        $this->ensureProvider($provider);
        $this->ensureConfigured($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Social login failed. Please try again or use email/password.']);
        }

        $user = User::query()
            ->where('oauth_provider', $provider)
            ->where('oauth_id', (string) $socialUser->getId())
            ->first();

        if (!$user && $socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'oauth_provider' => $provider,
                    'oauth_id' => (string) $socialUser->getId(),
                ]);
            }
        }

        if ($user) {
            if (!$user->hasVerifiedEmail()) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        $request->session()->put('oauth_pending', [
            'provider' => $provider,
            'oauth_id' => (string) $socialUser->getId(),
            'name' => $socialUser->getName() ?: 'Invesmal User',
            'email' => $socialUser->getEmail(),
            'avatar' => $socialUser->getAvatar(),
        ]);

        return redirect()->route('oauth.role');
    }

    public function showRoleForm(Request $request)
    {
        if (!$request->session()->has('oauth_pending')) {
            return redirect()->route('register');
        }

        return view('auth.oauth-role');
    }

    public function completeRegistration(Request $request)
    {
        $pending = $request->session()->get('oauth_pending');

        if (!$pending) {
            return redirect()->route('register');
        }

        $validated = $request->validate([
            'role' => ['required', 'in:student_founder,investor,mentor'],
        ]);

        if (empty($pending['email'])) {
            return back()->withErrors([
                'role' => 'This provider did not share an email. Please register with email/password instead.',
            ]);
        }

        $existing = User::where('email', $pending['email'])->first();

        if ($existing) {
            $existing->update([
                'oauth_provider' => $pending['provider'],
                'oauth_id' => $pending['oauth_id'],
            ]);
            if (!$existing->hasVerifiedEmail()) {
                $existing->forceFill(['email_verified_at' => now()])->save();
            }
            Auth::login($existing, true);
        } else {
            $user = User::create([
                'name' => $pending['name'],
                'email' => $pending['email'],
                'password' => Hash::make(Str::random(32)),
                'role' => $validated['role'],
                'is_verified' => false,
                'email_verified_at' => now(),
                'oauth_provider' => $pending['provider'],
                'oauth_id' => $pending['oauth_id'],
            ]);

            NotificationPreference::create(array_merge(
                ['user_id' => $user->id],
                NotificationPreference::defaultAttributes()
            ));

            Auth::login($user, true);
        }

        $request->session()->forget('oauth_pending');
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Signed in with ' . ucfirst($pending['provider']) . '!');
    }

    protected function ensureProvider(string $provider): void
    {
        if (!in_array($provider, $this->providers, true)) {
            abort(404);
        }
    }

    protected function ensureConfigured(string $provider): void
    {
        $id = config("services.{$provider}.client_id");
        $secret = config("services.{$provider}.client_secret");

        if (empty($id) || empty($secret)) {
            abort(503, ucfirst($provider) . ' login is not configured. See docs/SETUP_API_MAIL_SOCIAL.md');
        }
    }
}
