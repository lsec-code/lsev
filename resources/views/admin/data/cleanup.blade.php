<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pb-6 border-b border-[#222]">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#00ffff]/10 flex items-center justify-center border border-[#00ffff]/20">
                    <i class="fa-solid fa-database text-[#00ffff] text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Data Cleanup & Management</h1>
                    <p class="text-gray-400 text-sm">Hapus data sampah, kelola chat, dan penghapusan akun.</p>
                </div>
            </div>
            <div class="ml-auto">
                <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4 flex items-center gap-3">
                <i class="fa-solid fa-check-circle text-green-500"></i>
                <p class="text-green-500 font-bold text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4 flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                <p class="text-red-500 font-bold text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Global Chat Management -->
        <div class="bg-[#0a0a0a] border border-[#333] rounded-xl p-8 shadow-[0_0_50px_rgba(0,0,0,0.5)]">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-comments text-blue-500"></i> Global Chat Management
            </h3>
            
            <div class="space-y-6">
                <!-- Clear History -->
                <div class="flex items-center justify-between bg-[#111] p-4 rounded-lg border border-[#333]" x-data="{ showModal: false }">
                    <div>
                        <h4 class="text-white font-bold">Hapus Riwayat Chat</h4>
                        <p class="text-gray-500 text-xs">Menghapus semua pesan dalam database chat global secara permanen.</p>
                    </div>
                    
                    <button @click="showModal = true" class="bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/30 font-bold py-2 px-4 rounded-lg text-sm transition">
                        <i class="fa-solid fa-trash mr-2"></i> Hapus Semua Chat
                    </button>

                    <!-- Modal Confirmation -->
                    <div x-show="showModal" style="display: none;" 
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        
                        <div class="bg-[#1a1a1a] border border-[#333] rounded-xl p-6 w-full max-w-md shadow-2xl transform transition-all"
                            @click.away="showModal = false"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-90">
                            
                            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-triangle-exclamation text-red-500"></i> Konfirmasi Penghapusan
                            </h3>
                            <p class="text-gray-400 text-sm mb-6">Apakah Anda yakin ingin menghapus <strong>SELURUH</strong> riwayat chat global? Tindakan ini tidak dapat dibatalkan.</p>
                            
                            <div class="flex items-center justify-end gap-3">
                                <button @click="showModal = false" class="text-gray-400 hover:text-white font-bold text-sm px-4 py-2 transition">
                                    Batal
                                </button>
                                <form action="{{ route('admin.chat.clear') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition">
                                        Ya, Hapus Semua
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Account Deletion -->
        <div class="bg-[#0a0a0a] border border-[#333] rounded-xl p-8 shadow-[0_0_50px_rgba(0,0,0,0.5)]">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-user-slash text-red-500"></i> Hapus Akun User (Permanen)
            </h3>
            
            <div class="bg-red-900/10 border border-red-500/20 p-4 rounded-lg mb-6">
                <p class="text-red-400 text-sm font-bold flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                    Warning: Menghapus akun akan menghapus juga semua video, saldo, komentar, dan data terkait user tersebut secara permanen.
                </p>
            </div>

            <form action="{{ route('admin.users.delete') }}" method="POST" class="space-y-4" x-data="userSearch()">
                @csrf
                <div class="relative">
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Cari User (Username / Email / ID)</label>
                    
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" 
                            x-model="query" 
                            @input.debounce.300ms="search" 
                            @keydown.escape="showResults = false"
                            class="w-full bg-[#111] border border-[#333] text-white rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:border-red-500 transition placeholder-gray-600" 
                            placeholder="Ketik minimal 1 huruf..." 
                            required>
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-gray-500"></i>
                        
                        <!-- Hidden Input for Form Submission -->
                        <input type="hidden" name="user_identifier" :value="selectedUser">
                        
                        <!-- Loading Indicator -->
                        <div x-show="loading" class="absolute right-3 top-3.5">
                            <i class="fa-solid fa-circle-notch fa-spin text-red-500"></i>
                        </div>
                    </div>

                    <!-- Dropdown Results -->
                    <div x-show="showResults && users.length > 0" 
                        @click.away="showResults = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="absolute z-50 w-full mt-2 bg-[#121212] border border-[#333] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.5)] overflow-hidden">
                        
                        <div class="max-h-60 overflow-y-auto custom-scrollbar p-1">
                            <template x-for="user in users" :key="user.id">
                                <div @click="selectUser(user)" 
                                    class="p-3 hover:bg-[#1a1a1a] cursor-pointer rounded-lg mb-1 last:mb-0 transition-all group flex items-center justify-between gap-4 border border-transparent hover:border-[#333]">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <!-- Avatar -->
                                        <img :src="user.avatar_url" class="w-10 h-10 rounded-full border border-[#333] object-cover bg-black shrink-0">
                                        
                                        <div class="min-w-0">
                                            <p class="text-[#FFC107] font-bold text-sm truncate" x-text="user.username"></p>
                                            <p class="text-gray-500 text-xs truncate font-mono" x-text="user.email"></p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-mono bg-[#000] border border-[#222] text-gray-400 px-2 py-1 rounded shrink-0" x-text="'ID: ' + user.id"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- No Results -->
                    <div x-show="showResults && users.length === 0 && query.length > 0 && !loading" 
                        class="absolute z-50 w-full mt-2 bg-[#050505] border border-[#222] rounded-xl shadow-xl p-6 text-center"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="w-12 h-12 bg-gray-900/50 rounded-full flex items-center justify-center mx-auto mb-3 border border-dashed border-[#333]">
                            <i class="fa-solid fa-user-slash text-gray-600"></i>
                        </div>
                        <p class="text-gray-400 text-sm font-medium">User tidak ditemukan</p>
                        <p class="text-gray-600 text-xs mt-1">Coba kata kunci lain</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Konfirmasi Penghapusan</label>
                    <input type="text" name="confirm_text" class="w-full bg-[#111] border border-[#333] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-red-500 transition placeholder-gray-600" placeholder="Ketik 'HAPUS' untuk konfirmasi" required>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg shadow-red-900/30 transition transform hover:-translate-y-1">
                        <i class="fa-solid fa-skull mr-2"></i> Hapus Akun Permanen
                    </button>
                </div>
            </form>

            <script>
                function userSearch() {
                    return {
                        query: '',
                        selectedUser: '',
                        users: [],
                        showResults: false,
                        loading: false,
                        
                        async search() {
                            if (this.query.length < 1) {
                                this.users = [];
                                this.showResults = false;
                                return;
                            }
                            
                            this.loading = true;
                            // Reset selectedUser if query changes manually to avoid deleting wrong user if only part of name matches
                            
                            try {
                                const response = await fetch(`{{ route('admin.statistics.search') }}?q=${this.query}`);
                                const data = await response.json();
                                this.users = data;
                                this.showResults = true;
                            } catch (e) {
                                console.error('Search error:', e);
                            } finally {
                                this.loading = false;
                            }
                        },

                        selectUser(user) {
                            this.query = `${user.username} (${user.email})`;
                            this.selectedUser = user.id; // Send ID for sure match
                            this.showResults = false;
                        }
                    }
                }
            </script>
        </div>

        <!-- Reset Database Total -->
        <div class="bg-[#0a0a0a] border border-red-500/30 rounded-xl p-8 shadow-[0_0_50px_rgba(255,0,0,0.2)]" x-data="{ showResetModal: false }">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-skull-crossbones text-red-500"></i> Reset Database Total
            </h3>
            
            <div class="bg-red-900/20 border border-red-500/30 p-6 rounded-lg mb-6">
                <p class="text-red-400 text-sm font-bold flex items-start gap-2 mb-4">
                    <i class="fa-solid fa-radiation mt-1"></i>
                    <span>PERINGATAN EKSTRIM: Fitur ini akan menghapus SELURUH data aplikasi dan me-reset sistem ke kondisi awal!</span>
                </p>
                <ul class="text-red-300 text-xs space-y-2 ml-6">
                    <li>❌ Menghapus <strong>SEMUA user</strong> kecuali admin</li>
                    <li>❌ Menghapus <strong>SEMUA video, komentar, pesan</strong></li>
                    <li>❌ Me-reset <strong>SEMUA statistik admin</strong> (video, pendapatan, saldo) ke 0</li>
                    <li>✅ Mempertahankan akun admin dengan password tetap</li>
                    <li>⚠️ <strong>TIDAK DAPAT DIBATALKAN</strong></li>
                </ul>
            </div>
            
            <div x-data="resetDatabaseScope()">
            <form id="reset-database-form" action="{{ route('admin.data.reset-database') }}" method="POST" class="space-y-4" 
                @submit.prevent="verifyAndShow()">
                @csrf
                
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Password Admin</label>
                    <input type="password" name="admin_password" x-model="password" class="w-full bg-[#111] border border-[#333] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-red-500 transition placeholder-gray-600" placeholder="Masukkan password admin untuk konfirmasi" required>
                </div>
                
                <!-- Text Confirmation Removed by User Request -->
                {{-- 
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Konfirmasi Teks</label>
                    <input type="text" name="confirm_text" class="w-full bg-[#111] border border-[#333] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-red-500 transition placeholder-gray-600 font-mono" placeholder='Ketik "RESET DATABASE" untuk konfirmasi' required>
                    <p class="text-gray-500 text-xs mt-1">Ketik persis: <code class="bg-[#000] px-2 py-1 rounded text-red-400">RESET DATABASE</code></p>
                </div>
                --}}

                <div class="flex items-center gap-3 p-4 bg-[#111] rounded-lg border border-[#333]">
                    <input type="checkbox" id="understand_risk" name="understand_risk" class="w-5 h-5 text-red-600 bg-[#000] border-[#333] rounded focus:ring-red-500" required>
                    <label for="understand_risk" class="text-gray-300 text-sm font-medium cursor-pointer">
                        Saya memahami bahwa tindakan ini akan <strong class="text-red-400">menghapus semua data</strong> dan tidak dapat dibatalkan
                    </label>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" :disabled="verifying" class="bg-red-600/90 hover:bg-red-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg shadow-red-900/50 transition transform hover:-translate-y-1 border border-red-500/50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                        <i x-show="!verifying" class="fa-solid fa-radiation mr-2"></i>
                        <i x-show="verifying" class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                        <span x-text="verifying ? 'Checking...' : 'Reset Database Total'"></span>
                    </button>
                </div>
            </form>

    <!-- Modern Error/Alert Modal -->
    <div x-show="showErrorModal" style="display: none;" 
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        
        <div class="bg-[#1a1a1a] border border-red-500/50 rounded-xl p-8 w-full max-w-md shadow-2xl shadow-red-900/30 transform transition-all"
            @click.away="showErrorModal = false">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-500/30">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Perhatian</h3>
                <p class="text-gray-400 text-sm" x-text="errorMessage"></p>
            </div>
            
            <div class="flex justify-center">
                <button @click="showErrorModal = false" type="button" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2 rounded-lg transition shadow-lg shadow-red-500/20">
                    OK, Saya Mengerti
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Final -->
    <div x-show="showResetModal" style="display: none;" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        
        <div class="bg-[#1a1a1a] border-2 border-red-500/50 rounded-xl p-8 w-full max-w-md shadow-2xl shadow-red-900/30 transform transition-all"
            @click.away="showResetModal = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-red-500/30">
                    <i class="fa-solid fa-skull-crossbones text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Konfirmasi Terakhir</h3>
                <p class="text-gray-400 text-sm">Apakah Anda BENAR-BENAR yakin?</p>
            </div>
            
            <div class="bg-red-900/10 border border-red-500/20 rounded-lg p-4 mb-6">
                <p class="text-red-300 text-sm font-medium text-center">
                    Setelah klik tombol di bawah, <strong>SEMUA data akan terhapus</strong> dan sistem akan direset ke kondisi awal.
                </p>
            </div>
            
            <div class="flex items-center justify-center gap-3">
                <button @click="showResetModal = false" type="button" class="bg-[#222] hover:bg-[#2a2a2a] text-gray-400 hover:text-white font-bold px-6 py-3 rounded-lg transition border border-[#333]">
                    <i class="fa-solid fa-times mr-2"></i> Batal
                </button>
                <button @click="document.getElementById('reset-database-form').submit()" type="button" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-lg shadow-lg transition border border-red-500/50">
                    <i class="fa-solid fa-bomb mr-2"></i> Ya, Reset Semuanya!
                </button>
            </div>
        </div>
    </div>
</div>
</div>
</x-app-layout>

<script>
    function resetDatabaseScope() {
        return {
            password: '',
            verifying: false,
            showResetModal: false, 
            showErrorModal: false, 
            errorMessage: '',
            
            verifyAndShow() {
                if (!this.password) {
                    this.showError('Harap isi password admin terlebih dahulu!');
                    return;
                }
                this.verifying = true;
                
                const verifyUrl = "{{ route('admin.verify.password') }}";
                const token = "{{ csrf_token() }}";

                fetch(verifyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                         'Accept': 'application/json'
                    },
                    body: JSON.stringify({ password: this.password })
                })
                .then(async res => {
                    const contentType = res.headers.get("content-type");
                    if (!res.ok) {
                         const text = await res.text();
                         throw new Error(`Server Error (${res.status}): ${text.substring(0, 50)}...`);
                    }
                    if (!contentType || !contentType.includes("application/json")) {
                        throw new Error("Respon server bukan JSON valid!");
                    }
                    return res.json();
                })
                .then(data => {
                    this.verifying = false;
                    if (data.valid) {
                        this.showResetModal = true; 
                    } else {
                        this.showError(data.message || 'Password Admin Salah!');
                    }
                })
                .catch(err => {
                    this.verifying = false;
                    console.error('Verify error:', err);
                    this.showError('Ups! ' + (err.message || 'Terjadi kesalahan sistem saat verifikasi.'));
                });
            },
            
            showError(msg) {
                this.errorMessage = msg;
                this.showErrorModal = true;
            }
        }
    }
</script>
