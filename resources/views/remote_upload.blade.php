<x-app-layout>
@php
    $isEnabled = \App\Models\SiteSetting::where('setting_key', 'remote_upload_enabled')->value('setting_value') !== 'false';
@endphp
    <div class="max-w-7xl mx-auto px-6 space-y-8 min-h-screen text-white">
        
        <!-- HEADER -->
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-cloud-arrow-down text-[#00ffff]"></i> Remote Upload
            </h1>
            <p class="text-gray-400 text-sm font-medium">
                Unduh video langsung dari URL eksternal ke penyimpanan Anda.
            </p>
        </div>

        <!-- MAIN CARD -->
        <div class="bg-[#111]/60 backdrop-blur-xl border border-[#222] rounded-2xl p-8 relative overflow-hidden group">
            <!-- Background Glow -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-[#00ffff]/5 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <!-- WARNING: FEATURE DISABLED -->
            @if(!$isEnabled)
            <div class="mb-6 bg-red-500/10 border border-red-500/50 rounded-xl p-4 flex items-center gap-4 shadow-[0_0_20px_rgba(220,38,38,0.2)]">
                <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-ban text-red-500 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-red-500 font-bold text-sm uppercase tracking-wider">FITUR DINONAKTIFKAN</h4>
                    <p class="text-gray-400 text-xs mt-1">
                        Mohon maaf, fitur Remote Upload saat ini sedang tidak tersedia. Silakan hubungi administrator untuk informasi lebih lanjut.
                    </p>
                </div>
            </div>
            @endif

            <form action="{{ route('remote_upload.store') }}" method="POST" class="relative z-10 space-y-6 {{ !$isEnabled ? 'opacity-50 pointer-events-none' : '' }}" x-data="{ isLoading: false }" @submit="isLoading = true">
                @csrf
                
                <!-- URL INPUT -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-widest ml-1">URL Video</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-link text-gray-500 group-focus-within:text-[#00ffff] transition-colors"></i>
                        </div>
                        <input type="url" name="url" placeholder="https://example.com/video.mp4" 
                            class="w-full bg-black border border-[#222] text-white rounded-xl py-4 pl-12 pr-4 focus:outline-none focus:border-[#00ffff] focus:shadow-[0_0_20px_rgba(0,255,255,0.15)] transition-all font-mono text-sm placeholder-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                            required {{ !$isEnabled ? 'disabled' : '' }}>
                    </div>
                    <p class="text-[10px] text-gray-500 ml-1">Pastikan URL berakhiran format video (.mp4, .mkv) dan dapat diakses publik.</p>
                </div>

                <!-- FOLDER SELECTION (Optional) -->
                @php
                    $folders = \App\Models\Folder::where('user_id', auth()->id())->get();
                @endphp
                @if($folders->count() > 0)
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-widest ml-1">Simpan ke Folder (Opsional)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-folder text-gray-500 group-focus-within:text-[#FFC107] transition-colors"></i>
                        </div>
                        <select name="folder_id" class="w-full bg-black border border-[#222] text-gray-300 rounded-xl py-4 pl-12 pr-4 focus:outline-none focus:border-[#FFC107] focus:shadow-[0_0_20px_rgba(255,193,7,0.15)] transition-all text-sm appearance-none cursor-pointer disabled:opacity-50" {{ !$isEnabled ? 'disabled' : '' }}>
                            <option value="">Root (Tanpa Folder)</option>
                            @foreach($folders as $folder)
                                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-gray-600"></i>
                        </div>
                    </div>
                </div>
                @endif

                <!-- SUBMIT BUTTON -->
                <div class="pt-4">
                    <button type="submit" class="w-full group relative overflow-hidden rounded-xl bg-[#00ffff]/10 border border-[#00ffff] text-[#00ffff] font-black uppercase tracking-wider py-4 hover:bg-[#00ffff] hover:text-black transition-all duration-300 shadow-[0_0_20px_rgba(0,255,255,0.1)] hover:shadow-[0_0_40px_rgba(0,255,255,0.4)] flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="isLoading" {{ !$isEnabled ? 'disabled' : '' }}>
                        
                        <!-- Glow Overlay -->
                        <div class="absolute inset-0 bg-[#00ffff]/20 blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <!-- Normal State -->
                        <span x-show="!isLoading" class="relative z-10 flex items-center gap-3">
                            <i class="fa-solid fa-cloud-arrow-down text-lg"></i> MULAI UPLOAD
                        </span>
                        
                        <!-- Loading State -->
                        <span x-show="isLoading" style="display: none;" class="relative z-10 flex items-center gap-3">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> SEDANG MENGUNDUH...
                        </span>
                    
                    </button>
                    <p class="text-center text-[10px] text-gray-500 mt-4" x-show="!isLoading">
                        <i class="fa-solid fa-triangle-exclamation text-yellow-500 mr-1"></i>
                        Proses mungkin memakan waktu tergantung ukuran file dan kecepatan server sumber.
                    </p>
                    <p class="text-center text-[10px] text-[#00ffff] mt-4 font-bold animate-pulse" x-show="isLoading" style="display: none;">
                        Mohon tidak menutup halaman ini hingga proses selesai.
                    </p>
                </div>

            </form>
        </div>

        <!-- INFO CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl flex flex-col items-center text-center gap-3 hover:border-[#00ffff]/30 transition-colors">
                <div class="w-12 h-12 rounded-full bg-[#00ffff]/10 flex items-center justify-center text-[#00ffff] mb-2">
                    <i class="fa-solid fa-bolt text-xl"></i>
                </div>
                <h3 class="text-white font-bold text-sm">Cepat & Stabil</h3>
                <p class="text-gray-500 text-xs leading-relaxed">Transfer server-to-server tanpa memakan kuota internet lokal Anda.</p>
            </div>
            <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl flex flex-col items-center text-center gap-3 hover:border-[#00ffff]/30 transition-colors">
                 <div class="w-12 h-12 rounded-full bg-[#00ffff]/10 flex items-center justify-center text-[#00ffff] mb-2">
                    <i class="fa-solid fa-shield-halved text-xl"></i>
                </div>
                <h3 class="text-white font-bold text-sm">Aman & Privat</h3>
                <p class="text-gray-500 text-xs leading-relaxed">File Anda diproses secara terenkripsi dan langsung disimpan ke akun Anda.</p>
            </div>
             <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl flex flex-col items-center text-center gap-3 hover:border-[#00ffff]/30 transition-colors">
                 <div class="w-12 h-12 rounded-full bg-[#00ffff]/10 flex items-center justify-center text-[#00ffff] mb-2">
                    <i class="fa-solid fa-folder-tree text-xl"></i>
                </div>
                <h3 class="text-white font-bold text-sm">Terorganisir</h3>
                <p class="text-gray-500 text-xs leading-relaxed">Pilih folder tujuan langsung saat upload agar koleksi tetap rapi.</p>
            </div>
        </div>

    </div>
</x-app-layout>
