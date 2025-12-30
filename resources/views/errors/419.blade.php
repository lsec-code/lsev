<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Page Expired</title>
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
    <div class="max-w-xl w-full">
        <!-- Warning Card -->
        <div class="bg-[#0a0a0a]/90 backdrop-blur-md border-2 border-yellow-500/50 rounded-xl shadow-[0_0_60px_rgba(234,179,8,0.3)] p-8 md:p-12 text-center relative overflow-hidden">
            
            <!-- Neon Glow Effect -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-yellow-500 to-transparent opacity-50"></div>

            <!-- Icon -->
            <div class="inline-flex items-center justify-center w-24 h-24 bg-yellow-900/20 border-2 border-yellow-500/50 rounded-full mb-8 animate-pulse">
                <i class="fa-solid fa-hourglass-end text-5xl text-yellow-500"></i>
            </div>
            
            <h1 class="text-6xl font-black text-yellow-500 tracking-tighter mb-2">
                419
            </h1>
            
            <h2 class="text-xl md:text-2xl font-bold text-white uppercase tracking-wider mb-6">
                SESI TELAH BERAKHIR
            </h2>
            
            <div class="bg-yellow-900/10 border border-yellow-500/30 rounded-lg p-6 mb-8">
                <p class="text-yellow-400 font-medium">
                    <i class="fa-solid fa-rotate-left mr-2"></i>
                    HALAMAN KADALUARSA
                </p>
                <p class="text-sm text-gray-400 mt-2 leading-relaxed">
                    Token keamanan halaman ini sudah tidak valid karena terlalu lama tidak ada aktivitas.
                </p>
            </div>

            <!-- Countdown & Auto Redirect -->
            <div class="mt-8 bg-yellow-900/10 rounded-lg p-4 max-w-xs mx-auto">
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Refresh otomatis dalam</p>
                <div class="flex items-end justify-center gap-1 text-yellow-500">
                    <span id="countdown" class="text-3xl font-black">5</span>
                    <span class="text-sm font-bold mb-1">detik</span>
                </div>
            </div>

            <!-- Action Button -->
            <a href="{{ url('/') }}" class="inline-flex items-center mt-6 px-8 py-4 bg-yellow-600 hover:bg-yellow-700 text-white font-bold rounded-lg transition-all hover:scale-105 hover:shadow-[0_0_20px_rgba(234,179,8,0.6)] group">
                <i class="fa-solid fa-rotate mr-3 group-hover:rotate-180 transition-transform duration-500"></i>
                REFRESH SEKARANG
            </a>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-900/50">
                <p class="text-[10px] text-gray-700 uppercase tracking-widest">
                    Security System Watchdog
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto Redirect Script
        let seconds = 5;
        const countdownEl = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.reload(); 
                // For 419, usually we want to RELOAD/BACK, or go Home. 
                // User asked "kembali di halaman awal". So homepage. 
                // But 419 often happens on forms.
                // Let's stick to user request: "kembali di halaman awal" (Home).
                window.location.href = "{{ url('/') }}";
            }
        }, 1000);
    </script>
</body>
</html>
