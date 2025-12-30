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
                <h1 class="text-3xl font-bold text-cyan-400 mb-2">Syarat dan Ketentuan</h1>
                <p class="text-gray-500 text-sm">Terakhir diperbarui: 27 December 2025</p>
            </div>

            <!-- Content -->
            <div class="space-y-6 text-sm leading-relaxed">
                <!-- Section 1 -->
                <section>
                    <h2 class="text-xl font-bold text-cyan-400 mb-2">1. Penerimaan Ketentuan</h2>
                    <p class="text-gray-300">
                        Dengan mengakses atau menggunakan Cloud Host, Anda setuju untuk terikat oleh Syarat dan Ketentuan ini.
                    </p>
                </section>

                <!-- Section 2 -->
                <section>
                    <h2 class="text-xl font-bold text-cyan-400 mb-2">2. Akun Pengguna</h2>
                    <p class="text-gray-300">
                        Anda bertanggung jawab untuk menjaga kerahasiaan akun dan password Anda. Penggunaan bot atau script otomatis untuk memanipulasi saldo dilarang keras dan akan mengakibatkan pemblokiran akun permanen.
                    </p>
                </section>

                <!-- Section 3 -->
                <section>
                    <h2 class="text-xl font-bold text-cyan-400 mb-2">3. Monetisasi</h2>
                    <p class="text-gray-300">
                        Sistem pendapatan didasarkan pada interaksi video yang valid. Kami berhak meninjau setiap transaksi saldo sebelum penarikan (withdraw) disetujui.
                    </p>
                </section>

                <!-- Section 4 -->
                <section>
                    <h2 class="text-xl font-bold text-cyan-400 mb-2">4. Batasan Tanggung Jawab</h2>
                    <p class="text-gray-300">
                        Cloud Host tidak bertanggung jawab atas kerugian finansial atau teknis yang timbul dari penyalahgunaan layanan oleh pihak ketiga.
                    </p>
                </section>

                <!-- Section 5 -->
                <section>
                    <h2 class="text-xl font-bold text-cyan-400 mb-2">5. Perubahan Layanan</h2>
                    <p class="text-gray-300">
                        Kami berhak mengubah atau menghentikan fitur layanan kapan saja tanpa pemberitahuan sebelumnya.
                    </p>
                </section>
            </div>
        </div>
    </div>
</x-guest-layout>
