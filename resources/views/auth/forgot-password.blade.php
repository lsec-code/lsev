<x-guest-layout>
    <div class="auth-box">
        <a href="{{ url('/') }}" class="auth-logo text-[#00ffff] text-2xl font-bold flex items-center justify-center gap-2 hover:opacity-80 transition-opacity">
            <i class="fa-solid fa-cloud"></i> Cloud Host
        </a>
        <h2 class="text-xl font-bold text-white mb-6">Lupa Password</h2>

        <div class="mb-4 text-sm text-gray-400 text-center">
            {{ __('Masukkan email akun Anda untuk memulai reset password.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.check') }}">
            @csrf

            <!-- Email Address -->
            <div class="input-group mb-6">
                <input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="Email Address">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-left" />
            </div>

            <!-- CAPTCHA Widget -->
            @php
                $captcha_driver = \App\Models\SiteSetting::where('setting_key', 'captcha_driver')->value('setting_value') ?? 'none';
            @endphp

            @if($captcha_driver !== 'none')
                <div class="flex justify-center mb-6">
                    @if($captcha_driver === 'google')
                        <div class="g-recaptcha" data-sitekey="{{ \App\Models\SiteSetting::where('setting_key', 'recaptcha_site_key')->value('setting_value') }}"></div>
                    @elseif($captcha_driver === 'cloudflare')
                        <div class="cf-turnstile" data-sitekey="{{ \App\Models\SiteSetting::where('setting_key', 'turnstile_site_key')->value('setting_value') }}" data-theme="dark"></div>
                    @endif
                </div>
                <x-input-error :messages="$errors->get('g-recaptcha-response') ?: $errors->get('cf-turnstile-response')" class="mt-2 text-center" />
            @endif

            <button type="submit" class="btn btn-primary w-full py-3 mb-4 text-black font-bold" style="background-color: #00d4d4; color: black; border-radius: 4px;">
                Lanjut
            </button>



            <div class="text-sm text-gray-400 mt-4">
                 <a href="{{ route('login') }}" class="text-gray-400 hover:text-white"><i class="fa-solid fa-arrow-left"></i> Kembali ke Login</a>
            </div>
        </form>
    </div>
</x-guest-layout>
