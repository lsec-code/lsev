<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Site Settings') }}
        </h2>
    </x-slot>

    <div class="py-6 md:py-12">
        <div class="max-w-4xl mx-auto px-4 md:px-6 lg:px-8">
            <div class="bg-bg-card border border-border-color rounded-xl shadow-xl overflow-hidden font-sans">
                <div class="p-5 md:p-6 border-b border-border-color bg-bg-dark flex items-center justify-between">
                    <h3 class="text-white font-bold flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-primary"></i> {{ __('Site Settings') }}
                    </h3>
                    <div class="flex p-1 bg-black/40 rounded-xl border border-white/5 gap-1" x-data="{ activeSect: 'general' }">
                        <button @click="activeSect = 'general'; $dispatch('sect-change', 'general')" 
                            :class="activeSect === 'general' ? 'bg-[#00ffff] text-black shadow-[0_0_15px_rgba(0,255,255,0.3)]' : 'text-gray-500 hover:text-white hover:bg-white/5'"
                            class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-gear"></i> General
                        </button>
                        <button @click="activeSect = 'branding'; $dispatch('sect-change', 'branding')" 
                            :class="activeSect === 'branding' ? 'bg-[#00ffff] text-black shadow-[0_0_15px_rgba(0,255,255,0.3)]' : 'text-gray-500 hover:text-white hover:bg-white/5'"
                            class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-palette"></i> Branding
                        </button>
                    </div>
                </div>
                
                <div x-data="{ currentSect: 'general' }" @sect-change.window="currentSect = $event.detail">
                    
                    <form action="{{ route('admin.settings.update') }}" method="POST" class="p-5 md:p-6 space-y-6 md:space-y-8" x-show="currentSect === 'general'">
                        @csrf
                        <!-- General Settings Content (Existing) -->
                        <div class="space-y-4">
                            <h4 class="text-white font-bold text-sm uppercase tracking-wider border-b border-border-color pb-2 text-primary">
                                 General Settings
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                 <div class="space-y-2">
                                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider">Minimum Withdraw (IDR)</label>
                                    <input type="number" name="min_withdrawal" value="{{ $settings['min_withdrawal'] ?? $settings['min_withdraw'] ?? 250000 }}" class="w-full bg-bg-dark border border-border-color rounded-lg text-white p-3 focus:outline-none focus:border-primary transition">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider">Admin Security Code</label>
                                    <input type="text" name="admin_security_code" value="{{ $settings['admin_security_code'] ?? 'admin123' }}" class="w-full bg-bg-dark border border-border-color rounded-lg text-white p-3 focus:outline-none focus:border-red-500 transition font-mono tracking-widest">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider">Max Upload Video (MB)</label>
                                    <input type="number" name="max_upload_size" value="{{ $settings['max_upload_size'] ?? 500 }}" class="w-full bg-bg-dark border border-border-color rounded-lg text-white p-3 focus:outline-none focus:border-primary transition">
                                </div>
                            </div>
                        </div>

                        <!-- Feature Management Content -->
                        <div class="space-y-4">
                            <h4 class="text-white font-bold text-sm uppercase tracking-wider border-b border-border-color pb-2 text-purple-500">
                                 Feature Management
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-1 md:col-span-1 flex items-center justify-between bg-bg-dark p-4 rounded-lg border border-border-color">
                                    <div><h4 class="text-white font-bold text-sm">Global Chat</h4><p class="text-gray-500 text-xs">Aktifkan fitur chat global.</p></div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="global_chat_enabled" value="false">
                                        <input type="checkbox" name="global_chat_enabled" value="true" class="sr-only peer" {{ ($settings['global_chat_enabled'] ?? 'true') !== 'false' ? 'checked' : '' }} @change="autoSave('global_chat_enabled', $event.target.checked ? 'true' : 'false')">
                                        <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:bg-purple-600 after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                                    </label>
                                </div>
                                <div class="col-span-1 md:col-span-1 flex items-center justify-between bg-bg-dark p-4 rounded-lg border border-red-500/30">
                                    <div><h4 class="text-white font-bold text-sm text-red-500">Maintenance Mode</h4><p class="text-gray-500 text-xs">Kunci situs untuk perbaikan.</p></div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="maintenance_mode" value="false">
                                        <input type="checkbox" name="maintenance_mode" value="true" class="sr-only peer" {{ ($settings['maintenance_mode'] ?? 'false') === 'true' ? 'checked' : '' }} @change="autoSave('maintenance_mode', $event.target.checked ? 'true' : 'false')">
                                        <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:bg-red-600 after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Legal Pages -->
                        <div class="space-y-4">
                            <h4 class="text-white font-bold text-sm uppercase tracking-wider border-b border-border-color pb-2 text-green-500">
                                 Footer & Legal Pages
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                 <div class="space-y-2">
                                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider">Copyright Text</label>
                                    <input type="text" name="copyright_text" value="{{ $settings['copyright_text'] ?? 'Cloud Host' }}" class="w-full bg-bg-dark border border-border-color rounded-lg text-white p-3 focus:outline-none focus:border-green-500 transition">
                                </div>
                                 <div class="space-y-2">
                                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider">Copyright Year</label>
                                    <input type="text" name="copyright_year" value="{{ $settings['copyright_year'] ?? date('Y') }}" class="w-full bg-bg-dark border border-border-color rounded-lg text-white p-3 focus:outline-none focus:border-green-500 transition">
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-border-color flex justify-end">
                            <script>
                                function autoSave(key, value) {
                                    fetch("{{ route('admin.settings.toggle') }}", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                        },
                                        body: JSON.stringify({ key, value })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if(data.success) {
                                            window.showToast('Pengaturan Otomatis Tersimpan!', 'success');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        window.showToast('Gagal menyimpan otomatis', 'error');
                                    });
                                }
                            </script>
                             <button type="submit" class="bg-[#00ffff]/10 hover:bg-[#00ffff] border border-[#00ffff] text-[#00ffff] hover:text-black font-black uppercase tracking-widest text-[10px] py-4 px-10 rounded-xl transition transform active:scale-95 shadow-[0_0_20px_rgba(0,255,255,0.1)] hover:shadow-[0_0_30px_rgba(0,255,255,0.4)]">
                                 <i class="fa-solid fa-save mr-2"></i> Save All Settings
                             </button>
                        </div>
                    </form>

                    <!-- Branding Settings Section (Logo/Favicon) -->
                    <div x-show="currentSect === 'branding'" class="p-5 md:p-6 space-y-8 animate-fade-in">
                        <div class="space-y-4">
                            <h4 class="text-white font-bold text-sm uppercase tracking-wider border-b border-border-color pb-2 text-blue-500">
                                 Site Branding
                            </h4>
                            <p class="text-gray-500 text-xs">Kelola logo dan ikon browser Anda di sini.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-[#0a0a0a] border border-[#333] rounded-xl p-6 space-y-4">
                                <h3 class="text-white font-bold text-xs flex items-center gap-2">
                                    <i class="fa-solid fa-image text-blue-500"></i> Logo Situs
                                </h3>
                                <form action="{{ route('admin.theme.logo') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="type" value="logo">
                                    <div class="bg-[#111] border border-[#333] rounded-lg p-6 text-center relative group min-h-[120px] flex items-center justify-center">
                                        @if(isset($settings['logo_url']))
                                            <img src="{{ asset('storage/' . $settings['logo_url']) }}" alt="Logo" class="max-h-16 mx-auto transition-all group-hover:scale-105">
                                        @else
                                            <div class="text-gray-600 italic text-xs">Belum ada logo</div>
                                        @endif
                                        <input type="file" name="logo" class="absolute inset-0 opacity-0 cursor-pointer" onchange="this.form.submit()">
                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none bg-black/40">
                                            <div class="bg-blue-600 text-white px-3 py-1 rounded-full text-[10px] font-bold">GANTI LOGO</div>
                                        </div>
                                    </div>
                                    <p class="text-[9px] text-gray-600 text-center">Format: PNG/JPG. Maks: 2MB.</p>
                                </form>
                            </div>

                            <div class="bg-[#0a0a0a] border border-[#333] rounded-xl p-6 space-y-4">
                                <h3 class="text-white font-bold text-xs flex items-center gap-2">
                                    <i class="fa-solid fa-star text-yellow-500"></i> Favicon
                                </h3>
                                <form action="{{ route('admin.theme.logo') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="type" value="favicon">
                                    <div class="bg-[#111] border border-[#333] rounded-lg p-6 text-center relative group min-h-[120px] flex items-center justify-center">
                                        @if(isset($settings['favicon_url']))
                                            <img src="{{ asset('storage/' . $settings['favicon_url']) }}" alt="Fav" class="w-12 h-12 mx-auto transition-all group-hover:scale-105">
                                        @else
                                            <div class="text-gray-600 italic text-xs">Belum ada favicon</div>
                                        @endif
                                        <input type="file" name="logo" class="absolute inset-0 opacity-0 cursor-pointer" onchange="this.form.submit()">
                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none bg-black/40">
                                            <div class="bg-yellow-600 text-white px-3 py-1 rounded-full text-[10px] font-bold">GANTI ICON</div>
                                        </div>
                                    </div>
                                    <p class="text-[9px] text-gray-600 text-center">Ikon yang muncul di tab browser.</p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="mt-4 flex justify-between px-2">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-white text-xs transition">
                    ← Back to Dashboard
                </a>
                <span class="text-gray-600 text-[10px]">Cloud Host v2.0 Management</span>
            </div>
        </div>
    </div>
</x-app-layout>
