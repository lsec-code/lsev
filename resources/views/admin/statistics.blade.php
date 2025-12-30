<x-app-layout>
    <div class="py-6 md:py-12">
        <div class="max-w-4xl mx-auto px-4 md:px-6 lg:px-8">
            <div class="bg-[#0a0a0a] border border-[#222] shadow-sm sm:rounded-lg p-5 md:p-8">
                <div class="flex items-center gap-4 mb-8">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-white transition-colors">
                        <i class="fa-solid fa-arrow-left text-xl"></i>
                    </a>
                    <h2 class="text-xl font-bold text-white uppercase tracking-wider">Edit Statistik Pengguna</h2>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-900/20 border border-green-500/50 text-green-400 rounded text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-10 relative">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3">Cari Pengguna (Nama/Email/Username)</label>
                    <div class="relative">
                        <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="text" id="user-search" class="w-full bg-[#050505] border border-[#222] rounded-lg pl-12 pr-4 py-4 text-sm text-white focus:border-[#00ffff] focus:outline-none transition-all focus:ring-1 focus:ring-[#00ffff]/20" placeholder="Ketik minimal 3 karakter...">
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div id="search-results" class="absolute left-0 right-0 top-full mt-2 bg-[#0a0a0a]/95 backdrop-blur-md border border-[#222] rounded-lg shadow-2xl z-50 overflow-hidden hidden">
                        <!-- Ajax results here -->
                    </div>
                </div>

                <!-- Stats Display (Hidden until user selected) -->
                <div id="edit-form-container" class="hidden animate-in fade-in slide-in-from-top-4 duration-300" x-data="{ showAddModal: false }">
                    <div class="border-t border-[#222] pt-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 id="selected-user-name" class="text-lg font-bold text-[#00ffff]"></h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Row 1 -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Video</label>
                                <input type="text" id="stat-total-videos" readonly class="w-full bg-[#050505] border border-[#222] rounded-lg px-4 py-3 text-sm text-gray-400 cursor-not-allowed">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Penonton Aktif (Live)</label>
                                <input type="text" id="stat-active-viewers" readonly class="w-full bg-[#050505] border border-[#222] rounded-lg px-4 py-3 text-sm text-gray-400 cursor-not-allowed">
                            </div>

                            <!-- Row 2 -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Tayangan (Views)</label>
                                <input type="text" id="stat-total-views" readonly class="w-full bg-[#050505] border border-[#222] rounded-lg px-4 py-3 text-sm text-gray-400 cursor-not-allowed">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Pendapatan Harian (IDR)</label>
                                <div class="flex gap-2">
                                    <input type="text" id="stat-daily-earnings" readonly class="w-full bg-[#050505] border border-[#222] rounded-lg px-4 py-3 text-sm text-gray-400 cursor-not-allowed">
                                    <button @click="showAddModal = true" class="bg-[#00ffff]/10 hover:bg-[#00ffff]/20 border border-[#00ffff]/20 text-[#00ffff] px-4 rounded-lg text-xs font-bold uppercase whitespace-nowrap transition-all">
                                        + Tambah
                                    </button>
                                </div>
                            </div>

                            <!-- Row 3 -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Saldo Saat Ini (IDR)</label>
                                <input type="text" id="stat-balance" readonly class="w-full bg-[#050505] border border-[#222] rounded-lg px-4 py-3 text-sm text-gray-400 cursor-not-allowed">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Minggu Ini (IDR)</label>
                                <input type="text" id="stat-week-earnings" readonly class="w-full bg-[#050505] border border-[#222] rounded-lg px-4 py-3 text-sm text-gray-400 cursor-not-allowed">
                            </div>

                            <!-- Row 4 -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Bulan Ini (IDR)</label>
                                <input type="text" id="stat-month-earnings" readonly class="w-full bg-[#050505] border border-[#222] rounded-lg px-4 py-3 text-sm text-gray-400 cursor-not-allowed">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Pendapatan (Lifetime IDR)</label>
                                <input type="text" id="stat-lifetime-earnings" readonly class="w-full bg-[#050505] border border-[#222] rounded-lg px-4 py-3 text-sm text-gray-400 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- Add Limit Modal -->
                    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showAddModal = false"></div>
                        <div class="relative bg-[#0a0a0a] border border-[#222] rounded-xl p-6 w-full max-w-md shadow-2xl animate-in zoom-in-95 duration-200">
                            <h3 class="text-lg font-bold text-white mb-2">Tambah Saldo & Pendapatan</h3>
                            <p class="text-xs text-gray-500 mb-6">Masukkan jumlah nominal dalam Rupiah (IDR). Saldo user dan pendapatan hari ini akan bertambah.</p>
                            
                            <form action="{{ route('admin.statistics.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" id="modal-user-id">
                                
                                <div class="mb-6">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Nominal Tambahan (IDR)</label>
                                    <input type="number" name="amount_to_add" class="w-full bg-[#050505] border border-[#222] rounded-lg px-4 py-3 text-sm text-white focus:border-[#00ffff] focus:outline-none" placeholder="Contoh: 50000" required>
                                </div>

                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-lg text-xs font-bold text-gray-500 hover:text-white transition-colors uppercase">Batal</button>
                                    <button type="submit" class="bg-[#00ffff]/10 border border-[#00ffff] text-[#00ffff] hover:bg-[#00ffff] hover:text-black font-black px-8 py-2 rounded-lg text-xs uppercase transition-all shadow-[0_0_15px_rgba(0,255,255,0.1)] hover:shadow-[0_0_20px_rgba(0,255,255,0.3)]">SIMPAN</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('user-search');
        const resultsBox = document.getElementById('search-results');
        const statsContainer = document.getElementById('edit-form-container');
        
        // Form Elements
        const elTotalVideos = document.getElementById('stat-total-videos');
        const elActiveViewers = document.getElementById('stat-active-viewers');
        const elTotalViews = document.getElementById('stat-total-views');
        const elDailyEarnings = document.getElementById('stat-daily-earnings');
        const elBalance = document.getElementById('stat-balance');
        const elWeekEarnings = document.getElementById('stat-week-earnings');
        const elMonthEarnings = document.getElementById('stat-month-earnings');
        const elLifetimeEarnings = document.getElementById('stat-lifetime-earnings');
        const elModalUserId = document.getElementById('modal-user-id');
        
        searchInput.addEventListener('input', function() {
            const q = this.value;
            if (q.length < 1) {
                resultsBox.classList.add('hidden');
                return;
            }

            fetch(`{{ route('admin.statistics.search') }}?q=${q}`)
                .then(res => res.json())
                .then(users => {
                    resultsBox.innerHTML = '';
                    if (users.length === 0) {
                        resultsBox.innerHTML = '<div class="p-4 text-xs text-gray-600 text-center">Pengguna tidak ditemukan</div>';
                    } else {
                        users.forEach(user => {
                            const div = document.createElement('div');
                            div.className = 'p-4 hover:bg-[#111] cursor-pointer border-b border-[#222] last:border-0 transition-colors flex items-center gap-3';
                            div.innerHTML = `
                                <img src="${user.avatar_url}" class="w-8 h-8 rounded-full bg-[#050505] object-cover border border-[#222]">
                                <div>
                                    <div class="font-bold text-gray-200 text-sm">${user.username}</div>
                                    <div class="text-[10px] text-gray-500">${user.email}</div>
                                </div>
                            `;
                            div.onclick = () => selectUser(user);
                            resultsBox.appendChild(div);
                        });
                    }
                    resultsBox.classList.remove('hidden');
                });
        });

        function selectUser(user) {
            searchInput.value = user.username;
            resultsBox.classList.add('hidden');
            
            document.getElementById('selected-user-name').innerText = `Mengedit Statistik: ${user.name || user.username}`;
            elModalUserId.value = user.id;

            // Populate Readonly Stats
            elTotalVideos.value = new Intl.NumberFormat('id-ID').format(user.total_videos);
            elActiveViewers.value = new Intl.NumberFormat('id-ID').format(user.active_viewers);
            elTotalViews.value = new Intl.NumberFormat('id-ID').format(user.total_views);
            
            elDailyEarnings.value = "Rp " + new Intl.NumberFormat('id-ID').format(user.daily_earnings);
            elBalance.value = "Rp " + new Intl.NumberFormat('id-ID').format(user.balance);
            elWeekEarnings.value = "Rp " + new Intl.NumberFormat('id-ID').format(user.week_earnings);
            elMonthEarnings.value = "Rp " + new Intl.NumberFormat('id-ID').format(user.month_earnings);
            elLifetimeEarnings.value = "Rp " + new Intl.NumberFormat('id-ID').format(user.lifetime_earnings);
            
            statsContainer.classList.remove('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
