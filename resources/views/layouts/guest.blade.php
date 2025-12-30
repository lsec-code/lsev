<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Cloud Host</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.png?v=1') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png?v=1') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.png?v=1') }}">

        <!-- Fonts -->
        <!-- Custom Style -->
        <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <script src="https://cdn.tailwindcss.com"></script>
        <script src="{{ asset('js/fingerprint.js') }}" defer></script>
        <script src="//unpkg.com/alpinejs" defer></script>

        <!-- CAPTCHA Scripts -->
        @php
            $captchaDriver = \App\Models\SiteSetting::where('setting_key', 'captcha_driver')->value('setting_value') ?? 'none';
        @endphp

        @if($captchaDriver === 'google')
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @elseif($captchaDriver === 'cloudflare')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endif


    </head>
    <body class="font-sans text-white antialiased bg-black">
        <!-- ADS: Auth Pages -->
        {!! \App\Models\SiteSetting::where('setting_key', 'ad_script_auth')->value('setting_value') !!}
        
        <div class="split-wrapper">
             <div class="split-left flex flex-col min-h-full">
                  <div class="flex-1 flex flex-col justify-center">
                      {{ $slot }}
                  </div>
                  
                  <!-- Guest Footer Copyright -->
                  <div class="mt-12 mb-6 text-center text-xs text-gray-400 flex flex-col gap-2">
                      <div>
                          &copy; {{ \App\Models\SiteSetting::where('setting_key', 'copyright_year')->value('setting_value') ?? '2025' }} 
                          <i class="fa-solid fa-cloud text-[#00ffff] ml-1"></i> 
                          <span class="text-[#00ffff] font-semibold">{{ \App\Models\SiteSetting::where('setting_key', 'copyright_text')->value('setting_value') ?? 'Cloud Host' }}</span>
                      </div>
                      <div class="space-x-3 text-[10px]">
                        <a href="{{ route('page.about') }}" class="hover:text-primary transition-colors">Tentang Kami</a>
                        <a href="{{ route('page.contact') }}" class="hover:text-primary transition-colors">Hubungi Kami</a>
                        <a href="{{ route('page.privacy') }}" class="hover:text-primary transition-colors">Kebijakan Privasi</a>
                        <a href="{{ route('page.terms') }}" class="hover:text-primary transition-colors">Ketentuan Layanan</a>
                    </div>
                  </div>
             </div>
        </div>
    </body>
</html>
