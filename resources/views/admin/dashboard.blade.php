<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
                <!-- Group 1: Traffic -->
                <a href="{{ route('admin.users.online') }}" class="bg-[#0a0a0a] border-2 border-[#00ffff] p-5 rounded-xl shadow-lg relative overflow-hidden group transition-all block">
                    <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition">
                        <i class="fa-solid fa-users text-[#00ffff] text-3xl"></i>
                    </div>
                    <div class="text-gray-500 text-[10px] font-bold uppercase mb-2 flex items-center gap-1.5">
                        User Online
                        <span class="inline-flex items-center gap-1 bg-green-500/10 text-green-500 text-[8px] px-1 py-0.5 rounded border border-green-500/20">
                            <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span>
                            LIVE
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-[#00ffff]">{{ number_format($stats['online_users']) }}</div>
                    <p class="text-[9px] text-gray-500 mt-1">Last 5 mins</p>
                </a>
                
                <div class="bg-[#0a0a0a] border border-blue-500/20 p-5 rounded-xl shadow-lg relative overflow-hidden group hover:border-blue-500/50 transition-all">
                     <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition">
                        <i class="fa-solid fa-play text-blue-500 text-3xl"></i>
                    </div>
                    <div class="text-gray-500 text-[10px] font-bold uppercase mb-2">Semua Views</div>
                    <div class="text-2xl font-bold text-blue-500">{{ number_format($stats['total_views']) }}</div>
                    <p class="text-[9px] text-gray-500 mt-1">Platform total</p>
                </div>

                <a href="{{ route('admin.videos.all') }}" class="bg-[#0a0a0a] border border-[#222] p-5 rounded-xl shadow-lg relative overflow-hidden group hover:border-[#00ffff]/50 transition-all block">
                     <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition">
                        <i class="fa-solid fa-video text-[#00ffff] text-3xl"></i>
                    </div>
                    <div class="text-gray-500 text-[10px] font-bold uppercase mb-2">Total Video</div>
                    <div class="text-2xl font-bold text-[#00ffff]">{{ number_format($stats['total_videos']) }}</div>
                    <p class="text-[9px] text-gray-500 mt-1">User uploads</p>
                </a>

                <a href="{{ route('admin.earnings') }}" class="bg-[#0a0a0a] border border-emerald-500/20 p-5 rounded-xl shadow-lg relative overflow-hidden group hover:border-emerald-500/50 transition-all block">
                     <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition">
                        <i class="fa-solid fa-wallet text-emerald-500 text-3xl"></i>
                    </div>
                    <div class="text-gray-500 text-[10px] font-bold uppercase mb-2">Pendapatan User</div>
                    <div class="text-2xl font-bold text-emerald-500">Rp {{ number_format($stats['total_earnings'], 0, ',', '.') }}</div>
                    <p class="text-[9px] text-gray-500 mt-1">Accumulated</p>
                </a>

                <a href="{{ route('admin.users.list') }}" class="bg-[#0a0a0a] border border-indigo-500/20 p-5 rounded-xl shadow-lg relative overflow-hidden group hover:border-indigo-500/50 transition-all block">
                     <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition">
                        <i class="fa-solid fa-user-check text-indigo-500 text-3xl"></i>
                    </div>
                    <div class="text-gray-500 text-[10px] font-bold uppercase mb-2">User Terdaftar</div>
                    <div class="text-2xl font-bold text-indigo-500">{{ number_format($stats['total_users']) }}</div>
                    <p class="text-[9px] text-gray-500 mt-1">Total accounts</p>
                </a>

                <a href="{{ route('admin.users.banned') }}" class="bg-[#0a0a0a] border border-red-500/20 p-5 rounded-xl shadow-lg relative overflow-hidden group hover:border-red-500/50 transition-all block">
                     <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition">
                        <i class="fa-solid fa-user-slash text-red-500 text-3xl"></i>
                    </div>
                    <div class="text-gray-500 text-[10px] font-bold uppercase mb-2">User Terban</div>
                    <div class="text-2xl font-bold text-red-500">{{ number_format($stats['banned_users']) }}</div>
                    <p class="text-[9px] text-gray-500 mt-1">Suspended</p>
                </a>

                <a href="{{ route('admin.bans') }}" class="bg-[#0a0a0a] border border-red-600/20 p-5 rounded-xl shadow-lg relative overflow-hidden group hover:border-red-600/50 transition-all block">
                     <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition">
                        <i class="fa-solid fa-ban text-red-600 text-3xl"></i>
                    </div>
                    <div class="text-gray-500 text-[10px] font-bold uppercase mb-2">IP Terban</div>
                    <div class="text-2xl font-bold text-red-600">{{ number_format($stats['banned_ips']) }}</div>
                    <p class="text-[9px] text-gray-500 mt-1">Blocked IPs</p>
                </a>

                <a href="{{ route('admin.withdrawals.status', 'pending') }}" class="bg-[#0a0a0a] border border-amber-500/20 p-5 rounded-xl shadow-lg relative overflow-hidden group hover:border-amber-500/50 transition-all block">
                     <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition">
                        <i class="fa-solid fa-clock text-amber-500 text-3xl"></i>
                    </div>
                    <div class="text-gray-500 text-[10px] font-bold uppercase mb-2">Pending WD</div>
                    <div class="text-2xl font-bold text-amber-500">{{ number_format($stats['pending_withdrawals']) }}</div>
                    <p class="text-[9px] text-gray-500 mt-1">Waiting approval</p>
                </a>

                 <a href="{{ route('admin.withdrawals.status', 'approved') }}" class="bg-[#0a0a0a] border border-emerald-500/20 p-5 rounded-xl shadow-lg relative overflow-hidden group hover:border-emerald-500/50 transition-all block">
                     <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition">
                        <i class="fa-solid fa-check-circle text-emerald-500 text-3xl"></i>
                    </div>
                    <div class="text-gray-500 text-[10px] font-bold uppercase mb-2">WD Selesai</div>
                    <div class="text-2xl font-bold text-emerald-500">{{ number_format($stats['approved_withdrawals']) }}</div>
                    <p class="text-[9px] text-gray-500 mt-1">Processed</p>
                </a>

                 <a href="{{ route('admin.withdrawals.status', 'rejected') }}" class="bg-[#0a0a0a] border border-red-500/20 p-5 rounded-xl shadow-lg relative overflow-hidden group hover:border-red-500/50 transition-all block">
                     <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition">
                        <i class="fa-solid fa-circle-xmark text-red-500 text-3xl"></i>
                    </div>
                    <div class="text-gray-500 text-[10px] font-bold uppercase mb-2">WD Ditolak</div>
                    <div class="text-2xl font-bold text-red-500">{{ number_format($stats['rejected_withdrawals']) }}</div>
                    <p class="text-[9px] text-gray-500 mt-1">Failed/Rejected</p>
                </a>
            </div>

            <!-- Management Section -->
            <div class="bg-[#0a0a0a] border border-[#222] rounded-xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-[#222] flex justify-between items-center bg-[#050505]">
                    <h3 class="text-white font-bold">Admin Management</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- ADS / Iklan -->
                    <a href="{{ route('admin.ads') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-yellow-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-rectangle-ad text-yellow-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">ADS / Iklan</div>
                            <div class="text-gray-400 text-xs">Manage advertisement codes and placements.</div>
                        </div>
                    </a>

                    <!-- Ban Management -->
                    <a href="{{ route('admin.bans') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-gavel text-red-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Ban Management</div>
                            <div class="text-gray-400 text-xs">Unban IP / User & Reset Cooldown</div>
                        </div>
                    </a>

                    <!-- Buat User Baru -->
                    <a href="{{ route('admin.users.create') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-user-plus text-purple-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Buat User Baru</div>
                            <div class="text-gray-400 text-xs">Create new user accounts manually.</div>
                        </div>
                    </a>

                    <!-- Captcha -->
                    <a href="{{ route('admin.captcha') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-teal-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-robot text-teal-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Captcha</div>
                            <div class="text-gray-400 text-xs">Configure CAPTCHA settings and keys.</div>
                        </div>
                    </a>

                    <!-- Edit Statistik -->
                    <a href="{{ route('admin.statistics') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-chart-simple text-green-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Edit Statistik</div>
                            <div class="text-gray-400 text-xs">Manually edit user balance and views.</div>
                        </div>
                    </a>

                    <!-- Financial Settings (Set CPM) -->
                    <a href="{{ route('admin.cpm') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-coins text-green-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Financial Settings (CPM)</div>
                            <div class="text-gray-400 text-xs">Set CPM rates & Withdrawal Limits.</div>
                        </div>
                    </a>

                    <!-- Hapus Data -->
                    <a href="{{ route('admin.data.cleanup') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-trash-can text-red-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Hapus Data</div>
                            <div class="text-gray-400 text-xs">Clean up old data, logs, and sessions.</div>
                        </div>
                    </a>

                    <!-- Log Aktivitas -->
                    <a href="{{ route('admin.logs') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-file-lines text-blue-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Log Aktivitas</div>
                            <div class="text-gray-400 text-xs">Check all admin actions and logs.</div>
                        </div>
                    </a>

                    <!-- Papan Pengumuman -->
                    <a href="{{ route('admin.announcements') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-pink-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-bullhorn text-pink-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Papan Pengumuman</div>
                            <div class="text-gray-400 text-xs">Edit announcement text and color.</div>
                        </div>
                    </a>

                    <!-- Peringatan Keamanan -->
                    <a href="{{ route('admin.security_alerts') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-shield-halved text-red-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Peringatan Keamanan</div>
                            <div class="text-gray-400 text-xs text-red-400/80 font-bold uppercase tracking-widest animate-pulse">Critical issues & attacks detected.</div>
                        </div>
                    </a>

                    <!-- Theme Customization (Restored Standalone) -->
                    <a href="{{ route('admin.theme') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-palette text-purple-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Kustomisasi Tema</div>
                            <div class="text-gray-400 text-xs">Atur gaya visual dan warna situs</div>
                        </div>
                    </a>

                    <!-- System Health -->
                    <a href="{{ route('admin.system.health') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-heart-pulse text-green-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Kesehatan Sistem</div>
                            <div class="text-gray-400 text-xs">Monitor server resources & logs</div>
                        </div>
                    </a>

                    <!-- Backup & Restore -->
                    <a href="{{ route('admin.backup') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-database text-blue-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Backup & Restore</div>
                            <div class="text-gray-400 text-xs">Database backup management</div>
                        </div>
                    </a>

                    <!-- Badges -->
                    <a href="{{ route('admin.badges') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-yellow-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-trophy text-yellow-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Badge Management</div>
                            <div class="text-gray-400 text-xs">Manage user achievements</div>
                        </div>
                    </a>

                    <!-- Leaderboard -->
                    <a href="{{ route('admin.leaderboard') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-ranking-star text-purple-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Leaderboard</div>
                            <div class="text-gray-400 text-xs">Top performers on platform</div>
                        </div>
                    </a>

                    <!-- Permintaan Penarikan -->
                    <a href="{{ route('admin.withdrawals') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-orange-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-money-bill-transfer text-orange-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Permintaan Penarikan</div>
                            <div class="text-gray-400 text-xs">Approve or reject user withdrawals.</div>
                        </div>
                    </a>

                    <!-- Site Settings -->
                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-cyan-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-gears text-cyan-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">Site Settings</div>
                            <div class="text-gray-400 text-xs">Configure Ad Code, CPM Rates, etc.</div>
                        </div>
                    </a>

                    <!-- User List -->
                    <a href="{{ route('admin.users.list') }}" class="flex items-center gap-4 p-4 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg transition">
                        <div class="w-12 h-12 bg-indigo-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-users-viewfinder text-indigo-500 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold">User List & Files</div>
                            <div class="text-gray-400 text-xs">Inspect all users and their uploaded files.</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
