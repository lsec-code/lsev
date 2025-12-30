<x-app-layout>
<div class="max-w-7xl mx-auto px-4 md:px-6 py-6 md:py-8">
<div class="max-w-4xl mx-auto">
    <div class="bg-[#0a0a0a] border border-[#333] rounded-xl p-5 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-white">
                <i class="fa-solid fa-code text-yellow-500 mr-2"></i>
                Pengaturan Iklan (Ads)
            </h1>
            <a href="{{ route('admin.dashboard') }}" 
                class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs text-center">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                <p class="text-green-500 font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="mb-6 bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation text-yellow-500 text-xl mt-1"></i>
                <div class="text-sm text-yellow-300">
                    <p class="font-bold mb-2">Perhatian:</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-400">
                        <li>Masukkan script iklan HTML/JS lengkap (termasuk tag <code>&lt;script&gt;</code>).</li>
                        <li>Script akan dieksekusi di browser user. Hati-hati terhadap script berbahaya.</li>
                        <li>Pastikan script tidak merusak layout halaman.</li>
                    </ul>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.ads.update') }}" class="space-y-8">
            @csrf

            <!-- Video Watch Page Ads -->
            <div class="bg-[#111] border border-[#333] rounded-lg p-6">
                <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-play-circle text-red-500"></i>
                    Iklan Halaman Video Player
                </h3>
                <p class="text-gray-400 text-sm mb-3">Script ini akan muncul di halaman saat user menonton video. Cocok untuk Pop-under atau Banner di bawah player.</p>
                <textarea name="ad_script_video_watch" rows="6" 
                    class="w-full bg-[#0a0a0a] border border-[#333] rounded-lg px-4 py-3 text-gray-300 font-mono text-sm focus:border-yellow-500 focus:outline-none"
                    placeholder="<!-- Masukkan script iklan di sini -->">{{ $ad_video }}</textarea>
            </div>

            <!-- Auth Page Ads -->
            <div class="bg-[#111] border border-[#333] rounded-lg p-6">
                <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-blue-500"></i>
                    Iklan Halaman Auth (Login/Register)
                </h3>
                <p class="text-gray-400 text-sm mb-3">Script ini akan muncul di halaman Login, Register, dan Lupa Password.</p>
                <textarea name="ad_script_auth" rows="6" 
                    class="w-full bg-[#0a0a0a] border border-[#333] rounded-lg px-4 py-3 text-gray-300 font-mono text-sm focus:border-yellow-500 focus:outline-none"
                    placeholder="<!-- Masukkan script iklan di sini -->">{{ $ad_auth }}</textarea>
            </div>

            <!-- User Videos Page Ads -->
            <div class="bg-[#111] border border-[#333] rounded-lg p-6">
                <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-list text-green-500"></i>
                    Iklan Halaman Video Saya
                </h3>
                <p class="text-gray-400 text-sm mb-3">Script ini akan muncul di halaman dashboard user / list video saya.</p>
                <textarea name="ad_script_user_videos" rows="6" 
                    class="w-full bg-[#0a0a0a] border border-[#333] rounded-lg px-4 py-3 text-gray-300 font-mono text-sm focus:border-yellow-500 focus:outline-none"
                    placeholder="<!-- Masukkan script iklan di sini -->">{{ $ad_user_videos }}</textarea>
            </div>

            <!-- 404 Page Ads -->
            <div class="bg-[#111] border border-[#333] rounded-lg p-6">
                <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-file-circle-xmark text-purple-500"></i>
                    Iklan Halaman Error 404 (Not Found)
                </h3>
                <p class="text-gray-400 text-sm mb-3">Script ini akan muncul di halaman Error 404 saat konten tidak ditemukan.</p>
                <textarea name="ad_script_404" rows="6" 
                    class="w-full bg-[#0a0a0a] border border-[#333] rounded-lg px-4 py-3 text-gray-300 font-mono text-sm focus:border-yellow-500 focus:outline-none"
                    placeholder="<!-- Masukkan script iklan di sini -->">{{ $ad_404 }}</textarea>
            </div>

            <!-- Account Suspended Page Ads -->
            <div class="bg-[#111] border border-[#333] rounded-lg p-6">
                <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-ban text-red-600"></i>
                    Iklan Halaman Akun Ditangguhkan (Suspended)
                </h3>
                <p class="text-gray-400 text-sm mb-3">Script ini akan muncul di halaman peringatan saat akun user telah di-suspend.</p>
                <textarea name="ad_script_suspended" rows="6" 
                    class="w-full bg-[#0a0a0a] border border-[#333] rounded-lg px-4 py-3 text-gray-300 font-mono text-sm focus:border-yellow-500 focus:outline-none"
                    placeholder="<!-- Masukkan script iklan di sini -->">{{ $ad_suspended }}</textarea>
            </div>

            <!-- Security Alert Page Ads -->
            <div class="bg-[#111] border border-[#333] rounded-lg p-6">
                <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-shield-virus text-red-500"></i>
                    Iklan Halaman Peringatan Keamanan (Security Alert)
                </h3>
                <p class="text-gray-400 text-sm mb-3">Script ini akan muncul di halaman merah "Access Denied" saat pelanggaran terdeteksi.</p>
                <textarea name="ad_script_security" rows="6" 
                    class="w-full bg-[#0a0a0a] border border-[#333] rounded-lg px-4 py-3 text-gray-300 font-mono text-sm focus:border-yellow-500 focus:outline-none"
                    placeholder="<!-- Masukkan script iklan di sini -->">{{ $ad_security }}</textarea>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" 
                    class="w-full bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white font-bold py-3 px-6 rounded-lg transition">
                    <i class="fa-solid fa-save mr-2"></i>
                    Simpan Semua Pengaturan Iklan
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</x-app-layout>
