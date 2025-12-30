<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kustomisasi Tema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Theme Selection -->
            <div class="bg-[#1a1a1a] border border-[#333] rounded-2xl shadow-xl overflow-hidden shadow-purple-900/10" x-data="{ activeCategory: 'all' }">
                <div class="p-6 border-b border-[#333] bg-[#222] flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-white font-black uppercase tracking-widest text-sm flex items-center gap-2">
                            <i class="fa-solid fa-palette text-purple-500"></i> Theme Presets
                        </h3>
                        <p class="text-gray-500 text-[10px] mt-1 uppercase tracking-tighter">Pilih gaya visual untuk seluruh situs Anda</p>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        @foreach(['all', 'Glass', 'Material', 'Neon'] as $cat)
                            <button @click="activeCategory = '{{ strtolower($cat) }}'" 
                                    :class="activeCategory === '{{ strtolower($cat) }}' ? 'bg-purple-600 text-white' : 'bg-[#111] text-gray-500 border border-[#333] hover:border-purple-500/50'"
                                    class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">
                                {{ $cat }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php $activeTheme = $settings['active_theme'] ?? 'glass-dark'; @endphp
                        @foreach($themePresets as $id => $theme)
                            <div x-show="activeCategory === 'all' || activeCategory === '{{ strtolower($theme['category']) }}'" 
                                 class="group relative bg-[#0a0a0a] border {{ $activeTheme === $id ? 'border-purple-600 ring-1 ring-purple-600/50' : 'border-[#222]' }} rounded-2xl overflow-hidden hover:border-purple-500 transition-all duration-300 transform hover:-translate-y-1"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100">
                                
                                <!-- Preview Box -->
                                <div class="h-32 {{ $theme['preview_bg'] }} relative overflow-hidden p-4">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                                    <div class="relative z-10 space-y-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                            <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                        </div>
                                        <div class="h-1 w-2/3 bg-white/20 rounded"></div>
                                        <div class="h-1 w-1/2 bg-white/10 rounded"></div>
                                        <div class="mt-4 {{ $theme['preview_accent'] }} text-[10px] font-black uppercase tracking-widest">
                                            {{ $theme['name'] }} Preview
                                        </div>
                                    </div>

                                    @if($activeTheme === $id)
                                        <div class="absolute top-3 right-3 bg-purple-600 text-white rounded-full p-2 shadow-lg animate-pulse">
                                            <i class="fa-solid fa-check text-xs"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Info -->
                                <div class="p-5 space-y-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-white font-black uppercase tracking-widest text-xs">{{ $theme['name'] }}</h4>
                                            <span class="text-[9px] text-gray-500 uppercase font-bold">{{ $theme['category'] }} Style</span>
                                        </div>
                                    </div>
                                    
                                    <p class="text-gray-400 text-[10px] leading-relaxed line-clamp-2">
                                        {{ $theme['description'] }}
                                    </p>

                                    <form action="{{ route('admin.theme.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="active_theme" value="{{ $id }}">
                                        @foreach($theme['css_vars'] as $var => $value)
                                            <input type="hidden" name="css_vars[{{ $var }}]" value="{{ $value }}">
                                        @endforeach
                                        
                                        <button type="submit" 
                                            class="w-full {{ $activeTheme === $id ? 'bg-purple-600/10 text-purple-400 border border-purple-600/30' : 'bg-[#111] hover:bg-purple-600 hover:text-white text-gray-400 border border-[#222]' }} font-black py-3 rounded-xl uppercase tracking-widest text-[10px] transition-all flex items-center justify-center gap-2">
                                            @if($activeTheme === $id)
                                                <i class="fa-solid fa-circle-check"></i> AKTIF
                                            @else
                                                <i class="fa-solid fa-wand-magic-sparkles"></i> GUNAKAN TEMA
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Custom CSS -->
            <div class="bg-[#1a1a1a] border border-[#333] rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-[#333] bg-[#222]">
                    <h3 class="text-white font-black uppercase tracking-widest text-sm flex items-center gap-2">
                        <i class="fa-solid fa-code text-cyan-500"></i> Custom CSS
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.theme.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="bg-[#050505] rounded-xl border border-[#222] p-4 text-cyan-400 font-mono text-xs mb-2">
                            /* Tambahkan CSS kustom Anda di sini */
                        </div>
                        <textarea name="custom_css" rows="6" class="w-full bg-[#0a0a0a] border border-[#333] rounded-xl text-cyan-500 p-4 font-mono text-xs focus:outline-none focus:border-cyan-500 transition" placeholder=":root { --primary: #ff00ff; }">{{ $settings['custom_css'] ?? '' }}</textarea>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-cyan-600 hover:bg-cyan-500 text-white font-black py-3 px-8 rounded-xl uppercase tracking-widest text-xs transition transform active:scale-95">
                                Simpan CSS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="flex justify-between items-center px-4">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-white text-[10px] uppercase font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
                </a>
                <p class="text-gray-600 text-[9px] uppercase font-black tracking-widest">Theme Engine v2.0</p>
            </div>
        </div>
    </div>
</x-app-layout>
