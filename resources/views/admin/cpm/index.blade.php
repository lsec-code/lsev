<x-app-layout>


<div class="p-6">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight flex items-center gap-3">
                    <i class="fa-solid fa-coins text-yellow-500"></i>
                    Finance & Booster
                </h1>
                <p class="text-gray-500 text-sm mt-1">Konfigurasi CPM, Limit Penarikan, dan Booster Otomatis.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" 
                class="bg-[#0a0a0a] hover:bg-[#111] border border-[#222] text-gray-500 font-bold py-2.5 px-6 rounded-xl transition text-[10px] uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 rounded-xl p-4 flex items-center gap-3">
                <i class="fa-solid fa-check-circle text-green-500"></i>
                <p class="text-green-500 text-sm font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column: CPM & Withdrawal Settings -->
            <div class="space-y-8">
                <div class="bg-[#0a0a0a] border border-[#222] rounded-2xl p-6">
                    <h2 class="text-white font-black uppercase tracking-widest text-xs mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-gear text-[#00ffff]"></i>
                        Settings Keuangan
                    </h2>
                    
                    <form method="POST" action="{{ route('admin.cpm.update') }}" class="space-y-6">
                        @csrf
                        <!-- Withdrawal -->
                        <div class="space-y-3">
                            <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest">Min. Withdrawal (IDR)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-4 flex items-center text-gray-600 font-bold text-sm">Rp</span>
                                <input type="number" name="min_withdrawal" value="{{ old('min_withdrawal', $min_withdrawal ?? 250000) }}" 
                                    class="w-full bg-[#050505] border border-[#222] rounded-xl px-4 py-3 pl-12 text-white text-sm focus:border-[#00ffff]/50 focus:ring-1 focus:ring-primary/20 outline-none transition-all">
                            </div>
                        </div>

                        <hr class="border-[#222]/50">

                        <!-- CPM Flat Toggle -->
                        <div x-data="{ enabled: {{ $cpm_flat_enabled ? 'true' : 'false' }} }" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Flat CPM Rate (1K Views)</label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="cpm_flat_enabled" value="false">
                                    <input type="checkbox" name="cpm_flat_enabled" value="true" class="sr-only peer" x-model="enabled">
                                    <div class="w-11 h-6 bg-[#050505] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-gray-500 after:border-gray-500 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#00ffff] after:bg-white after:border-white"></div>
                                </label>
                            </div>
                            
                            <input type="number" name="cpm_rate" value="{{ $cpm_rate }}" 
                                class="w-full bg-[#050505] border border-[#222] rounded-xl px-4 py-3 text-white text-sm focus:border-[#00ffff]/50 focus:ring-1 focus:ring-primary/20 outline-none transition-all disabled:opacity-30" 
                                :disabled="!enabled" placeholder="Rp / 1.000 Views">

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest">Dynamic Min (Rp/View)</label>
                                    <input type="number" name="cpm_min_rate" value="{{ old('cpm_min_rate', $cpm_min ?? 1) }}" 
                                        class="w-full bg-[#050505] border border-[#222] rounded-xl px-4 py-3 text-white text-sm focus:border-[#00ffff]/50 focus:ring-1 focus:ring-primary/20 outline-none transition-all disabled:opacity-30"
                                        :disabled="enabled">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest">Dynamic Max (Rp/View)</label>
                                    <input type="number" name="cpm_max_rate" value="{{ old('cpm_max_rate', $cpm_max ?? 100) }}" 
                                        class="w-full bg-[#050505] border border-[#222] rounded-xl px-4 py-3 text-white text-sm focus:border-[#00ffff]/50 focus:ring-1 focus:ring-primary/20 outline-none transition-all disabled:opacity-30"
                                        :disabled="enabled">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-[#00ffff]/10 hover:bg-[#00ffff] border border-[#00ffff] text-[#00ffff] hover:text-black font-black py-4 rounded-xl uppercase tracking-widest text-[10px] transition-all shadow-[0_0_20px_rgba(0,255,255,0.1)] hover:shadow-[0_0_30px_rgba(0,255,255,0.4)]">
                            <i class="fa-solid fa-save mr-2"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Add Booster Form -->
            <div class="lg:col-span-1">
                <div class="bg-[#0a0a0a] border border-[#222] rounded-2xl p-6">
                    <h2 class="text-white font-black uppercase tracking-widest text-xs mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-rocket text-[#00ffff]"></i>
                        Tambah Booster Baru
                    </h2>
                    
                    <form action="{{ route('admin.boosts.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <!-- User Search -->
                        <div x-data="{ 
                            query: '', 
                            results: [], 
                            showResults: false,
                            loading: false,
                            selectedUsers: [],
                            search() {
                                if (this.query.length < 1) { 
                                    this.results = []; 
                                    this.showResults = false; 
                                    return; 
                                }
                                this.loading = true;
                                this.showResults = true;
                                fetch('{{ route('admin.statistics.search') }}?minimal=1&q=' + this.query)
                                    .then(res => res.json())
                                    .then(data => {
                                        this.results = data;
                                        this.loading = false;
                                    })
                                    .catch(() => {
                                        this.loading = false;
                                    });
                            },
                            add(user) {
                                if (!this.selectedUsers.some(u => u.id === user.id)) {
                                    this.selectedUsers.push(user);
                                }
                                this.query = '';
                                this.showResults = false;
                            },
                            remove(index) {
                                this.selectedUsers.splice(index, 1);
                            }
                        }" class="space-y-4">
                            <div>
                                <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Target Users</label>
                                
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <template x-for="(user, index) in selectedUsers" :key="user.id">
                                        <div class="bg-[#00ffff]/10 border border-[#00ffff]/30 text-[#00ffff] text-[10px] font-bold rounded-full px-3 py-1 flex items-center gap-2">
                                            <span x-text="user.username"></span>
                                            <input type="hidden" name="emails[]" :value="user.email">
                                            <button type="button" @click="remove(index)" class="hover:text-white transition-colors">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-search text-[10px]" :class="loading ? 'text-cyan-500 animate-spin' : 'text-gray-600'"></i>
                                    </div>
                                    <input type="text" x-model="query" @input.debounce.300ms="search()" @focus="if(query.length > 0) showResults = true"
                                        class="w-full bg-[#050505] border border-[#222] rounded-xl px-4 py-3 text-white text-sm focus:border-[#00ffff]/50 focus:ring-1 focus:ring-primary/20 outline-none transition-all pl-12" 
                                        placeholder="Cari username atau email..." autocomplete="off">
                                    
                                    <!-- Results Dropdown -->
                                    <div x-show="showResults" @click.away="showResults = false"
                                        class="absolute z-[100] w-full mt-2 bg-[#0a0a0a] border border-[#222] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.8)] overflow-hidden"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0">
                                        
                                        <div x-show="loading" class="px-4 py-8 text-center">
                                            <i class="fa-solid fa-circle-notch animate-spin text-cyan-500 text-xl mb-2"></i>
                                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Mencari User...</p>
                                        </div>

                                        <template x-for="user in results" :key="user.id">
                                            <div @click="add(user)" class="px-4 py-3 hover:bg-[#00ffff]/10 cursor-pointer flex items-center gap-3 transition group/item">
                                                <img :src="user.avatar_url" class="w-8 h-8 rounded-full bg-[#050505] border border-white/5 object-cover">
                                                <div class="flex-1">
                                                    <div class="text-white text-xs font-bold group-hover/item:text-[#00ffff] transition-colors" x-text="user.username"></div>
                                                    <div class="text-gray-500 text-[9px] font-mono" x-text="user.email"></div>
                                                </div>
                                                <i class="fa-solid fa-plus text-[10px] text-gray-500 group-hover/item:text-[#00ffff] transition-colors"></i>
                                            </div>
                                        </template>

                                        <div x-show="!loading && results.length === 0 && query.length > 0" class="px-4 py-8 text-center">
                                            <i class="fa-solid fa-ghost text-gray-800 text-xl mb-2"></i>
                                            <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest text-wrap px-4">User tidak ditemukan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Speed settings -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest">Min Speed</label>
                                <div class="relative">
                                    <input type="number" name="views_per_minute" value="10" min="1" max="1000" class="w-full bg-[#050505] border border-[#222] rounded-xl px-4 py-3 text-white text-sm focus:border-[#00ffff]/50 focus:ring-1 focus:ring-primary/20 outline-none pr-12">
                                    <span class="absolute inset-y-0 right-4 flex items-center text-[10px] font-bold text-gray-600">/m</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest">Max Speed</label>
                                <div class="relative">
                                    <input type="number" name="views_per_minute_max" min="1" max="1000" class="w-full bg-[#050505] border border-[#222] rounded-xl px-4 py-3 text-white text-sm focus:border-[#00ffff]/50 focus:ring-1 focus:ring-primary/20 outline-none pr-12" placeholder="Auto">
                                    <span class="absolute inset-y-0 right-4 flex items-center text-[10px] font-bold text-gray-600">/m</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest">Durasi (Menit)</label>
                            <input type="number" name="duration_minutes" value="60" min="1" class="w-full bg-[#050505] border border-[#222] rounded-xl px-4 py-3 text-white text-sm focus:border-[#00ffff]/50 focus:ring-1 focus:ring-primary/20 outline-none">
                        </div>

                        <button type="submit" class="w-full bg-[#00ffff]/10 hover:bg-[#00ffff] border border-[#00ffff] text-[#00ffff] hover:text-black font-black py-4 rounded-xl uppercase tracking-widest text-xs transition-all hover:scale-[1.02] active:scale-[0.98] shadow-[0_0_20px_rgba(0,255,255,0.1)] hover:shadow-[0_0_30px_rgba(0,255,255,0.4)] flex items-center justify-center gap-2">
                            <i class="fa-solid fa-play"></i> Mulai Booster
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Active Booster Table -->
        <div class="space-y-4">
            <h3 class="text-white font-black uppercase tracking-widest text-xs flex items-center gap-2">
                <i class="fa-solid fa-chart-bar text-cyan-500"></i>
                Booster Activity & Stats
            </h3>
            
            <div class="overflow-x-auto rounded-2xl border border-[#222] bg-[#0a0a0a]">
                <table class="w-full text-left text-sm text-gray-500 border-collapse">
                    <thead class="bg-[#050505]/80 uppercase tracking-widest font-black text-[10px] text-gray-500 border-b border-[#222]/50">
                        <tr>
                            <th class="px-6 py-4"><i class="fa-solid fa-user mr-2 opacity-50"></i>User</th>
                            <th class="px-6 py-4"><i class="fa-solid fa-video mr-2 opacity-50"></i>Video</th>
                            <th class="px-6 py-4"><i class="fa-solid fa-bolt mr-2 opacity-50"></i>Speed</th>
                            <th class="px-6 py-4"><i class="fa-solid fa-clock mr-2 opacity-50"></i>Durasi</th>
                            <th class="px-6 py-4 text-center"><i class="fa-solid fa-chart-line mr-2 opacity-50"></i>Stats</th>
                            <th class="px-6 py-4 text-center"><i class="fa-solid fa-circle-info mr-2 opacity-50"></i>Status</th>
                            <th class="px-6 py-4 text-center"><i class="fa-solid fa-gears mr-2 opacity-50"></i>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-color/30">
                        @forelse($boosts as $boost)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-white font-medium whitespace-nowrap">{{ Str::limit(optional($boost->user)->email ?? 'User Deleted', 15) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($boost->video)
                                    <a href="{{ route('videos.show', $boost->video->slug) }}" class="text-[#00ffff] hover:text-[#00ffff]-hover transition-colors flex items-center gap-2" target="_blank">
                                        <i class="fa-solid fa-external-link text-[10px] opacity-30"></i>
                                        {{ Str::limit($boost->video->title, 12) }}
                                    </a>
                                @else
                                    <span class="text-red-500/70 italic text-xs">Deleted Video</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">
                                <span class="bg-green-500/10 text-green-400 px-2 py-1 rounded-md border border-green-500/20 whitespace-nowrap">
                                    {{ $boost->views_per_minute }}-{{ $boost->max_views_per_minute ?? $boost->views_per_minute }}/m
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($boost->status == 'active')
                                    <div class="flex flex-col">
                                        <span class="text-[#00ffff] font-bold live-timer text-xs whitespace-nowrap" data-expires="{{ $boost->expires_at->toIso8601String() }}">
                                            <i class="fa-solid fa-hourglass-half mr-1 animate-spin"></i> 
                                            {{ $boost->expires_at->diff(now())->format('%H:%I:%S') }}
                                        </span>
                                    </div>
                                @elseif($boost->status == 'completed')
                                    <span class="text-green-500/80 text-xs flex items-center gap-1 font-bold whitespace-nowrap">
                                        <i class="fa-solid fa-circle-check"></i> Selesai
                                    </span>
                                @elseif($boost->status == 'cancelled')
                                    @php
                                        $duration = $boost->started_at && $boost->updated_at 
                                            ? $boost->started_at->diff($boost->updated_at)->format('%H:%I:%S') 
                                            : '00:00:00';
                                    @endphp
                                    <span class="text-orange-500/80 text-[10px] flex items-center gap-1 whitespace-nowrap">
                                        <i class="fa-solid fa-stop-circle"></i> Stop: {{ $duration }}
                                    </span>
                                @else
                                    <span class="text-gray-600">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-eye text-[10px] text-gray-600"></i>
                                        <span class="text-white font-mono text-xs">{{ number_format($boost->views_added) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-coins text-[10px] text-yellow-600/50"></i>
                                        <span class="text-yellow-500 font-mono text-[10px]">Rp{{ number_format($boost->earnings_added, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($boost->status == 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-[#00ffff]/10 text-[#00ffff] border border-[#00ffff]/20 shadow-[0_0_10px_rgba(var(--primary-rgb),0.1)]">
                                        <span class="w-1 h-1 rounded-full bg-[#00ffff] mr-1.5 animate-pulse"></span>
                                        Active
                                    </span>
                                @elseif($boost->status == 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-gray-800 text-gray-500 border border-gray-700">
                                        Done
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-red-900/10 text-red-500/70 border border-red-500/10">
                                        {{ $boost->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($boost->status == 'active')
                                <form id="stop-boost-{{ $boost->id }}" action="{{ route('admin.boosts.destroy', $boost->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            onclick="confirmAction('Hentikan booster ini? Proses tidak dapat dibatalkan.', () => document.getElementById('stop-boost-{{ $boost->id }}').submit())"
                                            class="w-8 h-8 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300 flex items-center justify-center border border-red-500/20 mx-auto">
                                        <i class="fa-solid fa-stop text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 opacity-30">
                                    <i class="fa-solid fa-ghost text-4xl"></i>
                                    <span class="text-xs font-bold uppercase tracking-widest">Tidak ada booster aktif.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($boosts->hasPages())
                <div class="px-6 py-4 border-t border-[#1a1a1a]">
                    {{ $boosts->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    let isReloading = false;
    function updateTimers() {
        if (isReloading) return;
        
        const timers = document.querySelectorAll('.live-timer');
        let needsReload = false;

        timers.forEach(timer => {
            const expiresAt = new Date(timer.dataset.expires).getTime();
            const now = new Date().getTime();
            const diff = expiresAt - now;

            if (diff <= 0) {
                timer.innerHTML = '<span class="text-green-500 font-black flex items-center gap-1"><i class="fa-solid fa-check"></i> Selesai (Reloading...)</span>';
                needsReload = true;
                return;
            }

            const h = Math.floor(diff / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            const display = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            if (timer.innerText.indexOf(display) === -1) {
                timer.innerHTML = `<i class="fa-solid fa-hourglass-half mr-1 animate-spin-slow"></i> ${display}`;
            }
        });

        if (needsReload) {
            isReloading = true;
            setTimeout(() => window.location.reload(), 2000);
        }
    }

    setInterval(updateTimers, 1000);

    function startBoosterTest() {
        fetch('{{ route('admin.boosts.process') }}')
            .then(response => response.json())
            .then(data => console.log('Booster triggered:', data))
            .catch(error => console.error('Booster Error:', error));
    }
</script>
</x-app-layout>
