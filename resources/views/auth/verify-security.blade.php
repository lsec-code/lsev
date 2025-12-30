<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-black p-4">
        <div class="w-full max-w-md">
            <!-- Security Verification Modal -->
            <div class="bg-[#0a0a0a]/90 backdrop-blur-md border border-[#00ffff]/30 rounded-xl shadow-[0_0_50px_rgba(0,255,255,0.15)] p-8">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-[#00ffff]/10 rounded-full mb-4">
                        <i class="fa-solid fa-shield-halved text-3xl text-[#00ffff]"></i>
                    </div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wider">Verifikasi Keamanan</h2>
                    <p class="text-xs text-gray-400 mt-2 uppercase tracking-widest">Diperlukan untuk melanjutkan</p>
                </div>

                @if($cooldown)
                    <!-- Cooldown State -->
                    <div class="bg-red-900/20 border border-red-500/30 rounded-lg p-6 text-center">
                        <i class="fa-solid fa-clock text-4xl text-red-500 mb-4"></i>
                        <h3 class="text-lg font-bold text-red-500 mb-2">Akun Dikunci</h3>
                        <p class="text-xs text-gray-400 mb-4">Terlalu banyak percobaan gagal</p>
                        <div class="text-2xl font-black text-white mb-2" id="cooldown-timer">
                            <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
                        </div>
                        <p class="text-[9px] text-gray-500 uppercase tracking-widest">Waktu tersisa</p>
                    </div>

                    <script>
                        let remainingSeconds = {{ $remainingSeconds }};
                        
                        function updateTimer() {
                            const hours = Math.floor(remainingSeconds / 3600);
                            const minutes = Math.floor((remainingSeconds % 3600) / 60);
                            const seconds = remainingSeconds % 60;
                            
                            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
                            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
                            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
                            
                            if (remainingSeconds > 0) {
                                remainingSeconds--;
                                setTimeout(updateTimer, 1000);
                            } else {
                                location.reload();
                            }
                        }
                        
                        updateTimer();
                    </script>
                @else
                    <!-- Verification Form -->
                    <div class="@if(isset($mode) && $mode == 'reset_password') bg-blue-900/20 border border-blue-500/50 @else bg-red-900/20 border border-red-500/50 @endif rounded-lg p-4 mb-6 shadow-[0_0_20px_rgba(239,68,68,0.2)]">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid @if(isset($mode) && $mode == 'reset_password') fa-shield-halved text-blue-500 @else fa-triangle-exclamation text-red-500 @endif text-xl mt-1"></i>
                            <div class="text-xs text-gray-300 leading-relaxed">
                                <p class="font-black @if(isset($mode) && $mode == 'reset_password') text-blue-500 @else text-red-500 @endif mb-1 uppercase tracking-wider">
                                    @if(isset($mode) && $mode == 'reset_password')
                                        Verifikasi Reset Password
                                    @else
                                        Aktivitas Mencurigakan Terdeteksi
                                    @endif
                                </p>
                                <p class="text-gray-400">
                                    @if(isset($mode) && $mode == 'reset_password')
                                        Masukkan kode keamanan untuk mereset password Anda.
                                    @else
                                        Masukkan kode keamanan untuk melanjutkan.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ isset($mode) && $mode == 'reset_password' ? route('password.security.store') : route('security.verify.submit') }}">
                        @csrf

                        @if(isset($mode) && $mode == 'reset_password')
                            <input type="hidden" name="email" value="{{ $email }}">
                        @endif

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Kode Keamanan</label>
                            <input 
                                type="text" 
                                name="{{ isset($mode) && $mode == 'reset_password' ? 'security_answer' : 'security_code' }}" 
                                class="w-full bg-[#111] border border-[#00ffff]/30 rounded-lg px-4 py-3 text-sm text-white focus:border-[#00ffff] focus:shadow-[0_0_15px_rgba(0,255,255,0.2)] focus:outline-none transition-all"
                                placeholder="Masukkan kode keamanan Anda"
                                required
                                autofocus
                            >
                            @error(isset($mode) && $mode == 'reset_password' ? 'security_answer' : 'security_code')
                                <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        @if(isset($mode) && $mode == 'reset_password')
                            <!-- New Password Fields -->
                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Password Baru</label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    class="w-full bg-[#111] border border-[#00ffff]/30 rounded-lg px-4 py-3 text-sm text-white focus:border-[#00ffff] focus:shadow-[0_0_15px_rgba(0,255,255,0.2)] focus:outline-none transition-all"
                                    placeholder="Minimal 8 karakter"
                                    required
                                >
                                @error('password')
                                    <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Konfirmasi Password</label>
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    class="w-full bg-[#111] border border-[#00ffff]/30 rounded-lg px-4 py-3 text-sm text-white focus:border-[#00ffff] focus:shadow-[0_0_15px_rgba(0,255,255,0.2)] focus:outline-none transition-all"
                                    placeholder="Ulangi password baru"
                                    required
                                >
                            </div>
                        @endif

                        @if($attempts > 0 && (!isset($mode) || $mode != 'reset_password'))
                            <div class="bg-red-900/20 border border-red-500/30 rounded-lg p-3 mb-6">
                                <p class="text-xs text-red-400 text-center font-bold">
                                    <i class="fa-solid fa-exclamation-circle mr-1"></i>
                                    Percobaan gagal: {{ $attempts }} kali
                                    <br>
                                    <span class="text-[9px] text-gray-500">Sisa {{ 3 - ($attempts % 3) }} percobaan sebelum cooldown</span>
                                </p>
                            </div>
                        @endif

                        <button 
                            type="submit" 
                            class="w-full bg-[#00ffff] text-black font-black py-3 rounded-lg text-sm uppercase tracking-wider hover:bg-white transition-all shadow-[0_0_20px_rgba(0,255,255,0.3)] hover:shadow-[0_0_30px_rgba(0,255,255,0.5)]"
                        >
                            {{ isset($mode) && $mode == 'reset_password' ? 'Reset Password' : 'Verifikasi Sekarang' }}
                        </button>
                    </form>

                    <!-- Cancel/Logout Button -->
                    @if(isset($mode) && $mode == 'reset_password')
                        <!-- Back to Login (Guest) -->
                        <div class="mt-4">
                            <a href="{{ route('login') }}" class="block w-full text-center bg-gray-800 border border-gray-600 text-gray-300 font-bold py-3 rounded-lg text-xs uppercase tracking-wider hover:bg-gray-700 hover:text-white transition-all">
                                <i class="fa-solid fa-arrow-left mr-2"></i>
                                Kembali ke Login
                            </a>
                        </div>
                    @else
                        <!-- Logout (Auth User) -->
                        <form method="POST" action="{{ route('security.verify.cancel') }}" class="mt-4">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full bg-red-900/20 border border-red-500/50 text-red-500 font-bold py-3 rounded-lg text-xs uppercase tracking-wider hover:bg-red-900/30 transition-all"
                            >
                                <i class="fa-solid fa-right-from-bracket mr-2"></i>
                                Batalkan & Logout
                            </button>
                        </form>
                    @endif

                    <div class="mt-6 text-center">
                        <p class="text-[9px] text-gray-600 uppercase tracking-widest">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Kode keamanan adalah jawaban yang Anda set saat registrasi
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>
