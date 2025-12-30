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

    <!-- Custom Style -->
    <link rel="stylesheet" href="{{ asset('assets/style.css?v=' . time()) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/fingerprint.js') }}" defer></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        [x-cloak] { display: none !important; }

        @php
            try {
                $settings = \App\Models\SiteSetting::all()->pluck('setting_value', 'setting_key');
                $customCss = $settings['custom_css'] ?? '';
            } catch (\Exception $e) {
                $customCss = '';
            }
        @endphp

        {!! $customCss !!}
    </style>
</head>
<body class="bg-black text-white font-sans antialiased" x-data="{ showPanduan: false }">
    @php
        $isMaintenance = \App\Models\SiteSetting::where('setting_key', 'maintenance_mode')->where('setting_value', 'true')->exists();
        $isAdmin = auth()->check() && (int)auth()->user()->is_admin === 1;
    @endphp

    @if($isMaintenance && $isAdmin)
        <div class="bg-red-600 text-white text-[10px] font-black uppercase tracking-[0.3em] py-2 text-center sticky top-0 z-[1000000] border-b border-white/20 shadow-lg">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i> MAINTENANCE MODE AKTIF (DISEMBUNYIKAN DARI USER)
        </div>
    @endif

    @if($isMaintenance && !$isAdmin)
        <!-- Maintenance Overlay -->
        <div class="fixed inset-0 z-[999999] bg-black/60 backdrop-blur-2xl flex items-center justify-center p-6 text-center overflow-hidden">
            <div class="relative max-w-md w-full animate-fade-in-up">
                <!-- Background Glow -->
                <div class="absolute inset-0 bg-[#00ffff]/5 blur-[100px] rounded-full"></div>
                
                <div class="relative space-y-8">
                    <div class="w-24 h-24 bg-gradient-to-tr from-[#00ffff]/20 to-blue-500/20 rounded-[2.5rem] border border-[#00ffff]/30 flex items-center justify-center mx-auto shadow-[0_0_40px_rgba(0,255,255,0.1)]">
                        <i class="fa-solid fa-gears text-5xl text-[#00ffff] animate-spin-slow"></i>
                    </div>
                    
                    <div class="space-y-3">
                        <h1 class="text-3xl font-black text-white uppercase tracking-tighter">Situs Sedang <span class="text-[#00ffff]">Maintenance</span></h1>
                        <p class="text-gray-400 font-medium leading-relaxed">
                            Mohon maaf atas ketidaknyamanannya. Saat ini kami sedang melakukan peningkatan sistem untuk memberikan layanan yang lebih baik.
                        </p>
                    </div>

                    <div class="pt-4 flex flex-col items-center gap-4">
                        <div class="px-6 py-2 rounded-full border border-white/5 bg-white/5 text-[10px] font-bold text-gray-500 uppercase tracking-[0.3em]">
                            Kami Akan Segera Kembali
                        </div>
                        <div class="flex gap-2">
                           <div class="w-1.5 h-1.5 rounded-full bg-[#00ffff] animate-pulse"></div>
                           <div class="w-1.5 h-1.5 rounded-full bg-[#00ffff] animate-pulse delay-75"></div>
                           <div class="w-1.5 h-1.5 rounded-full bg-[#00ffff] animate-pulse delay-150"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>body { overflow: hidden !important; }</style>
    @endif
    
    <!-- HEADER -->
    <header class="bg-[#0a0a0a] border-b border-[#222]">
        <div class="w-full px-4 md:px-6 h-14 md:h-16 flex items-center">
            @auth
                <!-- Logo (Left) -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 md:gap-3 shrink-0 hover:opacity-80 transition-opacity">
                    <i class="fa-solid fa-cloud text-[#00ffff] text-xl md:text-2xl"></i>
                    <span class="font-bold text-lg md:text-xl tracking-wide text-white">Cloud Host</span>
                </a>

                <!-- Right Actions (Pushed to Right with ml-auto) -->
                <div class="flex items-center gap-3 md:gap-6 ml-auto shrink-0">
                    <!-- Panduan Button -->
                    <button @click="showPanduan = true" class="flex items-center gap-2 border border-[#00ffff] text-[#00ffff] px-3 md:px-4 py-1.5 rounded-full text-[10px] md:text-xs font-bold hover:bg-[#00ffff] hover:text-black transition-all">
                        <i class="fa-solid fa-graduation-cap"></i> <span class="hidden md:inline">PANDUAN</span>
                    </button>
                    
                    <!-- Notification -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="relative cursor-pointer focus:outline-none group">
                            <i class="fa-regular fa-bell text-gray-300 text-lg group-hover:text-white transition-colors"></i>
                            @php $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count(); @endphp
                            @if($unreadCount > 0)
                                <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-[#00ffff] text-[9px] font-black text-black shadow-[0_0_10px_rgba(0,255,255,0.5)]">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="fixed inset-x-4 top-16 md:absolute md:inset-auto md:right-0 md:mt-3 md:w-80 bg-[#0a0a0a]/95 backdrop-blur-xl border border-[#222] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.5)] overflow-hidden z-[100]"
                             style="display: none;">
                            
                            <div class="p-4 border-b border-[#111] flex justify-between items-center bg-white/5">
                                <span class="text-[10px] font-black uppercase tracking-widest text-[#00ffff]">Notifikasi</span>
                                @if($unreadCount > 0)
                                    <form action="{{ route('notifications.mark_all_read') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[9px] font-bold text-gray-500 hover:text-white uppercase tracking-tighter">Tandai Dibaca</button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                @php 
                                    $notifications = \App\Models\Notification::where('user_id', Auth::id())
                                        ->latest()
                                        ->take(10)
                                        ->get();
                                @endphp

                                @forelse($notifications as $n)
                                    <a href="{{ route('notifications.read', $n->id) }}" class="block p-4 border-b border-[#111] hover:bg-white/5 transition-all @if($n->is_read) opacity-40 @endif">
                                        <div class="flex gap-3">
                                            <div class="shrink-0 mt-1">
                                                @if($n->type == 'withdrawal')
                                                    <div class="w-8 h-8 rounded-lg bg-yellow-500/10 border border-yellow-500/30 flex items-center justify-center text-yellow-500">
                                                        <i class="fa-solid fa-money-bill-transfer text-xs"></i>
                                                    </div>
                                                @elseif($n->type == 'security')
                                                    <div class="w-8 h-8 rounded-lg bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-500">
                                                        <i class="fa-solid fa-shield-halved text-xs"></i>
                                                    </div>
                                                @else
                                                    <div class="w-8 h-8 rounded-lg bg-[#00ffff]/10 border border-[#00ffff]/30 flex items-center justify-center text-[#00ffff]">
                                                        <i class="fa-solid fa-bell text-xs"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="space-y-1">
                                                <div class="text-[11px] font-black text-white leading-tight">{{ $n->title }}</div>
                                                <div class="text-[10px] text-gray-400 line-clamp-2">{{ $n->message }}</div>
                                                <div class="text-[9px] text-gray-600 font-bold uppercase tracking-tighter mt-1">{{ $n->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-8 text-center">
                                        <i class="fa-regular fa-bell text-2xl text-gray-800 mb-2"></i>
                                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest">Tidak ada notifikasi baru</p>
                                    </div>
                                @endforelse
                            </div>

                            <a href="#" class="block p-3 text-center text-[10px] font-black text-gray-500 hover:text-[#00ffff] border-t border-[#222] uppercase tracking-widest transition-colors">
                                Lihat Semua
                            </a>
                        </div>
                    </div>

                    <!-- Chat / Message Icon -->
                    <div class="relative cursor-pointer" x-data="{ unread: 0 }" x-on:chat-unread.window="unread = $event.detail" @click="$dispatch('toggle-chat')">
                        <i class="fa-regular fa-comments text-gray-300 text-lg hover:text-white transition-colors"></i>
                        <template x-if="unread > 0">
                            <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-bold text-white shadow-[0_0_5px_rgba(220,38,38,0.5)] animate-bounce" x-text="unread"></span>
                        </template>
                    </div>
                    
                    <!-- User Profile -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                            @php $navAvatarType = Auth::user()->getAvatarType(); @endphp
                            <div class="relative p-0.5 rounded-full 
                                {{ $navAvatarType === 'dev' ? 'bg-gradient-to-tr from-red-600 to-blue-600 animate-pulse' : '' }}
                                {{ $navAvatarType === 'gold' ? 'bg-gradient-to-tr from-[#FFC107] to-yellow-600' : '' }}
                                {{ $navAvatarType === 'silver' ? 'bg-gradient-to-tr from-gray-300 to-gray-600' : '' }}
                                {{ $navAvatarType === 'bronze' ? 'bg-gradient-to-tr from-orange-400 to-orange-800' : '' }}
                                {{ $navAvatarType === 'default' ? 'border border-gray-700' : '' }}
                            ">
                                <img src="{{ Auth::user()->getAvatarUrl() }}" class="w-8 h-8 rounded-full object-cover border-2 border-[#151515]">
                                
                                @if($navAvatarType === 'dev')
                                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-red-600 text-white font-black text-[6px] px-1.5 py-px rounded-full shadow-[0_0_5px_rgba(220,38,38,0.5)] tracking-widest border border-white/20 leading-none">
                                        DEV
                                    </div>
                                @elseif($navAvatarType === 'gold')
                                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-[#FFC107] text-black font-black text-[6px] px-1.5 py-px rounded-full shadow transition-all border border-white/20 leading-none">
                                        #1
                                    </div>
                                @elseif($navAvatarType === 'silver')
                                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-gray-400 text-black font-black text-[6px] px-1.5 py-px rounded-full shadow transition-all border border-white/20 leading-none">
                                        #2
                                    </div>
                                @elseif($navAvatarType === 'bronze')
                                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-orange-600 text-white font-black text-[6px] px-1.5 py-px rounded-full shadow transition-all border border-white/20 leading-none">
                                        #3
                                    </div>
                                @endif
                            </div>
                              <span class="hidden md:inline text-sm font-bold text-gray-200 group-hover:text-white transition-colors">{{ Auth::user()->name }}</span>
                             <i class="fa-solid fa-chevron-down text-[10px] text-gray-500 group-hover:text-white transition-colors"></i>
                        </button>

                        <!-- Dropdown -->
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-[#151515] border border-[#333] rounded-lg shadow-xl py-1 z-50" style="display: none;">
                            @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-cyan-500 font-bold hover:bg-[#222]">{{ __('ui.admin_panel') }}</a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-[#222] hover:text-[#00ffff]">{{ __('ui.profile') }}</a>
                            
                            <!-- Language Switcher -->
                            <div class="border-t border-[#333] my-1"></div>
                            <div class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Language</div>
                            <a href="{{ route('language.switch', 'id') }}" class="flex items-center justify-between px-4 py-2 text-sm {{ App::getLocale() == 'id' ? 'text-[#00ffff] bg-[#222]' : 'text-gray-300 hover:bg-[#222]' }}">
                                <span>Bahasa Indonesia</span>
                                @if(App::getLocale() == 'id') <i class="fa-solid fa-check text-[10px]"></i> @endif
                            </a>
                            <a href="{{ route('language.switch', 'en') }}" class="flex items-center justify-between px-4 py-2 text-sm {{ App::getLocale() == 'en' ? 'text-[#00ffff] bg-[#222]' : 'text-gray-300 hover:bg-[#222]' }}">
                                <span>English</span>
                                @if(App::getLocale() == 'en') <i class="fa-solid fa-check text-[10px]"></i> @endif
                            </a>

                            <div class="border-t border-[#333] my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-[#222] hover:text-red-300">{{ __('ui.logout') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- Guest View: Logo Left, Buttons Right -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 md:gap-3 shrink-0 hover:opacity-80 transition-opacity">
                    <i class="fa-solid fa-cloud text-[#00ffff] text-xl md:text-2xl"></i>
                    <span class="font-bold text-lg md:text-xl tracking-wide text-white">Cloud Host</span>
                </a>

                <div class="flex items-center gap-2 md:gap-4 ml-auto shrink-0">
                    <a href="{{ route('login') }}" class="px-3 md:px-4 py-1.5 border border-gray-700 text-gray-300 text-[10px] md:text-xs font-bold rounded-full hover:border-[#00ffff] hover:text-[#00ffff] transition-all uppercase">{{ __('ui.login') }}</a>
                    <a href="{{ route('register') }}" class="px-3 md:px-4 py-1.5 bg-[#00ffff] text-black text-[10px] md:text-xs font-bold rounded-full hover:bg-white transition-all uppercase shadow-[0_0_15px_rgba(0,255,255,0.3)]">{{ __('ui.register') }}</a>
                </div>
            @endauth
        </div>
    </header>

    <!-- NAVIGATION BAR -->
    @auth
    <nav class="hidden md:block bg-[#080808]/90 border-b border-white/5 sticky top-0 z-[40] backdrop-blur-2xl">
        <div class="w-full shadow-lg">
            <div class="flex items-center h-12 md:h-14 text-[9px] md:text-[10px] font-black uppercase tracking-wider md:tracking-[0.15em] w-full overflow-x-auto scrollbar-hide">
                
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex-1 flex items-center justify-center gap-2 h-full px-3 md:px-5 transition-all relative group shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    <i class="fa-solid fa-table-columns text-xs text-[#00ffff] group-hover:scale-110 transition-transform shadow-[0_0_10px_rgba(0,255,255,0.3)]"></i>
                    <span>{{ __('ui.dashboard') }}</span>
                    @if(request()->routeIs('dashboard'))
                        <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-[#00ffff] shadow-[0_0_15px_rgba(0,255,255,0.8)]"></div>
                    @endif
                </a>

                <div class="h-4 w-px bg-white/5 shrink-0"></div>

                <!-- My Videos -->
                <a href="{{ route('videos.index') }}" class="flex-1 flex items-center justify-center gap-2 h-full px-3 md:px-5 transition-all relative group shrink-0 {{ request()->routeIs('videos.*') && !request()->routeIs('videos.create') ? 'text-white' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    <i class="fa-regular fa-folder-open text-xs text-blue-500 group-hover:scale-110 transition-transform shadow-[0_0_10px_rgba(59,130,246,0.3)]"></i>
                    <span><span class="hidden sm:inline">Video </span>Saya</span>
                    @if(request()->routeIs('videos.*') && !request()->routeIs('videos.create'))
                        <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.8)]"></div>
                    @endif
                </a>

                <div class="h-4 w-px bg-white/5 shrink-0"></div>

                <!-- Remote Upload -->
                <a href="{{ route('remote_upload') }}" class="flex-1 flex items-center justify-center gap-2 h-full px-3 md:px-5 transition-all relative group shrink-0 {{ request()->routeIs('remote_upload') ? 'text-white' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    <i class="fa-solid fa-link text-xs text-orange-500 group-hover:scale-110 transition-transform shadow-[0_0_10px_rgba(249,115,22,0.3)]"></i>
                    <span>Remote<span class="hidden sm:inline"> Upload</span></span>
                    @if(request()->routeIs('remote_upload'))
                        <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-orange-500 shadow-[0_0_15px_rgba(249,115,22,0.8)]"></div>
                    @endif
                </a>

                <div class="h-4 w-px bg-white/5 shrink-0"></div>

                <!-- Upload Video -->
                <a href="{{ route('videos.create') }}" class="flex-1 flex items-center justify-center gap-2 h-full px-3 md:px-5 transition-all relative group shrink-0 {{ request()->routeIs('videos.create') ? 'text-white' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    <i class="fa-solid fa-cloud-arrow-up text-xs text-amber-500 group-hover:scale-110 transition-transform shadow-[0_0_10px_rgba(245,158,11,0.3)]"></i>
                    <span>Upload<span class="hidden sm:inline"> Video</span></span>
                    @if(request()->routeIs('videos.create'))
                        <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.8)]"></div>
                    @endif
                </a>

                <div class="h-4 w-px bg-white/5 shrink-0"></div>

                <!-- Leaderboard -->
                <a href="{{ route('leaderboard.index') }}" class="flex-1 flex items-center justify-center gap-2.5 h-full px-5 transition-all relative group shrink-0 {{ request()->routeIs('leaderboard.index') ? 'text-white' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    <i class="fa-solid fa-ranking-star text-xs text-purple-500 group-hover:scale-110 transition-transform shadow-[0_0_10px_rgba(168,85,247,0.3)]"></i>
                    <span>Leaderboard</span>
                    @if(request()->routeIs('leaderboard.index'))
                        <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-purple-500 shadow-[0_0_15px_rgba(168,85,247,0.8)]"></div>
                    @endif
                </a>

                <div class="h-4 w-px bg-white/5 shrink-0"></div>

                <!-- Withdraw -->
                <a href="{{ route('withdraw.index') }}" class="flex-1 flex items-center justify-center gap-2.5 h-full px-5 transition-all relative group shrink-0 {{ request()->routeIs('withdraw.*') ? 'text-white' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    <i class="fa-solid fa-wallet text-xs text-emerald-500 group-hover:scale-110 transition-transform shadow-[0_0_10px_rgba(16,185,129,0.3)]"></i>
                    <span>Withdraw</span>
                    @if(request()->routeIs('withdraw.*'))
                        <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.8)]"></div>
                    @endif
                </a>
            </div>
        </div>
    </nav>
    @endauth

    <!-- MAIN CONTENT -->
    <main class="min-h-screen bg-black py-8">
        {{ $slot }}
        @if(Request::is('admin*')) 
            <div class="h-20 md:hidden"></div> <!-- Spacer for mobile bottom nav -->
        @else
            <div class="h-20 md:hidden"></div> <!-- Spacer for mobile bottom nav -->
        @endif
    </main>

    @auth
        <!-- Mobile Bottom Navigation -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-[#0a0a0a]/95 backdrop-blur-2xl border-t border-white/10 z-[100] px-2 py-2 shadow-[0_-10px_40px_rgba(0,0,0,0.5)]">
            <div class="flex items-center justify-around h-14" x-data="{ showMoreMenu: false }">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-1 flex-1 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500 hover:text-white' }}">
                    <i class="fa-solid fa-house text-lg {{ request()->routeIs('dashboard') ? 'text-[#00ffff]' : 'text-gray-500' }}"></i>
                    <span class="text-[9px] font-bold uppercase tracking-tighter">Home</span>
                </a>

                <!-- Videos -->
                <a href="{{ route('videos.index') }}" class="flex flex-col items-center justify-center gap-1 flex-1 {{ request()->routeIs('videos.*') && !request()->routeIs('videos.create') ? 'text-white' : 'text-gray-500 hover:text-white' }}">
                    <i class="fa-regular fa-folder-open text-lg {{ request()->routeIs('videos.*') && !request()->routeIs('videos.create') ? 'text-blue-500' : 'text-gray-500' }}"></i>
                    <span class="text-[9px] font-bold uppercase tracking-tighter">Folder</span>
                </a>

                <!-- Upload (Center Highlight) -->
                <a href="{{ route('videos.create') }}" class="flex flex-col items-center justify-center -mt-8">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-[#00ffff] to-blue-600 flex items-center justify-center shadow-[0_0_20px_rgba(0,255,255,0.4)] border-4 border-[#0a0a0a]">
                        <i class="fa-solid fa-cloud-arrow-up text-black text-xl"></i>
                    </div>
                    <span class="text-[9px] font-bold text-[#00ffff] uppercase tracking-tighter mt-1">Upload</span>
                </a>

                <!-- Chat -->
                <button @click="$dispatch('toggle-chat')" class="flex flex-col items-center justify-center gap-1 flex-1 text-gray-500 hover:text-white relative">
                    <i class="fa-regular fa-comments text-lg"></i>
                    <span class="text-[9px] font-bold uppercase tracking-tighter">Chat</span>
                    <div x-data="{ unread: 0 }" x-on:chat-unread.window="unread = $event.detail">
                        <template x-if="unread > 0">
                            <span class="absolute top-0 right-1/4 flex h-3 w-3 items-center justify-center rounded-full bg-red-600 text-[8px] font-bold text-white" x-text="unread"></span>
                        </template>
                    </div>
                </button>

                <!-- More Menu Trigger -->
                <button @click="showMoreMenu = true" class="flex flex-col items-center justify-center gap-1 flex-1 text-gray-500 hover:text-white">
                    <i class="fa-solid fa-bars-staggered text-lg"></i>
                    <span class="text-[9px] font-bold uppercase tracking-tighter">Menu</span>
                </button>

                <!-- Slide-up Menu Drawer -->
                <div x-show="showMoreMenu" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="translate-y-full"
                     x-transition:enter-end="translate-y-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="translate-y-0"
                     x-transition:leave-end="translate-y-full"
                     class="fixed inset-0 z-[110]" style="display: none;">
                    
                    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showMoreMenu = false"></div>
                    
                    <div class="absolute bottom-0 left-0 right-0 bg-[#111] border-t border-white/10 rounded-t-[2.5rem] p-6 pb-12 shadow-[0_-20px_50px_rgba(0,0,0,0.8)]">
                        <div class="w-12 h-1 bg-white/10 rounded-full mx-auto mb-6"></div>
                        
                        <div class="grid grid-cols-3 gap-4">
                            <!-- Remote Upload -->
                            <a href="{{ route('remote_upload') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-[#00ffff]/10 hover:border-[#00ffff]/30 transition-all">
                                <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500">
                                    <i class="fa-solid fa-link text-lg"></i>
                                </div>
                                <span class="text-[10px] font-black text-white uppercase tracking-wider">Remote</span>
                            </a>

                            <!-- Withdrawals -->
                            <a href="{{ route('withdraw.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-[#00ffff]/10 hover:border-[#00ffff]/30 transition-all">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                                    <i class="fa-solid fa-wallet text-lg"></i>
                                </div>
                                <span class="text-[10px] font-black text-white uppercase tracking-wider">Tarik</span>
                            </a>

                            <!-- Leaderboard -->
                            <a href="{{ route('leaderboard.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-[#00ffff]/10 hover:border-[#00ffff]/30 transition-all">
                                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                                    <i class="fa-solid fa-trophy text-lg"></i>
                                </div>
                                <span class="text-[10px] font-black text-white uppercase tracking-wider">Top</span>
                            </a>

                            @if(Auth::user()->is_admin)
                            <!-- Admin Panel -->
                            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 hover:bg-cyan-500/20 transition-all">
                                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-500">
                                    <i class="fa-solid fa-user-shield text-lg"></i>
                                </div>
                                <span class="text-[10px] font-black text-cyan-500 uppercase tracking-wider">Admin</span>
                            </a>
                            @endif

                            <!-- Profile -->
                            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-[#00ffff]/10 hover:border-[#00ffff]/30 transition-all">
                                <div class="w-10 h-10 rounded-xl bg-gray-500/10 flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-user-gear text-lg"></i>
                                </div>
                                <span class="text-[10px] font-black text-white uppercase tracking-wider">Ganti Profil</span>
                            </a>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full flex flex-col items-center gap-2 p-4 rounded-2xl bg-red-500/5 border border-red-500/10 hover:bg-red-500/10 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                                        <i class="fa-solid fa-power-off text-lg"></i>
                                    </div>
                                    <span class="text-[10px] font-black text-red-500 uppercase tracking-wider">Keluar</span>
                                </button>
                            </form>
                        </div>

                        <button @click="showMoreMenu = false" class="w-full mt-8 py-3 bg-white/5 rounded-xl text-gray-500 font-bold text-xs uppercase tracking-[0.3em] hover:text-white transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    @endauth

    <!-- PANDUAN & ATURAN MODAL -->
    <div x-show="showPanduan" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-cloak>
        <div @click.away="showPanduan = false" class="bg-[#0a0a0a]/80 backdrop-blur-md border border-[#00ffff]/30 w-full max-w-2xl rounded-xl shadow-[0_0_50px_rgba(0,255,255,0.1)] overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="p-5 border-b border-[#222]">
                <h2 class="text-xl font-bold text-[#00ffff] flex items-center gap-3">
                    <i class="fa-solid fa-graduation-cap text-2xl"></i> Panduan & Aturan
                </h2>
            </div>
            
            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto text-sm text-gray-300 space-y-6 custom-scrollbar">
                
                <!-- Cara Mendapatkan Penghasilan -->
                <div>
                    <h3 class="text-white font-bold mb-2">Cara Mendapatkan Penghasilan</h3>
                    <p class="leading-relaxed text-gray-400">
                        Anda bisa menghasilkan uang dengan membagikan video yang Anda upload. 
                        Gunakan tautan yang disediakan untuk membagikan video ke media sosial, grup, atau forum.
                        Setiap tayangan valid dari penonton unik akan dihitung sebagai pendapatan Anda.
                    </p>
                </div>

                <!-- AdBlock Warning -->
                <div class="bg-yellow-900/10 border border-yellow-600/20 rounded p-4 text-yellow-500 border-l-4 border-l-yellow-500">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-shield-halved mt-0.5"></i>
                        <div>
                            <span class="font-bold block mb-1">Catatan AdBlock:</span>
                            Penghasilan valid dihitung ketika video ditonton tanpa menggunakan AdBlock. 
                            Jika penonton menggunakan AdBlock, maka iklan tidak akan dihitung sebagai tontonan valid.
                        </div>
                    </div>
                </div>

                <!-- PERINGATAN KERAS -->
                <div class="bg-red-900/10 border border-red-600/20 rounded p-5 text-red-400 border-l-4 border-l-red-600">
                    <div class="flex gap-3 mb-3">
                        <i class="fa-solid fa-triangle-exclamation text-lg mt-0.5"></i>
                        <h3 class="font-bold text-base text-red-500 tracking-wide uppercase">PERINGATAN KERAS</h3>
                    </div>
                    
                    <div class="space-y-3 pl-8">
                        <div>
                            <span class="font-bold text-white block mb-1">DILARANG KERAS MEMANIPULASI SISTEM!</span>
                            Seluruh aktivitas Anda dipantau oleh Log System kami secara real-time. Hal-hal berikut adalah <span class="font-bold text-white">PELANGGARAN BERAT</span>:
                        </div>
                        <ul class="list-disc pl-4 space-y-1 text-red-300/80">
                            <li>Menggunakan Bot, Script Auto-View, atau Traffic Generator.</li>
                            <li>Sengaja menonton video sendiri berulang kali (Self-View Abuse).</li>
                            <li>Menggunakan VPN/Proxy untuk memalsukan lokasi/identitas.</li>
                            <li>Mencoba meretas, "hijacking", atau mengakali sistem keamanan.</li>
                        </ul>

                        <div class="mt-4 pt-3 border-t border-red-500/20">
                            <span class="font-bold text-red-500 block mb-1">SANKSI TEGAS:</span>
                            <span class="font-bold">Jika terdeteksi melakukan pelanggaran, Administrator akan memblokir akun Anda (BANNED) secara permanen dan MENGHANGUSKAN seluruh pendapatan yang telah terkumpul.</span>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-gray-500 italic text-center pt-2">
                    * Sistem kami menjunjung tinggi kejujuran (Fair Play). Mari ciptakan komunitas yang sehat.
                </p>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-[#222] flex justify-end bg-[#0a0a0a]">
                <button @click="showPanduan = false" class="bg-[#00ffff] hover:bg-[#00e6e6] text-black font-bold py-2 px-6 rounded transition-colors text-sm">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>

    <!-- FOOTER COPYRIGHT -->
    <div class="py-6 border-t border-[#111] bg-[#050505] text-center text-xs text-gray-600 flex flex-col gap-2">
        <div>
            &copy; {{ \App\Models\SiteSetting::where('setting_key', 'copyright_year')->value('setting_value') ?? '2025' }} 
            <i class="fa-solid fa-cloud text-[#00ffff] ml-1"></i> 
            <span class="text-[#00ffff] font-semibold">{{ \App\Models\SiteSetting::where('setting_key', 'copyright_text')->value('setting_value') ?? 'Cloud Host' }}</span>
        </div>
        <div class="space-x-4">
            <a href="{{ route('page.about') }}" class="hover:text-white transition-colors">Tentang Kami</a>
            <a href="{{ route('page.contact') }}" class="hover:text-white transition-colors">Hubungi Kami</a>
            <a href="{{ route('page.privacy') }}" class="hover:text-white transition-colors">Kebijakan Privasi</a>
            <a href="{{ route('page.terms') }}" class="hover:text-white transition-colors">Ketentuan Layanan</a>
        </div>
    </div>

    <!-- Global Notification Toast System (Centered Popup) -->
    <div id="global-toast-container" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[10000] flex flex-col gap-3 pointer-events-none w-full max-w-md items-center"></div>

    <!-- Global Confirmation Modal (Premium) -->
    <div x-data="{ 
            show: false, 
            message: '', 
            callback: null,
            openModal(msg, cb) {
                this.message = msg;
                this.callback = cb;
                this.show = true;
            },
            confirm() {
                if (typeof this.callback === 'function') this.callback();
                this.show = false;
            }
         }"
         x-on:confirm-modal.window="openModal($event.detail.message, $event.detail.callback)"
         x-show="show"
         class="fixed inset-0 z-[10001] flex items-center justify-center p-4"
         x-cloak>
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="show = false"></div>
        <div class="relative bg-[#0a0a0a] border border-white/10 rounded-[2rem] p-8 max-w-[320px] w-full shadow-[0_20px_80px_rgba(0,0,0,0.8)]"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-10"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-10">
             
            <div class="flex flex-col items-center text-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-500 shadow-[0_0_20px_rgba(239,68,68,0.1)]">
                    <i class="fa-solid fa-circle-exclamation text-2xl"></i>
                </div>
                
                <div class="space-y-2">
                    <h3 class="text-white font-black uppercase tracking-widest text-sm">Konfirmasi</h3>
                    <p class="text-gray-400 text-xs leading-relaxed font-medium" x-text="message"></p>
                </div>

                <div class="grid grid-cols-2 gap-3 w-full">
                    <button @click="show = false" class="px-4 py-3 rounded-xl bg-white/5 border border-white/5 text-gray-500 font-bold text-[10px] uppercase tracking-widest hover:bg-white/10 transition-all">
                        Batal
                    </button>
                    <button @click="confirm()" class="px-4 py-3 rounded-xl bg-red-600 text-white font-bold text-[10px] uppercase tracking-widest hover:bg-red-500 shadow-[0_10px_20px_rgba(220,38,38,0.3)] transition-all">
                        Yakin
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Chat Drawer (Floating Widget) -->
    <div x-data="globalChat()" 
         x-on:toggle-chat.window="open = !open"
         x-show="open" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full md:translate-y-10 opacity-0 md:scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100 scale-100"
         x-transition:leave-end="translate-y-full md:translate-y-10 opacity-0 md:scale-95"
         class="fixed inset-0 md:inset-auto md:bottom-24 md:right-6 w-full h-full md:w-[340px] md:h-[550px] bg-[#050505]/95 backdrop-blur-3xl border-t md:border border-white/10 md:rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.8)] z-[9999] flex flex-col overflow-hidden"
         x-cloak>
        
            <!-- Header (Ultra Premium) -->
            <div class="p-3.5 border-b border-white/5 flex items-center justify-between bg-white/[0.03]">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                        <div class="absolute inset-0 rounded-full bg-green-500 animate-ping opacity-30"></div>
                    </div>
                    <div class="flex flex-col">
                        <h3 class="text-[#00ffff] font-black tracking-widest text-[10px] uppercase">GLOBAL CHAT</h3>
                        <span class="text-[7px] text-gray-500 font-black uppercase tracking-widest leading-none"><span x-text="onlineCount">0</span> USER AKTIF</span>
                    </div>
                </div>
                <button @click="open = false; localStorage.setItem('chat_drawer_open', 'false')" class="w-6 h-6 rounded-lg flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/5 transition-all cursor-pointer z-50">
                    <i class="fa-solid fa-xmark text-xs pointer-events-none"></i>
                </button>
            </div>
 
            <!-- Admin Notice when Disabled -->
            <div x-show="!isEnabled && isAdmin" class="px-4 pt-2 pb-0">
                <div class="bg-red-500/10 border border-red-500/20 p-2 rounded-lg text-center animate-pulse">
                    <p class="text-[10px] text-red-400 font-bold">
                        <i class="fa-solid fa-eye-slash mr-1"></i> Chat dimatikan user, Anda bypass.
                    </p>
                </div>
            </div>

            <!-- Messages -->
            <div x-ref="chatContainer" class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-4 scrollbar-hide bg-[#020202]/40">
                
                <!-- Disabled Overlay Removed -->

                <div class="space-y-4">
                    <template x-for="msg in messages" :key="msg.id">
                        <div class="flex flex-col animate-fade-in group w-full" :class="msg.is_mine ? 'items-end' : 'items-start'">
                            <!-- Header: Meta -->
                            <div class="flex items-center gap-2 mb-1.5 px-0.5" :class="msg.is_mine ? 'flex-row-reverse' : 'flex-row'">
                               <div class="flex items-center gap-1">
                                   <span class="text-[10px] font-black tracking-tight" :class="msg.is_admin ? 'text-red-500' : 'text-gray-200'" x-text="msg.username"></span>
                                   <template x-if="msg.is_admin">
                                       <i class="fa-solid fa-circle-check text-blue-500 text-[8px]"></i>
                                   </template>
                               </div>
                               <span class="text-[7px] text-gray-600 font-bold uppercase" x-text="msg.time"></span>
                            </div>

                            <div class="flex gap-2.5 max-w-[85%]" :class="msg.is_mine ? 'flex-row-reverse' : 'flex-row'">
                                <!-- Avatar with Rank Border -->
                                <div class="relative shrink-0 mt-0.5">
                                    <div class="w-8 h-8 rounded-full p-0.5 transition-all duration-500 relative" 
                                         :class="{
                                             'bg-gradient-to-tr from-red-600 to-blue-600 animate-pulse shadow-[0_0_10px_rgba(220,38,38,0.3)]': msg.avatar_type === 'dev',
                                             'bg-gradient-to-tr from-[#FFC107] to-yellow-600 shadow-[0_0_10px_rgba(255,193,7,0.3)]': msg.avatar_type === 'gold',
                                             'bg-gradient-to-tr from-gray-300 to-gray-600 shadow-[0_0_5px_rgba(156,163,175,0.1)]': msg.avatar_type === 'silver',
                                             'bg-gradient-to-tr from-orange-400 to-orange-800 shadow-[0_0_5px_rgba(154,52,18,0.1)]': msg.avatar_type === 'bronze',
                                             'border border-gray-800': msg.avatar_type === 'default'
                                         }">
                                        <img :src="msg.avatar" class="w-full h-full rounded-full object-cover border-2 border-[#000]">
                                        
                                        <template x-if="msg.avatar_type === 'dev'">
                                            <div class="absolute -bottom-0.5 left-1/2 -translate-x-1/2 bg-red-600 text-white font-black text-[5px] px-1 py-px rounded-full shadow tracking-tighter border border-white/10 leading-none">DEV</div>
                                        </template>
                                        <template x-if="msg.avatar_type === 'gold'">
                                            <div class="absolute -bottom-0.5 left-1/2 -translate-x-1/2 bg-[#FFC107] text-black font-black text-[5px] px-1 py-px rounded-full shadow border border-white/10 leading-none">#1</div>
                                        </template>
                                        <template x-if="msg.avatar_type === 'silver'">
                                            <div class="absolute -bottom-0.5 left-1/2 -translate-x-1/2 bg-gray-400 text-black font-black text-[5px] px-1 py-px rounded-full shadow border border-white/10 leading-none">#2</div>
                                        </template>
                                        <template x-if="msg.avatar_type === 'bronze'">
                                            <div class="absolute -bottom-0.5 left-1/2 -translate-x-1/2 bg-orange-700 text-white font-black text-[5px] px-1 py-px rounded-full shadow border border-white/10 leading-none">#3</div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Bubble -->
                                <div class="rounded-xl p-2.5 shadow-sm border transition-all text-[11px] leading-relaxed break-words"
                                     :class="msg.is_mine 
                                        ? 'bg-[#00ffff]/10 border-[#00ffff]/20 rounded-tr-none text-[#00ffff]' 
                                        : 'bg-white/[0.04] border-white/10 rounded-tl-none text-gray-300'">
                                    <p x-text="msg.message"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-2 bg-[#0a0a0a] border-t border-white/5 relative">
                <!-- Cooldown Overlay (Now more integrated) -->
                <div x-show="cooldown > 0" 
                     class="absolute inset-0 z-10 bg-black/60 backdrop-blur-[2px] flex items-center justify-center transition-all"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     x-cloak>
                    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600/20 border border-red-500/30 shadow-[0_0_20px_rgba(220,38,38,0.2)]">
                        <i class="fa-solid fa-hourglass-half text-red-500 text-xs animate-spin-slow"></i>
                        <span class="text-[11px] font-black text-red-500 uppercase tracking-widest leading-none">
                            TUNGGU <span x-text="Math.ceil(cooldown)">0</span>S
                        </span>
                    </div>
                </div>

                <!-- Disabled Warning Pill -->
                <div x-show="!isEnabled && !isAdmin" class="bg-red-500/10 border border-red-500/30 rounded-full py-2 px-4 flex items-center justify-center gap-2 animate-pulse">
                    <i class="fa-solid fa-eye-slash text-red-500 text-sm"></i>
                    <span class="text-red-500 font-bold text-xs uppercase tracking-widest">Chat Dimatikan</span>
                </div>

                <!-- Input Form (Only if Enabled or Admin) -->
                <form @submit.prevent="sendMessage" class="relative" x-show="isEnabled || isAdmin">
                    <div class="relative">
                        <textarea 
                            x-model="newMessage" 
                            rows="1"
                            maxlength="150"
                            @keydown.enter.prevent="sendMessage"
                            :disabled="cooldown > 0"
                            placeholder="Ketik pesan..." 
                            class="w-full bg-[#050505] border border-white/5 rounded-lg py-1.5 pl-3 pr-10 text-[11px] text-white placeholder-gray-700 focus:outline-none focus:border-[#00ffff]/20 transition-all resize-none disabled:opacity-30 disabled:cursor-not-allowed"
                        ></textarea>
                        
                        <div class="absolute right-1.5 bottom-1.5 flex items-center gap-1.5">
                             <div class="relative" x-data="{ show: false }">
                                <button type="button" @click="show = !show" class="text-gray-700 hover:text-[#00ffff] transition-colors">
                                    <i class="fa-regular fa-face-smile text-sm"></i>
                                </button>
                                <div x-show="show" @click.away="show = false" class="absolute bottom-full right-0 mb-2 bg-[#0a0a0a] border border-white/10 rounded-lg p-1.5 shadow-2xl z-50 grid grid-cols-6 gap-0.5 w-40">
                                    <template x-for="emoji in ['😊','😂','🔥','🚀','💎','💯','👍','🙌','❤️','✨','💰','🎮','👑','✅','❌','⚠️','👋','😎']">
                                        <button type="button" @click="newMessage += emoji; show = false" class="hover:bg-white/5 p-0.5 rounded transition-colors text-sm" x-text="emoji"></button>
                                    </template>
                                </div>
                             </div>
                             <button type="submit" :disabled="sending || !newMessage.trim() || cooldown > 0" class="w-6 h-6 rounded bg-[#00ffff]/5 border border-[#00ffff]/10 flex items-center justify-center text-[#00ffff] hover:bg-[#00ffff] hover:text-black transition-all disabled:opacity-20 text-[10px]">
                                <template x-if="!sending">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </template>
                                <template x-if="sending">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                                </template>
                            </button>
                        </div>
                    </div>

                    <!-- Char limit info -->
                    <div class="mt-1 flex justify-end px-0.5" x-show="cooldown <= 0">
                        <div class="text-[7px] font-bold text-gray-800">
                            <span x-text="newMessage.length">0</span>/150
                        </div>
                    </div>
                </form>
            </div>
    </div>

    <script>
        window.showToast = function(message, type = 'info') {
            const container = document.getElementById('global-toast-container');
            const toast = document.createElement('div');
            
            const colors = {
                'success': 'bg-black/90 backdrop-blur-xl border border-[#00ffff] text-[#00ffff] shadow-[0_0_30px_rgba(0,255,255,0.2)]',
                'error': 'bg-black/90 backdrop-blur-xl border border-red-600 text-red-500 shadow-[0_0_30px_rgba(220,38,38,0.2)]',
                'warning': 'bg-black/90 backdrop-blur-xl border border-[#FFC107] text-[#FFC107] shadow-[0_0_30px_rgba(255,193,7,0.2)]',
                'info': 'bg-black/90 backdrop-blur-xl border border-blue-500 text-blue-500 shadow-[0_0_30px_rgba(59,130,246,0.2)]'
            };
            
            // Popup Style: Larger, Centered, Scale Animation
            toast.className = `pointer-events-auto px-8 py-6 rounded-2xl font-bold tracking-wider text-sm transform scale-50 opacity-0 transition-all duration-300 ease-out cubic-bezier(0.175, 0.885, 0.32, 1.275) ${colors[type] || colors.info} flex flex-col items-center gap-3 min-w-[320px] max-w-md text-center justify-center`;
            
            // Add Icon based on type
            let icon = '';
            if(type === 'success') icon = '<div class="w-12 h-12 rounded-full border border-[#00ffff] flex items-center justify-center shadow-[0_0_15px_rgba(0,255,255,0.3)] mb-1"><i class="fa-solid fa-check text-xl"></i></div>';
            if(type === 'error') icon = '<div class="w-12 h-12 rounded-full border border-red-600 flex items-center justify-center shadow-[0_0_15px_rgba(220,38,38,0.3)] mb-1"><i class="fa-solid fa-xmark text-xl"></i></div>';
            if(type === 'warning') icon = '<div class="w-12 h-12 rounded-full border border-[#FFC107] flex items-center justify-center shadow-[0_0_15px_rgba(255,193,7,0.3)] mb-1"><i class="fa-solid fa-exclamation text-xl"></i></div>';
            if(type === 'info') icon = '<div class="w-12 h-12 rounded-full border border-blue-500 flex items-center justify-center shadow-[0_0_15px_rgba(59,130,246,0.3)] mb-1"><i class="fa-solid fa-info text-xl"></i></div>';

            toast.innerHTML = icon + '<div class="flex flex-col gap-1">' + message + '</div>';
            
            container.appendChild(toast);
            
            // Animate In (Pop Up)
            requestAnimationFrame(() => {
                toast.classList.remove('scale-50', 'opacity-0');
                toast.classList.add('scale-100', 'opacity-100');
            });
            
            // Remove after delay
            setTimeout(() => {
                toast.classList.remove('scale-100', 'opacity-100');
                toast.classList.add('scale-50', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000); // 3 seconds display
        };

        window.confirmAction = function(message, callback) {
            window.dispatchEvent(new CustomEvent('confirm-modal', { 
                detail: { message, callback } 
            }));
        };

        // Trigger Toast from Session
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                showToast({!! json_encode(session('success')) !!}, 'success');
            @endif
            
            @if(session('error'))
                showToast({!! json_encode(session('error')) !!}, 'error');
            @endif

            @if(session('warning'))
                showToast({!! json_encode(session('warning')) !!}, 'warning');
            @endif
            
            @if($errors->any())
                @foreach($errors->all() as $error)
                    showToast({!! json_encode($error) !!}, 'error');
                @endforeach
            @endif
        });

        function globalChat() {
            return {
                open: localStorage.getItem('chat_drawer_open') === 'true',
                messages: [],
                newMessage: '',
                sending: false,
                polling: null,
                unreadCount: 0,
                onlineCount: 0,
                cooldown: 0,
                isEnabled: true,
                isAdmin: false,
                cooldownInterval: null,
                lastSeenId: parseInt(localStorage.getItem('chat_last_seen_id') || 0),

                init() {
                    try {
                        // Start fetching immediately if open
                        if (this.open) {
                            this.fetchMessages();
                        }
                        
                        // Fallback: Ensure open is a boolean
                        this.open = !!this.open;

                    // Watch for open state changes
                    this.$watch('open', value => {
                        localStorage.setItem('chat_drawer_open', value);
                        if (value) {
                            this.fetchMessages();
                            if (this.messages.length > 0) {
                                this.markAsRead();
                            }
                        }
                    });

                    // Background polling (Reduced to 15s and added Visibility check)
                    this.polling = setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            this.fetchMessages();
                        }
                    }, 15000);

                    // Immediate fetch when switching back to tab
                    document.addEventListener('visibilitychange', () => {
                        if (document.visibilityState === 'visible') {
                            this.fetchMessages();
                        }
                    });
                } catch (e) {
                    console.error("Global Chat Init Error:", e);
                }
                },

                markAsRead() {
                    if (!this.messages.length) return;
                    const latest = this.messages[this.messages.length - 1];
                    if (latest) {
                        this.lastSeenId = latest.id;
                        localStorage.setItem('chat_last_seen_id', this.lastSeenId);
                        this.unreadCount = 0;
                        window.dispatchEvent(new CustomEvent('chat-unread', { detail: 0 }));
                    }
                },

                startCooldown(seconds) {
                    this.cooldown = seconds;
                    if (this.cooldownInterval) clearInterval(this.cooldownInterval);
                    this.cooldownInterval = setInterval(() => {
                        if (this.cooldown > 0) {
                            this.cooldown--;
                        } else {
                            clearInterval(this.cooldownInterval);
                        }
                    }, 1000);
                },

                async fetchMessages() {
                    try {
                        const response = await fetch('{{ route("chat.messages") }}');
                        const data = await response.json();
                        
                        const newMessages = data.messages;
                        this.onlineCount = data.online_count;
                        this.isEnabled = data.is_enabled;
                        this.isAdmin = data.is_admin;

                        // Check for new messages if drawer is closed
                        if (newMessages.length > 0) {
                            const latestId = newMessages[newMessages.length - 1].id;
                            
                            if (!this.open && latestId > this.lastSeenId) {
                                // Count how many are new
                                const newOnes = newMessages.filter(m => m.id > this.lastSeenId).length;
                                this.unreadCount = newOnes;
                                window.dispatchEvent(new CustomEvent('chat-unread', { detail: this.unreadCount }));
                            } else if (this.open) {
                                // Auto mark as read if open
                                this.lastSeenId = latestId;
                                localStorage.setItem('chat_last_seen_id', this.lastSeenId);
                                this.unreadCount = 0;
                                window.dispatchEvent(new CustomEvent('chat-unread', { detail: 0 }));
                            }
                        }

                        this.messages = newMessages;
                        
                        // Auto scroll to bottom if open or first load
                        if (this.open) {
                            this.$nextTick(() => {
                                this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                            });
                        }
                    } catch (e) {
                        console.error("Chat Error:", e);
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim() || this.sending || this.cooldown > 0) return;

                    this.sending = true;
                    try {
                        const response = await fetch('{{ route("chat.send") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ message: this.newMessage })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.newMessage = '';
                            await this.fetchMessages();
                            this.$nextTick(() => {
                                this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                            });
                        } else if (response.status === 429) {
                            this.startCooldown(data.cooldown || 60);
                        }
                    } catch (e) {
                        console.error("Send Error:", e);
                    } finally {
                        this.sending = false;
                    }
                }
            }
        }
    </script>

    <style>
        .bg-black { background-color: #050505; }
        body { background-color: #000; }
        /* Custom Scrollbar for Modal */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #111; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #444; }
        
        .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Hide scrollbar but keep functionality */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.1); }
    </style>
</body>
</html>
