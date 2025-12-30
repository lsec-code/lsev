<x-guest-layout>
    <div class="max-w-3xl mx-auto p-6 text-gray-300">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="text-green-500 hover:text-green-400 flex items-center gap-2 font-bold text-sm transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="bg-black/80 border border-gray-800 rounded-xl p-8 shadow-2xl backdrop-blur-sm text-center">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-cyan-400 mb-2">Hubungi Kami</h1>
                <p class="text-gray-400 text-sm">Punya pertanyaan atau butuh bantuan? Tim kami siap membantu Anda.</p>
            </div>

            <!-- Contact Cards -->
            <div class="space-y-4 mb-8">
                <!-- Admin Card -->
                <div class="border border-green-600/50 bg-green-900/10 rounded-lg p-5 hover:bg-green-900/20 transition-colors">
                    <h3 class="text-green-500 font-bold mb-1">Admin</h3>
                    <div class="flex items-center justify-center gap-2 text-white font-bold text-lg mb-3">
                        <i class="fa-brands fa-whatsapp text-green-500"></i> 08999899922
                    </div>
                    <a href="https://wa.me/628999899922" target="_blank" class="inline-block bg-green-600 hover:bg-green-500 text-white text-xs font-bold py-2 px-6 rounded-full transition-colors">
                        Chat Now
                    </a>
                </div>

                <!-- Staff Admin Card -->
                <div class="border border-green-600/50 bg-green-900/10 rounded-lg p-5 hover:bg-green-900/20 transition-colors">
                    <h3 class="text-green-500 font-bold mb-1">Staff Admin</h3>
                    <div class="flex items-center justify-center gap-2 text-white font-bold text-lg mb-3">
                        <i class="fa-brands fa-whatsapp text-green-500"></i> 08388853885
                    </div>
                    <a href="https://wa.me/628388853885" target="_blank" class="inline-block bg-green-600 hover:bg-green-500 text-white text-xs font-bold py-2 px-6 rounded-full transition-colors">
                        Chat Now
                    </a>
                </div>
            </div>

            <!-- Info Section -->
            <div class="mb-8 space-y-2 text-sm text-gray-400">
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-regular fa-clock text-cyan-400"></i> Senin - Sabtu: 09:00 - 18:00 WIB
                </div>
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-location-dot text-cyan-400"></i> Surabaya, Jawa Timur
                </div>
            </div>

            <div class="border-t border-gray-800 my-6"></div>

            <p class="text-xs text-gray-500 mb-6">
                Format pengaduan atau kerja sama dapat dikirimkan melalui nomor WhatsApp di atas.
            </p>

            <!-- Security Note -->
            <div class="text-left bg-gray-900/50 border-l-4 border-cyan-400 p-4 rounded text-xs text-gray-400">
                <strong class="text-cyan-400 block mb-1">Catatan Keamanan:</strong>
                Kami tidak pernah meminta password akun Anda. Jangan pernah memberikan informasi rahasia kepada pihak manapun yang mengaku atas nama Cloud Host.
            </div>
        </div>
    </div>
</x-guest-layout>
