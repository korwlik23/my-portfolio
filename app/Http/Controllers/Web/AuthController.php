<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Services\AuditLogger;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function showRegister()
    {
        return Auth::check() ? redirect('/dashboard') : view('auth.register');
    }

    public function register(Request $request)
    {
        $systemSettings = app(\App\Services\SystemSettingsService::class)->all();

        $passwordRule = ($systemSettings['bypass_password_validation'] ?? false)
            ? ['required', 'confirmed']
            : ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => $passwordRule,
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_type' => 'system',
            'status' => 'active',
        ]);

        if (Role::where('name', 'user')->where('guard_name', 'web')->exists()) {
            $user->assignRole('user');
        }

        Auth::login($user);
        app(AuditLogger::class)->log('register', $user, "User registered: {$user->email}");

        if ($systemSettings['require_email_verification'] ?? true) {
            event(new Registered($user));
            return redirect()->route('verification.notice');
        }

        $user->markEmailAsVerified();

        return redirect()->route('dashboard')->with('success', __('messages.create_success'));
    }

    public function showVerificationNotice()
    {
        return auth()->user()->hasVerifiedEmail()
            ? redirect()->intended('/dashboard')
            : view('auth.verify-email');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('dashboard')->with('success', __('messages.email_verified'));
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', __('messages.verification_link_sent'));
    }

    public function showLogin()
    {
        return Auth::check() ? redirect('/dashboard') : view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->isBanned()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $banReason = $user->ban_reason ? ' (' . __('messages.reason') . ': ' . $user->ban_reason . ')' : '';

                return back()->withErrors(['email' => __('messages.account_banned') . $banReason])->withInput();
            }

            $user->update(['last_login_at' => now()]);
            app(AuditLogger::class)->log('login', $user, "User logged in: {$user->email}");

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => __('messages.login_failed')])->withInput();
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            app(AuditLogger::class)->log('logout', $user, "User logged out: {$user->email}");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['success' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
