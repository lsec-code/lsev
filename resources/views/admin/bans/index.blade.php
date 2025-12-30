<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pb-6 border-b border-[#222]">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-900/20 flex items-center justify-center border border-red-500/20">
                    <i class="fa-solid fa-gavel text-red-500 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Ban Management</h1>
                    <p class="text-gray-400 text-sm">Kelola IP yang diblokir dan User yang disuspend.</p>
                </div>
            </div>
            <div class="ml-auto">
                <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4 flex items-center gap-3 animate-fade-in">
                <i class="fa-solid fa-check-circle text-green-500"></i>
                <p class="text-green-500 font-bold text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- IP Bans Section -->
            <div class="bg-[#0a0a0a] border border-[#222] rounded-xl shadow-[0_0_50px_rgba(0,0,0,0.3)] overflow-hidden">
                <div class="p-6 border-b border-[#222] flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-network-wired text-gray-500"></i> IP Banned
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Daftar alamat IP yang diblokir sistem.</p>
                    </div>
                    <span class="px-3 py-1 bg-red-900/20 border border-red-500/20 text-red-500 rounded text-xs font-bold">{{ $banned_ips->count() }} Active</span>
                </div>
                
                <div class="p-4 space-y-3">
                    @forelse($banned_ips as $ban)
                        <div class="p-4 bg-[#111] border border-[#222] rounded-xl hover:border-[#333] transition flex items-center justify-between group"
                             x-data="{
                                expiresAt: {{ $ban->expires_at ? $ban->expires_at->timestamp * 1000 : 'null' }},
                                now: new Date().getTime(),
                                timeLeft: '',
                                updateTimer() {
                                    if (!this.expiresAt) return;
                                    const distance = this.expiresAt - new Date().getTime();
                                    if (distance < 0) {
                                        this.timeLeft = 'EXPIRED';
                                        return;
                                    }
                                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                    this.timeLeft = `${hours}h ${minutes}m ${seconds}s`;
                                }
                             }"
                             x-init="setInterval(() => updateTimer(), 1000); updateTimer();">
                            
                            <div class="space-y-3 w-full">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-mono font-bold text-white bg-[#1a1a1a] px-3 py-1.5 rounded-lg border border-[#333]">
                                        {{ $ban->ip_address }}
                                    </span>
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wider font-bold bg-[#1a1a1a] px-2 py-1 rounded border border-[#222]">
                                        BANNED {{ $ban->banned_at->diffForHumans() }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2 p-3 bg-[#151515] rounded-lg border border-[#222]">
                                    <div>
                                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">ALASAN / JENIS SERANGAN</p>
                                        <p class="text-xs text-red-400 font-mono flex items-center gap-2">
                                            <i class="fa-solid fa-bug"></i>
                                            {{ $ban->last_pattern ?? 'Suspicious Activity Detected' }}
                                        </p>
                                    </div>
                                    @if(!empty($ban->violations) && count($ban->violations) > 0)
                                    <div>
                                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">TARGET URL</p>
                                        <p class="text-xs text-blue-400 font-mono truncate max-w-[200px]">
                                            {{ $ban->violations[count($ban->violations)-1]['url'] ?? 'N/A' }}
                                        </p>
                                    </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-xs font-medium pt-1">
                                    <span class="flex items-center gap-1.5 text-yellow-500 bg-yellow-500/10 px-2 py-1 rounded border border-yellow-500/10">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        {{ $ban->attempt_count }} Violations
                                    </span>

                                    @if($ban->expires_at)
                                        <span class="flex items-center gap-2 text-green-400" x-show="timeLeft !== 'EXPIRED'">
                                            <i class="fa-solid fa-hourglass-half fa-spin-slow"></i>
                                            <span class="font-mono font-bold text-sm" x-text="timeLeft">Loading...</span>
                                        </span>
                                        <span class="text-gray-500" x-show="timeLeft === 'EXPIRED'">
                                            Ban Expired
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1.5 text-red-500 bg-red-500/10 px-2 py-1 rounded border border-red-500/10">
                                            <i class="fa-solid fa-ban"></i>
                                            PERMANENT BAN
                                        </span>
                                    @endif
                                    
                                    <form action="{{ route('admin.bans.unban_ip', $ban->id) }}" method="POST" class="ml-auto">
                                        @csrf
                                        <button type="submit" class="bg-red-900/20 hover:bg-red-900/40 text-red-500 border border-red-900/30 hover:border-red-500/50 px-4 py-1.5 rounded text-[10px] font-bold uppercase tracking-wider transition-all">
                                            UNBAN IP
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center flex flex-col items-center justify-center text-gray-500">
                            <i class="fa-solid fa-check-circle text-2xl mb-3 text-[#222]"></i>
                            <p class="text-sm">Tidak ada IP yang diblokir saat ini.</p>
                        </div>
                    @endforelse
                    
                    @if($banned_ips->hasPages())
                        <div class="pt-2">
                            {{ $banned_ips->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Suspended Users Section -->
            <div class="bg-[#0a0a0a] border border-[#222] rounded-xl shadow-[0_0_50px_rgba(0,0,0,0.3)] overflow-hidden">
                <div class="p-6 border-b border-[#222] flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-users-slash text-red-500"></i> Suspended Users
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Daftar pengguna yang ditangguhkan.</p>
                    </div>
                    <span class="px-3 py-1 bg-red-900/20 border border-red-500/20 text-red-500 rounded text-xs font-bold">{{ $suspended_users->count() }} Active</span>
                </div>
                
                <div class="p-4 space-y-3">
                    @forelse($suspended_users as $user)
                        <div class="p-4 bg-[#111] border border-[#222] rounded-xl hover:border-[#333] transition flex items-center justify-between group">
                            <div class="space-y-3 w-full">
                                <div class="flex items-center gap-4">
                                    <div class="relative shrink-0">
                                        <img src="{{ $user->getAvatarUrl() }}" class="w-12 h-12 rounded-full border-2 border-[#1a1a1a] object-cover bg-black">
                                        <div class="absolute -bottom-1 -right-1 bg-red-600 text-[8px] font-black text-white px-1.5 py-0.5 rounded border border-[#111] shadow-sm">BANNED</div>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-bold text-white">{{ $user->username }}</span>
                                            <span class="text-[9px] font-mono bg-[#1a1a1a] border border-[#333] text-gray-400 px-1.5 py-0.5 rounded">ID: {{ $user->id }}</span>
                                        </div>
                                        <div class="text-[10px] text-gray-600 uppercase tracking-wide font-bold">
                                            Banned {{ $user->suspended_at ? $user->suspended_at->diffForHumans() : 'Unknown date' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 bg-[#151515] rounded-lg border border-[#222]">
                                    <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">ALASAN SUSPEND</p>
                                    <p class="text-xs text-red-400 font-medium flex items-start gap-2">
                                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                                        {{ $user->suspension_reason ?? 'Pelanggaran Syarat & Ketentuan' }}
                                    </p>
                                </div>
                                
                                <form action="{{ route('admin.bans.unban_user', $user->id) }}" method="POST" class="text-right">
                                    @csrf
                                    <button type="submit" class="bg-red-900/20 hover:bg-red-900/40 text-red-500 border border-red-900/30 hover:border-red-500/50 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all shadow-[0_0_10px_rgba(220,38,38,0.1)] hover:shadow-[0_0_15px_rgba(220,38,38,0.2)]">
                                        UNBAN USER
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center flex flex-col items-center justify-center text-gray-500">
                            <i class="fa-solid fa-user-check text-2xl mb-3 text-[#222]"></i>
                            <p class="text-sm">Tidak ada User yang disuspend saat ini.</p>
                        </div>
                    @endforelse

                    @if($suspended_users->hasPages())
                        <div class="pt-2">
                            {{ $suspended_users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
