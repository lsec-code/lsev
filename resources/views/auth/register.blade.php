<x-guest-layout>
    <div class="auth-box">
        <a href="{{ url('/') }}" class="auth-logo text-[#00ffff] text-2xl font-bold flex items-center justify-center gap-2 hover:opacity-80 transition-opacity">
            <i class="fa-solid fa-cloud"></i> Cloud Host
        </a>
        <h2 class="text-xl font-bold text-white mb-6">{{ __('ui.register_title') }}</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name Input Removed -->

            <!-- Username -->
            <div class="input-group mb-4">
                <input id="username" type="text" name="username" :value="old('username')" required autocomplete="username" placeholder="Username (5-12 Huruf/Angka, Tanpa Spasi)">
                <x-input-error :messages="$errors->get('username')" class="mt-2 text-left" />
            </div>

            <!-- Email Address -->
            <div class="input-group mb-4">
                <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Email Address (Valid)">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-left" />
            </div>

            <!-- Password with Strength Meter -->
            <div class="input-group mb-4" x-data="{ 
                password: '', 
                strength: 0,
                message: '',
                color: 'bg-gray-700',
                checkStrength(val) {
                    this.password = val;
                    let strength = 0;
                    if (val.length >= 6) strength++; // Min length
                    if (val.match(/[A-Z]/)) strength++; // Uppercase
                    if (val.match(/[0-9]/)) strength++; // Number
                    if (val.match(/[^A-Za-z0-9]/)) strength++; // Special char
                    
                    this.strength = strength;
                    if (val.length === 0) { this.message = ''; this.color = 'bg-gray-700'; }
                    else if (val.length < 6) { this.message = 'Too Short (Min 6)'; this.color = 'bg-red-600'; }
                    else if (strength <= 1) { this.message = 'Weak (Gunakan Huruf Besar & Angka)'; this.color = 'bg-red-500'; }
                    else if (strength === 2) { this.message = 'Normal'; this.color = 'bg-yellow-500'; }
                    else { this.message = 'Strong'; this.color = 'bg-green-500'; }
                }
            }">
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="{{ __('ui.password') }} (Min. 6 Karakter, Huruf Besar & Angka)" 
                       @input="checkStrength($event.target.value)">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-left" />

                <!-- Strength Indicator Bar -->
                <div class="mt-2 h-1 w-full bg-gray-800 rounded overflow-hidden" x-show="password.length > 0">
                    <div class="h-full transition-all duration-300" :class="color" :style="'width: ' + (strength * 25) + '%'"></div>
                </div>
                <!-- Strength Text -->
                <p class="text-xs mt-1 text-right font-bold" :class="{
                    'text-red-500': message.includes('Weak') || message.includes('Short'),
                    'text-yellow-500': message === 'Normal',
                    'text-green-500': message === 'Strong'
                }" x-text="message"></p>
            </div>

            <!-- Confirm Password -->
            <div class="input-group mb-4">
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi Password">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-left" />
            </div>

            <!-- Security Question -->
            <!-- Security Question Warning -->
            <div id="warning-box" class="mb-4 p-4 rounded bg-red-900/10 border border-red-500/50 text-red-400 text-xs leading-relaxed">
                <i class="fa-solid fa-triangle-exclamation mr-1 text-base relative top-0.5 text-red-500"></i>
                <strong class="font-bold text-red-500">PENTING:</strong> Pertanyaan Kode Keamanan ini akan digunakan untuk verifikasi perubahan password dan reset password. Pastikan Anda mengingatnya! <strong class="font-bold text-red-500">Bahwa kode keamanan ini tidak dapat di ubah kembali.</strong>
            </div>

            <!-- Security Answer (Kode Keamanan) -->
            <div class="input-group mb-4">
                <input id="security_answer" type="text" name="security_answer" :value="old('security_answer')" required placeholder="Buat Kode Keamanan (Contoh: nama hewan / angka rahasia)">
                <x-input-error :messages="$errors->get('security_answer')" class="mt-2 text-left" />
            </div>

            <!-- Security Answer Confirmation -->
            <div class="input-group mb-6">
                <input id="security_answer_confirmation" type="text" name="security_answer_confirmation" required placeholder="Konfirmasi Kode Keamanan">
                <x-input-error :messages="$errors->get('security_answer_confirmation')" class="mt-2 text-left" />
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

            <button type="submit" class="w-full bg-[#00ffff]/10 border border-[#00ffff] text-[#00ffff] hover:bg-[#00ffff] hover:text-black font-black py-3 rounded-lg text-sm uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(0,255,255,0.1)] hover:shadow-[0_0_30px_rgba(0,255,255,0.4)] mb-4">
                {{ __('ui.register') }}
            </button>

            <div class="text-sm text-gray-400">
                {{ __('ui.already_have_account') }} <a href="{{ route('login') }}" class="text-cyan-400 hover:underline">{{ __('ui.login_here') }}</a>
            </div>
        </form>
    </div>
</x-guest-layout>
