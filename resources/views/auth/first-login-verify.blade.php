<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Login Pertama - Cloud Host</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-black min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Verification Card -->
        <div class="bg-[#0a0a0a]/90 backdrop-blur-md border-2 border-cyan-500/50 rounded-xl shadow-[0_0_60px_rgba(6,182,212,0.3)] p-8">
            <!-- Icon -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-cyan-900/30 border-2 border-cyan-500/50 rounded-full mb-4">
                    <i class="fa-solid fa-shield-halved text-4xl text-cyan-500"></i>
                </div>
                
                <h1 class="text-2xl font-black text-cyan-500 uppercase tracking-wider mb-2">
                    Verifikasi Keamanan
                </h1>
                
                <p class="text-xs text-gray-400 uppercase tracking-widest">
                    Login Pertama Kali
                </p>
            </div>

            @if(isset($locked) && $locked)
                <!-- Locked State -->
                <div class="bg-red-900/20 border border-red-500/50 rounded-lg p-6 mb-6">
                    <div class="flex items-start gap-4">
                        <i class="fa-solid fa-lock text-3xl text-red-500 mt-1"></i>
                        <div>
                            <h2 class="text-lg font-black text-red-500 uppercase tracking-wider mb-2">
                                Akun Terkunci
                            </h2>
                            <p class="text-sm text-gray-300 leading-relaxed mb-4">
                                Anda telah salah memasukkan kode keamanan sebanyak 3 kali. Akun Anda dikunci sementara.
                            </p>
                            <div class="bg-black/50 rounded-lg p-4">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Waktu Tersisa:</p>
                                <p id="countdown" class="text-2xl text-red-500 font-black" data-seconds="{{ $remainingSeconds }}">
                                    {{ gmdate('H:i:s', $remainingSeconds) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    let seconds = parseInt(document.getElementById('countdown').dataset.seconds);
                    setInterval(() => {
                        if (seconds > 0) {
                            seconds--;
                            const hours = Math.floor(seconds / 3600);
                            const minutes = Math.floor((seconds % 3600) / 60);
                            const secs = seconds % 60;
                            document.getElementById('countdown').textContent = 
                                String(hours).padStart(2, '0') + ':' + 
                                String(minutes).padStart(2, '0') + ':' + 
                                String(secs).padStart(2, '0');
                        } else {
                            location.reload();
                        }
                    }, 1000);
                </script>
            @else
                <!-- Verification Form -->
                <div class="bg-cyan-900/10 border border-cyan-500/30 rounded-lg p-6 mb-6">
                    <p class="text-sm text-gray-300 leading-relaxed mb-4">
                        <i class="fa-solid fa-info-circle text-cyan-500 mr-2"></i>
                        Untuk keamanan akun Anda, silakan masukkan <strong class="text-cyan-500">Kode Keamanan</strong> yang Anda buat saat registrasi.
                    </p>
                    <p class="text-xs text-gray-500">
                        Ini adalah verifikasi wajib untuk login pertama kali.
                    </p>
                </div>

                <form method="POST" action="{{ route('first-login.verify.submit') }}">
                    @csrf

                    <!-- Security Code Input -->
                    <div class="mb-6">
                        <label for="security_code" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                            Kode Keamanan
                        </label>
                        <input 
                            type="password" 
                            id="security_code" 
                            name="security_code" 
                            class="w-full bg-black/50 border-2 @error('security_code') border-red-500 @else border-cyan-500/50 @enderror rounded-lg px-4 py-3 text-white placeholder-gray-600 focus:border-cyan-500 focus:outline-none transition-all"
                            placeholder="Masukkan kode keamanan Anda"
                            required
                            autofocus
                        >
                        @error('security_code')
                            <p class="mt-2 text-xs text-red-500">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remaining Attempts -->
                    <div class="bg-yellow-900/20 border border-yellow-500/30 rounded-lg p-4 mb-6">
                        <p class="text-xs text-yellow-500">
                            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                            Sisa percobaan: <strong class="text-lg">{{ $remainingAttempts ?? 3 }}</strong> kali
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-black font-black py-3 rounded-lg uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(6,182,212,0.5)] hover:shadow-[0_0_30px_rgba(6,182,212,0.7)]"
                    >
                        <i class="fa-solid fa-check mr-2"></i>
                        Verifikasi
                    </button>
                </form>
            @endif

            <!-- Footer -->
            <div class="mt-6 pt-6 border-t border-gray-800 text-center">
                <p class="text-[9px] text-gray-700 uppercase tracking-widest">
                    Security Verification © {{ date('Y') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
