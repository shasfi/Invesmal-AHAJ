<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\NotificationService;
use App\Services\RecaptchaService;
use App\Support\DevMailHelper;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Symfony\Component\Mailer\Exception\TransportException;

class AuthController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private RecaptchaService $recaptchaService,
    ) {}

    public function showLoginForm()
    {
        return view('auth.form', ['mode' => 'login']);
    }

    public function login(LoginRequest $request)
    {
        $user = User::query()
            ->where('email', $request->email)
            ->where('role', $request->role)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid email, password, or role.'])->withInput();
    }

    public function showRegisterForm()
    {
        return view('auth.form', ['mode' => 'register']);
    }

    public function register(RegisterRequest $request)
    {
        if (!$this->recaptchaService->verify($request->input('recaptcha_token'), 'REGISTER')) {
            return back()->withErrors(['email' => 'Security verification failed. Please try again.'])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_verified' => false,
        ]);

        NotificationPreference::create(array_merge(
            ['user_id' => $user->id],
            NotificationPreference::defaultAttributes()
        ));

        Auth::login($user);

        $mailSent = $this->sendVerificationEmail($user);

        return redirect()->route('verification.notice')
            ->with('mail_sent', $mailSent)
            ->with('mail_error', session('mail_error'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }

    /**
     * Display the forgot password form.
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link to the user's email.
     */
    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $email = $request->validated('email');
        $user = User::where('email', $email)->where('role', $request->validated('role'))->first();

        // Do not reveal whether the email exists
        if (!$user) {
            return back()->with('reset_sent', true);
        }

        $broker = Password::broker();

        if ($broker->getRepository()->recentlyCreatedToken($user)) {
            return back()->withErrors(['email' => __('passwords.throttled')]);
        }

        $token = $broker->createToken($user);

        try {
            $user->sendPasswordResetNotification($token);
        } catch (TransportException $e) {
            report($e);

            $hint = match (config('mail.default')) {
                'resend' => 'Resend free plan only delivers to your Resend account email until you verify a domain. Use the reset link below, or switch to Mailtrap sandbox in .env.',
                'smtp' => 'SMTP failed (check MAIL_USERNAME and MAIL_PASSWORD in .env). Use the reset link below to continue.',
                default => 'Email could not be sent. Use the reset link below.',
            };

            return back()
                ->withInput()
                ->withErrors(['email' => $hint])
                ->with([
                    'dev_reset_url' => route('password.reset', [
                        'token' => $token,
                        'email' => $email,
                        'role' => $user->role,
                    ]),
                    'smtp_help' => true,
                ]);
        }

        $flash = ['reset_sent' => true];

        if (DevMailHelper::usesLogDriver()) {
            $flash['dev_reset_url'] = route('password.reset', [
                'token' => $token,
                'email' => $email,
                'role' => $user->role,
            ]);
        }

        return back()->with($flash);
    }

    /**
     * Display the password reset form (must not auto-login).
     */
    public function showResetForm(Request $request, string $token)
    {
        $email = $request->query('email');
        $role = $request->query('role');

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('password.reset', [
                'token' => $token,
                'email' => $email,
                'role' => $role,
            ]);
        }

        if (!$email || !$role) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'Invalid reset link. Please request a new one.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
            'role' => $role,
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::query()
            ->where('email', $request->email)
            ->where('role', $request->role)
            ->first();

        $broker = Password::broker();

        if (!$user || !$broker->tokenExists($user, $request->token)) {
            return back()->withErrors(['email' => __('passwords.token')]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        $broker->deleteToken($user);

        return redirect()
            ->route('login')
            ->with('status', 'Password updated. Sign in with your new password.');
    }

    /**
     * Display email verification notice (only while email is unverified).
     */
    public function showVerifyNotice(Request $request)
    {
        $user = $request->user()->fresh();
        Auth::setUser($user);

        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('dashboard')
                ->with('status', 'Your email is verified. Welcome back!');
        }

        return view('auth.verify-email', [
            'email' => $user->email,
            'role' => $user->role,
            'mailSent' => session('mail_sent', true),
            'mailError' => session('mail_error'),
        ]);
    }

    /**
     * User verified on another Chrome/device — refresh status on this browser.
     */
    public function checkVerificationStatus(Request $request)
    {
        $user = $request->user()->fresh();
        Auth::setUser($user);

        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('dashboard')
                ->with('status', 'Email verified! You can use your account now.');
        }

        return back()->with('status', 'not-verified-yet');
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $mailSent = $this->sendVerificationEmail($user);

        return redirect()->route('verification.notice')
            ->with('mail_sent', $mailSent)
            ->with('mail_error', session('mail_error'));
    }

    public function wrongAccount(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('register');
    }

    protected function sendVerificationEmail(User $user): bool
    {
        try {
            $user->sendEmailVerificationNotification();
            session()->forget('mail_error');

            return true;
        } catch (TransportException $e) {
            report($e);
            session()->flash('mail_error', 'SMTP error: could not send email. Check Gmail app password in .env or try Resend.');

            return false;
        } catch (\Throwable $e) {
            report($e);
            session()->flash('mail_error', 'Email failed: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Verify email from signed link — any device, any browser session.
     * Always signs in the user who owns the link (correct Gmail / profile).
     */
    public function verifyEmail(Request $request, string $id, string $hash)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Invalid verification link. Please register again or contact support.']);
        }

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Invalid verification link for this email.']);
        }

        if (!$request->hasValidSignature()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'This verification link has expired. Log in with ' . $user->email . ' and click “Resend verification email”.']);
        }

        $alreadyVerified = $user->hasVerifiedEmail();

        if (!$alreadyVerified) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        Auth::login($user);
        $request->session()->regenerate();

        $message = $alreadyVerified
            ? 'Your email (' . $user->email . ') was already verified. You are now signed in to the correct account.'
            : 'Email verified successfully! Welcome to Invesmal, ' . $user->name . '. Your account is ready.';

        return redirect()->route('dashboard')->with('status', $message);
    }
}
