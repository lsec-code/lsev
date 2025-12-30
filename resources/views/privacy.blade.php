<x-guest-layout>
    <div class="max-w-3xl mx-auto p-6 text-gray-300">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="text-cyan-400 hover:text-cyan-300 flex items-center gap-2 font-bold text-sm transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="bg-black/60 border border-gray-800 rounded-xl p-8 shadow-2xl backdrop-blur-sm">
            <!-- Header -->
            <div class="mb-8 border-b border-gray-800 pb-6">
                <h1 class="text-3xl font-bold text-cyan-400 mb-2">Kebijakan Privasi</h1>
                <p class="text-gray-500 text-sm">Terakhir diperbarui: 27 December 2025</p>
            </div>

            <!-- Content -->
            <div class="space-y-6 text-sm leading-relaxed">
                <!-- Section 1 -->
                <section>
                    <h2 class="text-xl font-bold text-cyan-400 mb-2">1. Informasi yang Kami Kumpulkan</h2>
                    <p class="text-gray-300">
                        Kami mengumpulkan informasi yang Anda berikan langsung kepada kami saat mendaftar akun, seperti alamat email, nama pengguna, dan jawaban keamanan.
                    </p>
                </section>

                <!-- Section 2 -->
                <section>
                    <h2 class="text-xl font-bold text-cyan-400 mb-2">2. Penggunaan Informasi</h2>
                    <p class="text-gray-300">
                        Informasi Anda digunakan untuk mengelola akun Anda, memproses transaksi saldo, dan memastikan keamanan layanan kami melalui fitur captcha dan deteksi IP.
                    </p>
                </section>

                <!-- Section 3 -->
                <section>
                    <h2 class="text-xl font-bold text-cyan-400 mb-2">3. Perlindungan Data</h2>
                    <p class="text-gray-300">
                        Kami menggunakan standar keamanan industri termasuk enkripsi password bcrypt dan perlindungan CSRF untuk melindungi data pribadi Anda.
                    </p>
                </section>

                <!-- Section 4 -->
                <section>
                    <h2 class="text-xl font-bold text-cyan-400 mb-2">4. Cookie</h2>
                    <p class="text-gray-300">
                        Kami menggunakan cookie fungsional untuk mengidentifikasi sesi login Anda dan meningkatkan pengalaman pengguna.
                    </p>
                </section>

                <!-- Section 5 -->
                <section>
                    <h2 class="text-xl font-bold text-cyan-400 mb-2">5. Kontak Kami</h2>
                    <p class="text-gray-300">
                        Jika Anda memiliki pertanyaan tentang kebijakan ini, silakan hubungi administrator melalui fitur chat yang tersedia di dashboard.
                    </p>
                </section>
            </div>
        </div>
    </div>
</x-guest-layout>
