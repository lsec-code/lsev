<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

use App\Traits\VerifiesCaptcha;

class SecurityResetController extends Controller
{
    use VerifiesCaptcha;

    /**
     * Handle the email check request.
     */
    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $captchaDriver = \App\Models\SiteSetting::where('setting_key', 'captcha_driver')->value('setting_value') ?? 'none';
        
        if ($captchaDriver === 'google') {
            $request->validate([
                'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                    if (!$this->verifyCaptcha($value, 'google')) {
                        $fail('Verifikasi Captcha gagal. Silakan coba lagi.');
                    }
                }]
            ]);
        } elseif ($captchaDriver === 'cloudflare') {
            $request->validate([
                'cf-turnstile-response' => ['required', function ($attribute, $value, $fail) {
                    if (!$this->verifyCaptcha($value, 'cloudflare')) {
                        $fail('Verifikasi Captcha gagal. Silakan coba lagi.');
                    }
                }]
            ]);
        }
        
        \Illuminate\Support\Facades\Log::info('Forgot Password Check initiated for: ' . $request->email);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            \Illuminate\Support\Facades\Log::warning('User not found: ' . $request->email);
            return back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.']);
        }

        // Store email in session for the next step
        session(['reset_password_email' => $user->email]);
        
        return redirect()->route('password.reset.verify');
    }

    /**
     * Show the combined security verification and password reset form.
     */
    public function showSecurityResetForm()
    {
        $email = session('reset_password_email');
        
        if (!$email) {
            return redirect()->route('password.request');
        }
        
        $user = User::where('email', $email)->firstOrFail();
        
        // Check for rate limiting/cooldown
        $cacheKey = 'password_reset_attempts_' . str_replace(['@', '.'], '_', $user->email);
        $attempts = \Illuminate\Support\Facades\Cache::get($cacheKey, 0);
        $lockoutTime = \Illuminate\Support\Facades\Cache::get($cacheKey . '_lockout');
        
        $cooldown = false;
        $remainingSeconds = 0;
        
        if ($lockoutTime && now()->lt($lockoutTime)) {
            $cooldown = true;
            $remainingSeconds = now()->diffInSeconds($lockoutTime);
        }
        
        return view('auth.verify-security', [
            'email' => $user->email,
            'hint' => $user->security_question ?? 'Tidak ada pertanyaan keamanan (Hubungi Admin)',
            'has_question' => !empty($user->security_answer),
            'cooldown' => $cooldown,
            'remainingSeconds' => $remainingSeconds,
            'attempts' => $attempts,
            'mode' => 'reset_password'
        ]);
    }

    /**
     * Handle final password reset (Combined Flow).
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'security_answer' => 'required|string',
            'password' => 'required|confirmed|min:8',
        ]);
        
        $user = User::where('email', $request->email)->firstOrFail();
        
        $cacheKey = 'password_reset_attempts_' . str_replace(['@', '.'], '_', $user->email);
        $lockoutTime = \Illuminate\Support\Facades\Cache::get($cacheKey . '_lockout');
        
        if ($lockoutTime && now()->lt($lockoutTime)) {
            return back()->withErrors(['security_answer' => 'Akun terkunci. Silakan tunggu beberapa saat.'])->withInput($request->only('email'));
        }

        // Verify Answer First
        if (strtoupper(trim($request->security_answer)) !== strtoupper(trim($user->security_answer))) {
             $attempts = \Illuminate\Support\Facades\Cache::get($cacheKey, 0) + 1;
             \Illuminate\Support\Facades\Cache::put($cacheKey, $attempts, now()->addHour());
             
             if ($attempts >= 3) {
                 \Illuminate\Support\Facades\Cache::put($cacheKey . '_lockout', now()->addHour(), now()->addHour());
                 return back()->withErrors(['security_answer' => 'Terkunci: Jawaban salah 3 kali. Tunggu 1 jam.'])->withInput($request->only('email'));
             }
             return back()->withErrors(['security_answer' => "Jawaban salah. Sisa percobaan: " . (3 - $attempts)])->withInput($request->only('email'));
        }
        
        // Success: Clear attempts
        \Illuminate\Support\Facades\Cache::forget($cacheKey);
        \Illuminate\Support\Facades\Cache::forget($cacheKey . '_lockout');
        session()->forget('reset_password_email');
        
        // Update Password
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => \Illuminate\Support\Str::random(60),
        ])->save();
        
        // Security: Invalidate ALL active sessions for this user
        try {
            \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $user->id)->delete();
        } catch (\Exception $e) {
            // Ignore if sessions table doesn't exist or other DB error, not critical to flow
            \Illuminate\Support\Facades\Log::warning('Failed to flush sessions for user: ' . $user->id);
        }
        
        return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
    }
}
