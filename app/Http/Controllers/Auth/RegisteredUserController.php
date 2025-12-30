<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

use App\Traits\VerifiesCaptcha;

class RegisteredUserController extends Controller
{
    use VerifiesCaptcha;
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => [
                'required', 
                'string', 
                'min:5', 
                'max:12', 
                'alpha_num', 
                'unique:'.User::class, 
                'not_in:admin,moderator,staff,administrator,root,support,cloud,host,cloudhost,server,sysadmin'
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'security_answer' => ['required', 'string', 'max:255', 'confirmed'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

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

        $code = rand(100000, 999999);
        
        $user = User::create([
            'name' => $request->username, // Use username as name
            'username' => $request->username,
            'email' => $request->email,
            'security_question' => 'Kode Keamanan Rahasia', // Default question
            'security_answer' => $request->security_answer,
            'password' => Hash::make($request->password),
            'balance' => 0, // Default 0
            // 'verification_code' => $code, // Removed: Using Security Answer for verification
            'email_verified_at' => null, // Ensure explicitly null (Used as 'Account Active' flag)
        ]);

        // DISABLE AUTO LOGIN
        // Auth::login($user);

        // Redirect to Login with Success Message
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login dan aktifkan akun Anda menggunakan Kode Keamanan.');
    }
}
