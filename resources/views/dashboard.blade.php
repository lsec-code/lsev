<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 md:px-6 space-y-6 md:space-y-8">
        
        
        <!-- ANNOUNCEMENT BOX (Dynamic from Database) -->
        @php
            $announcement = Cache::remember('active_announcement', 60, function() {
                return \App\Models\Announcement::where('active', true)->first();
            });
        @endphp
        
        @if($announcement)
        <div class="mb-6 border backdrop-blur-sm rounded-xl overflow-hidden h-12 relative shadow-lg"
            style="border-color: {{ $announcement->color === 'rainbow' ? 'var(--primary)' : $announcement->color }}33; background: {{ $announcement->color === 'rainbow' ? 'var(--primary)' : $announcement->color }}0D;">
             <div class="w-full h-full flex items-center px-4 overflow-hidden">
                <div class="mr-4 flex-shrink-0 flex items-center border-r pr-4 h-full"
                    style="border-color: {{ $announcement->color === 'rainbow' ? 'var(--primary)' : $announcement->color }}33;">
                    <i class="fa-solid fa-bullhorn animate-pulse" 
                        style="color: {{ $announcement->color === 'rainbow' ? 'var(--primary)' : $announcement->color }};"></i>
                </div>
                <div class="flex-1 overflow-hidden">
                    @if($announcement->color === 'rainbow')
                        <marquee class="rainbow-text font-black text-xs tracking-widest uppercase" scrollamount="6">
                            {{ $announcement->content }}
                        </marquee>
                    @else
                        <marquee class="font-black text-xs tracking-widest uppercase" scrollamount="6"
                            style="color: {{ $announcement->color }}; text-shadow: 0 0 10px {{ $announcement->color }}, 0 0 20px {{ $announcement->color }};">
                            {{ $announcement->content }}
                        </marquee>
                    @endif
                </div>
            </div>
        </div>

        <style>
            .rainbow-text {
                animation: rainbow-colors 3s linear infinite;
            }

            @keyframes rainbow-colors {
                0% { color: #ff0000; text-shadow: 0 0 10px #ff0000, 0 0 20px #ff0000; }
                14% { color: #ff7f00; text-shadow: 0 0 10px #ff7f00, 0 0 20px #ff7f00; }
                28% { color: #ffff00; text-shadow: 0 0 10px #ffff00, 0 0 20px #ffff00; }
                42% { color: #00ff00; text-shadow: 0 0 10px #00ff00, 0 0 20px #00ff00; }
                57% { color: #0000ff; text-shadow: 0 0 10px #0000ff, 0 0 20px #0000ff; }
                71% { color: #4b0082; text-shadow: 0 0 10px #4b0082, 0 0 20px #4b0082; }
                85% { color: #9400d3; text-shadow: 0 0 10px #9400d3, 0 0 20px #9400d3; }
                100% { color: #ff0000; text-shadow: 0 0 10px #ff0000, 0 0 20px #ff0000; }
            }
        </style>
        @endif



        <!-- STATS GRID - ROW 1 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Total Video -->
            <div class="bg-[#111]/40 backdrop-blur-sm border border-white/5 rounded-xl p-5 hover:border-[#00ffff]/50 hover:bg-[#111] transition-all duration-300 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-[#00ffff]/10 flex items-center justify-center border border-[#00ffff]/20 group-hover:shadow-[0_0_15px_rgba(0,255,255,0.2)] transition-all">
                        <i class="fa-solid fa-video text-[#00ffff]"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase text-gray-600 bg-[#111] px-2 py-1 rounded border border-gray-800">Video</span>
                </div>
                <div>
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Total Upload</div>
                    <div class="text-2xl font-black text-white tracking-tight">{{ number_format($user_stats['video_count'] ?? 0) }}</div>
                </div>
            </div>


            <!-- Penonton Aktif -->
            <div x-data="{ 
                viewerCount: {{ $user_stats['online_count'] ?? 0 }},
                async fetchViewers() {
                    try {
                        const response = await fetch('/dashboard/active-viewers');
                        const data = await response.json();
                        this.viewerCount = data.count;
                    } catch(e) {
                        console.log('Fetch error:', e);
                    }
                }
            }" 
            x-init="setInterval(() => fetchViewers(), 10000)"
            class="bg-[#111]/60 backdrop-blur-sm border border-[#222] rounded-xl p-5 hover:border-[#00ffff]/50 hover:bg-bg-card transition-all duration-300 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-[#00ffff]/10 flex items-center justify-center border border-[#00ffff]/20 group-hover:shadow-[0_0_15px_rgba(0,255,255,0.2)] transition-all">
                        <i class="fa-solid fa-users text-[#00ffff]"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase text-green-500 bg-green-500/10 px-2 py-1 rounded border border-green-500 animate-pulse shadow-[0_0_10px_rgba(34,197,94,0.5)]">Live</span>
                </div>
                <div>
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Penonton Aktif</div>
                    <div class="text-2xl font-black text-white tracking-tight transition-all" x-text="viewerCount.toLocaleString()"></div>
                </div>
            </div>

            <!-- Total Tayangan -->
            <div class="bg-[#111]/40 backdrop-blur-sm border border-white/5 rounded-xl p-5 hover:border-blue-500/50 hover:bg-[#111] transition-all duration-300 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center border border-blue-500/20 group-hover:shadow-[0_0_15px_rgba(59,130,246,0.2)] transition-all">
                        <i class="fa-solid fa-eye text-blue-500"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase text-gray-600 bg-[#111] px-2 py-1 rounded border border-gray-800">Views</span>
                </div>
                <div>
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Total Tayangan</div>
                    <div class="text-2xl font-black text-white tracking-tight">{{ number_format($user_stats['total_views'] ?? 0) }}</div>
                </div>
            </div>

            <!-- Pendapatan Harian -->
            <div class="bg-[#111]/40 backdrop-blur-sm border border-white/5 rounded-xl p-5 hover:border-amber-500/50 hover:bg-[#111] transition-all duration-300 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center border border-amber-500/20 group-hover:shadow-[0_0_15px_rgba(245,158,11,0.2)] transition-all">
                        <i class="fa-solid fa-coins text-amber-500"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase text-gray-600 bg-[#111] px-2 py-1 rounded border border-gray-800">Harian</span>
                </div>
                <div>
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Pendapatan Harian</div>
                    <div class="text-2xl font-black text-white tracking-tight">Rp {{ number_format($financial_stats['daily_earning'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- STATS GRID - ROW 2 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Saldo Saat Ini -->
            <div class="bg-emerald-500/5 backdrop-blur-md border border-emerald-500/20 rounded-xl p-5 shadow-[0_0_20px_rgba(16,185,129,0.05)] hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] transition-all duration-300 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 group-hover:shadow-[0_0_15px_rgba(16,185,129,0.2)] transition-all">
                        <i class="fa-solid fa-wallet text-emerald-500"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase text-emerald-500 bg-emerald-500/20 border border-emerald-500/30 px-2 py-1 rounded">Dompet</span>
                </div>
                <div>
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Saldo Saat Ini</div>
                    <div class="text-2xl font-black text-white tracking-tight">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Minggu Ini -->
            <div class="bg-[#111]/40 border border-white/5 rounded-xl p-5 hover:border-orange-500/50 hover:bg-[#111] transition-all duration-300 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-[#0a0a0a] flex items-center justify-center border border-[#222] group-hover:border-orange-500/30 group-hover:shadow-[0_0_15px_rgba(249,115,22,0.2)] transition-all">
                        <i class="fa-regular fa-calendar-check text-orange-500"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase text-gray-600 bg-[#111] px-2 py-1 rounded border border-[#222]">Mingguan</span>
                </div>
                <div>
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Minggu Ini</div>
                    <div class="text-2xl font-black text-white tracking-tight">Rp {{ number_format($financial_stats['week_earning'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Bulan Ini -->
            <div class="bg-[#111]/40 backdrop-blur-sm border border-white/5 rounded-xl p-5 hover:border-indigo-500/50 hover:bg-[#111] transition-all duration-300 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-[#0a0a0a] flex items-center justify-center border border-[#222] group-hover:border-indigo-500/30 group-hover:shadow-[0_0_15px_rgba(99,102,241,0.2)] transition-all">
                        <i class="fa-regular fa-calendar-days text-indigo-500"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase text-gray-600 bg-[#111] px-2 py-1 rounded border border-[#222]">Bulanan</span>
                </div>
                <div>
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Bulan Ini</div>
                    <div class="text-2xl font-black text-white tracking-tight">Rp {{ number_format($financial_stats['month_earning'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Total Pendapatan -->
            <div class="bg-[#111]/40 backdrop-blur-sm border border-white/5 rounded-xl p-5 hover:border-purple-500/50 hover:bg-[#111] transition-all duration-300 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-[#0a0a0a] flex items-center justify-center border border-[#222] group-hover:border-purple-500/30 group-hover:shadow-[0_0_15px_rgba(168,85,247,0.2)] transition-all">
                        <i class="fa-solid fa-chart-line text-purple-500"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase text-gray-500 bg-[#111] px-2 py-1 rounded border border-[#222]">Total</span>
                </div>
                <div>
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Total Pendapatan</div>
                    <div class="text-2xl font-black text-white tracking-tight">Rp {{ number_format($financial_stats['total_lifetime'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- ACTIVITY STATISTICS (CHART) -->
        <div class="group relative overflow-hidden rounded-xl border border-[#222] bg-[#0a0a0a]/90 backdrop-blur shadow-xl">
            <div class="absolute inset-0 bg-gradient-to-b from-[#00ffff]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            
            <div class="p-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                             <div class="w-1 h-4 bg-[#00ffff] rounded-full shadow-[0_0_10px_rgba(0,255,255,0.8)]"></div>
                             <h3 class="text-lg font-black text-white tracking-wider uppercase">Statistik Aktivitas</h3>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">Monitoring pendapatan real-time minggu ini</p>
                    </div>
                    <div class="flex items-end gap-3 text-right">
                        <div>
                             <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Total Minggu Ini</div>
                             <div class="text-3xl font-black text-white tracking-tight">Rp {{ number_format($financial_stats['week_earning'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="mb-1">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-500/10 border border-green-500/20 text-[10px] font-bold text-green-500 uppercase tracking-wider">
                                <i class="fa-solid fa-arrow-trend-up"></i> +0.00%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Chart Container -->
                <div class="h-48 flex items-end justify-between gap-2 px-2 pb-2 border-b border-gray-800/50">
                    @php
                        $maxVal = max($chart_data) > 0 ? max($chart_data) : 1;
                    @endphp
                    @foreach($chart_data as $index => $data)
                        @php
                            $height = ($data / $maxVal) * 100;
                            $height = $data == 0 ? 2 : $height; 
                        @endphp
                         <div class="w-full h-full flex items-end relative group/bar" title="Rp {{ number_format($data, 0, ',', '.') }}">
                              <div class="w-full bg-[#050505] hover:bg-[#111] rounded-t transition-all duration-300 relative overflow-hidden" style="height: {{ $height }}%">
                                 <div class="absolute inset-0 bg-gradient-to-t from-[#00ffff]/20 via-[#00ffff]/5 to-transparent opacity-0 group-hover/bar:opacity-100 transition-opacity"></div>
                                 <div class="absolute bottom-0 left-0 right-0 h-1 bg-[#00ffff]/50 shadow-[0_0_10px_rgba(0,255,255,0.5)]"></div>
                              </div>
                             
                             <!-- Tooltip -->
                             <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-[#0a0a0a] border border-[#222] text-white text-[10px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover/bar:opacity-100 transition-all duration-200 whitespace-nowrap z-20 shadow-xl translate-y-2 group-hover/bar:translate-y-0 pointer-events-none">
                                Rp {{ number_format($data, 0, ',', '.') }}
                                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-[#0a0a0a] border-r border-b border-[#222] rotate-45"></div>
                             </div>
                        </div>
                    @endforeach
                </div>
                 <!-- X-Axis Labels -->
                 <div class="flex justify-between px-2 mt-4">
                     @foreach($days as $day)
                        <div class="w-full text-center">
                            <div class="text-[10px] font-bold text-gray-500 uppercase">{{ $day }}</div>
                        </div>
                     @endforeach
                 </div>
            </div>
        </div>

    </div>
</x-app-layout>
