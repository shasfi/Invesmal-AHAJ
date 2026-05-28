<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FirebaseAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $apiKey = config('services.firebase.api_key');

        if (empty($apiKey)) {
            return back()->withErrors([
                'email' => 'Firebase is not configured. Add FIREBASE_* keys to .env (see docs/SETUP_API_MAIL_SOCIAL.md).',
            ]);
        }

        $validated = $request->validate([
            'id_token' => ['required', 'string'],
            'role' => ['nullable', 'in:student_founder,investor,mentor'],
        ]);

        $response = Http::post(
            'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . $apiKey,
            ['idToken' => $validated['id_token']]
        );

        if (!$response->successful() || empty($response->json('users.0'))) {
            return back()->withErrors(['email' => 'Firebase sign-in failed. Check API key and enabled providers in Firebase Console.']);
        }

        $firebaseUser = $response->json('users.0');
        $email = $firebaseUser['email'] ?? null;
        $uid = $firebaseUser['localId'] ?? null;
        $name = $firebaseUser['displayName'] ?? ($email ? Str::before($email, '@') : 'Invesmal User');

        if (!$email || !$uid) {
            return back()->withErrors(['email' => 'Firebase did not return an email. Enable email on the provider or use another sign-in method.']);
        }

        $provider = collect($firebaseUser['providerUserInfo'] ?? [])->first()['providerId'] ?? 'firebase';

        $user = User::query()
            ->where('oauth_provider', $provider)
            ->where('oauth_id', $uid)
            ->first();

        if (!$user) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'oauth_provider' => $provider,
                    'oauth_id' => $uid,
                ]);
            }
        }

        if (!$user) {
            if (empty($validated['role'])) {
                $request->session()->put('firebase_pending', [
                    'provider' => $provider,
                    'oauth_id' => $uid,
                    'name' => $name,
                    'email' => $email,
                    'id_token' => $validated['id_token'],
                ]);

                return redirect()->route('firebase.role');
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => $validated['role'],
                'is_verified' => false,
                'oauth_provider' => $provider,
                'oauth_id' => $uid,
            ]);

            NotificationPreference::create(array_merge(
                ['user_id' => $user->id],
                NotificationPreference::defaultAttributes()
            ));
        }

        if (!$user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('status', 'Signed in successfully.');
    }

    public function showRoleForm(Request $request)
    {
        if (!$request->session()->has('firebase_pending')) {
            return redirect()->route('register');
        }

        return view('auth.firebase-role');
    }

    public function completeRegistration(Request $request)
    {
        $pending = $request->session()->get('firebase_pending');

        if (!$pending) {
            return redirect()->route('register');
        }

        $validated = $request->validate([
            'role' => ['required', 'in:student_founder,investor,mentor'],
        ]);

        $request->merge(['id_token' => $pending['id_token'], 'role' => $validated['role']]);
        $request->session()->forget('firebase_pending');

        return $this->authenticate($request);
    }
}
