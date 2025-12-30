<x-guest-layout>
    <div class="auth-box">
        <a href="{{ url('/') }}" class="auth-logo text-[#00ffff] text-2xl font-bold flex items-center justify-center gap-2 hover:opacity-80 transition-opacity">
            <i class="fa-solid fa-cloud"></i> Cloud Host
        </a>
        <h2 class="text-xl font-bold text-white mb-6">{{ __('ui.login_title') }}</h2>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address / Username -->
            <div class="input-group mb-4">
                <input id="email" type="text" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="{{ __('ui.email_username') }}">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-left" />
            </div>

            <!-- Password -->
            <div class="input-group mb-6">
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="{{ __('ui.password') }}">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-left" />
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
                {{ __('ui.login') }}
            </button>

            <div class="mb-6">
                <a href="{{ route('password.request') }}" class="text-cyan-400 text-sm hover:underline">{{ __('ui.forgot_password') }}</a>
            </div>

            <div class="text-sm text-gray-400">
                {{ __('ui.dont_have_account') }} <a href="{{ route('register') }}" class="text-cyan-400 hover:underline">{{ __('ui.register_now') }}</a>
            </div>

            <!-- Trust / Info Section -->
            <div class="mt-8 pt-6 border-t border-gray-800 text-left">
                <h3 class="text-gray-300 font-bold mb-2">Infrastruktur Manajemen Konten Digital</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Kelola dan distribusikan aset video Anda melalui platform cloud yang aman. Cloud Host menyediakan infrastruktur hosting video dengan sistem pelacakan performa konten yang akurat dan transparan bagi para distributor konten.
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>

@if(session('success'))
    <div id="success-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm p-4 transition-opacity duration-300">
        <div class="bg-[#111] border border-[#00ffff]/30 rounded-xl shadow-[0_0_50px_rgba(0,255,255,0.2)] p-8 max-w-sm w-full text-center transform scale-100 transition-transform duration-300">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#00ffff]/10 rounded-full mb-6 relative">
                <div class="absolute inset-0 bg-[#00ffff]/20 rounded-full animate-ping"></div>
                <i class="fa-solid fa-check text-3xl text-[#00ffff]"></i>
            </div>
            
            <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-wider">Berhasil!</h3>
            <p class="text-gray-400 text-sm mb-8 leading-relaxed">
                {{ session('success') }}
            </p>
            
            <button onclick="closeSuccessModal()" class="w-full bg-[#00ffff]/10 border border-[#00ffff] text-[#00ffff] hover:bg-[#00ffff] hover:text-black font-black py-3 rounded-lg text-sm uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(0,255,255,0.1)] hover:shadow-[0_0_40px_rgba(0,255,255,0.4)]">
                Login Sekarang
            </button>
        </div>
    </div>

    <script>
        function closeSuccessModal() {
            const modal = document.getElementById('success-modal');
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.remove();
            }, 300);
        }
    </script>
@endif
