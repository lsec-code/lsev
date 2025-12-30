<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-[#0a0a0a] border border-[#333] rounded-xl p-8 shadow-[0_0_50px_rgba(0,0,0,0.5)]">
            
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-[#222]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-[#00ffff]/10 flex items-center justify-center border border-[#00ffff]/20">
                        <i class="fa-solid fa-shield-cat text-[#00ffff] text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Pengaturan Captcha</h1>
                        <p class="text-gray-400 text-sm">Amankan website dari bot dengan Google ReCaptcha atau Cloudflare Turnstile.</p>
                    </div>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg p-4 flex items-center gap-3">
                    <i class="fa-solid fa-check-circle text-green-500"></i>
                    <p class="text-green-500 font-bold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.captcha.update') }}" class="space-y-8" x-data="{ driver: '{{ $active_driver }}' }">
                @csrf

                <!-- Driver Selection -->
                <div class="bg-[#111] border border-[#333] rounded-lg p-6">
                    <h3 class="text-white font-bold mb-4 uppercase tracking-wider text-xs">Pilih Layanan Captcha</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <!-- None -->
                        <label class="cursor-pointer">
                            <input type="radio" name="captcha_driver" value="none" class="peer sr-only" x-model="driver">
                            <div class="bg-[#0a0a0a] border border-[#333] peer-checked:border-gray-500 peer-checked:bg-gray-500/10 peer-checked:text-white rounded-lg p-5 text-center transition-all group h-full flex flex-col items-center justify-center">
                                <i class="fa-solid fa-ban text-2xl text-gray-600 mb-3 group-hover:text-gray-400 transition-colors"></i>
                                <div class="font-bold text-sm text-gray-500 group-hover:text-gray-300">Nonaktif</div>
                            </div>
                        </label>
                        
                        <!-- Google -->
                        <label class="cursor-pointer">
                            <input type="radio" name="captcha_driver" value="google" class="peer sr-only" x-model="driver">
                            <div class="bg-[#0a0a0a] border border-[#333] peer-checked:border-blue-500 peer-checked:bg-blue-500/10 peer-checked:text-blue-500 rounded-lg p-5 text-center transition-all group h-full flex flex-col items-center justify-center">
                                <i class="fa-brands fa-google text-2xl text-gray-600 mb-3 peer-checked:text-blue-500 transition-colors"></i>
                                <div class="font-bold text-sm text-gray-400 group-hover:text-white peer-checked:text-blue-500">Google ReCaptcha v2</div>
                            </div>
                        </label>
                        
                        <!-- Cloudflare -->
                        <label class="cursor-pointer">
                            <input type="radio" name="captcha_driver" value="cloudflare" class="peer sr-only" x-model="driver">
                            <div class="bg-[#0a0a0a] border border-[#333] peer-checked:border-orange-500 peer-checked:bg-orange-500/10 peer-checked:text-orange-500 rounded-lg p-5 text-center transition-all group h-full flex flex-col items-center justify-center">
                                <i class="fa-solid fa-cloud text-2xl text-gray-600 mb-3 peer-checked:text-orange-500 transition-colors"></i>
                                <div class="font-bold text-sm text-gray-400 group-hover:text-white peer-checked:text-orange-500">Cloudflare Turnstile</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Google Settings -->
                <div x-show="driver === 'google'" x-transition 
                     class="bg-[#111] border border-blue-900/30 rounded-lg p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                        <i class="fa-brands fa-google text-9xl"></i>
                    </div>
                    
                    <h3 class="text-blue-400 font-bold mb-6 flex items-center gap-2 relative z-10">
                        <i class="fa-brands fa-google"></i> Konfigurasi Google ReCaptcha
                    </h3>

                    <div class="space-y-4 relative z-10">
                        <div>
                            <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Site Key</label>
                            <input type="text" name="recaptcha_site_key" value="{{ $recaptcha_site }}" 
                                class="w-full bg-[#0a0a0a] border border-[#333] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 focus:shadow-[0_0_15px_rgba(59,130,246,0.2)] transition-all font-mono text-sm placeholder-gray-700"
                                placeholder="6Lcx...">
                        </div>
                        <div>
                            <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Secret Key</label>
                            <input type="text" name="recaptcha_secret_key" value="{{ $recaptcha_secret }}" 
                                class="w-full bg-[#0a0a0a] border border-[#333] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 focus:shadow-[0_0_15px_rgba(59,130,246,0.2)] transition-all font-mono text-sm placeholder-gray-700"
                                placeholder="6Lcx...">
                        </div>
                    </div>
                </div>

                <!-- Cloudflare Settings -->
                <div x-show="driver === 'cloudflare'" x-transition 
                     class="bg-[#111] border border-orange-900/30 rounded-lg p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                        <i class="fa-solid fa-cloud text-9xl"></i>
                    </div>

                    <h3 class="text-orange-400 font-bold mb-6 flex items-center gap-2 relative z-10">
                        <i class="fa-solid fa-cloud"></i> Konfigurasi Cloudflare Turnstile
                    </h3>

                    <div class="space-y-4 relative z-10">
                        <div>
                            <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Site Key</label>
                            <input type="text" name="turnstile_site_key" value="{{ $turnstile_site }}" 
                                class="w-full bg-[#0a0a0a] border border-[#333] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-orange-500 focus:shadow-[0_0_15px_rgba(249,115,22,0.2)] transition-all font-mono text-sm placeholder-gray-700"
                                placeholder="0x4AAAA...">
                        </div>
                        <div>
                            <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Secret Key</label>
                            <input type="text" name="turnstile_secret_key" value="{{ $turnstile_secret }}" 
                                class="w-full bg-[#0a0a0a] border border-[#333] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-orange-500 focus:shadow-[0_0_15px_rgba(249,115,22,0.2)] transition-all font-mono text-sm placeholder-gray-700"
                                placeholder="0x4AAAA...">
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-[#222] flex items-center justify-end">
                    <button type="submit" class="bg-[#00ffff] hover:bg-[#00e6e6] text-black font-bold py-3 px-8 rounded-lg shadow-[0_0_20px_rgba(0,255,255,0.3)] hover:shadow-[0_0_30px_rgba(0,255,255,0.5)] transition-all transform hover:-translate-y-1">
                        <i class="fa-solid fa-save mr-2"></i> Simpan Pengaturan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
</x-app-layout>
