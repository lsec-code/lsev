<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
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
        <div class="bg-[#0a0a0a]/90 backdrop-blur-md border-2 border-red-500/50 rounded-xl shadow-[0_0_60px_rgba(239,68,68,0.3)] p-8 md:p-12 text-center relative overflow-hidden">
            
            <!-- Neon Glow Effect -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-red-500 to-transparent opacity-50"></div>

            <!-- Icon -->
            <div class="inline-flex items-center justify-center w-24 h-24 bg-red-900/20 border-2 border-red-500/50 rounded-full mb-8 animate-pulse">
                <i class="fa-solid fa-server text-5xl text-red-500"></i>
            </div>
            
            <h1 class="text-6xl font-black text-red-500 tracking-tighter mb-2">
                500
            </h1>
            
            <h2 class="text-xl md:text-2xl font-bold text-white uppercase tracking-wider mb-6">
                INTERNAL SERVER ERROR
            </h2>
            
            <div class="bg-red-900/10 border border-red-500/30 rounded-lg p-6 mb-8">
                <p class="text-red-400 font-medium">
                    <i class="fa-solid fa-bug mr-2"></i>
                    TERJADI KESALAHAN SISTEM
                </p>
                <p class="text-sm text-gray-400 mt-2 leading-relaxed">
                    Server kami sedang mengalami gangguan sementara. Silakan coba beberapa saat lagi.
                </p>
            </div>

            <!-- Countdown & Auto Redirect -->
            <div class="mt-8 bg-red-900/10 rounded-lg p-4 max-w-xs mx-auto">
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Otomatis kembali dalam</p>
                <div class="flex items-end justify-center gap-1 text-red-500">
                    <span id="countdown" class="text-3xl font-black">5</span>
                    <span class="text-sm font-bold mb-1">detik</span>
                </div>
            </div>

            <!-- Action Button -->
            <a href="{{ url('/') }}" class="inline-flex items-center mt-6 px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-all hover:scale-105 hover:shadow-[0_0_20px_rgba(220,38,38,0.6)] group">
                <i class="fa-solid fa-house mr-3 group-hover:-translate-x-1 transition-transform"></i>
                KEMBALI KE AMAN
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
                window.location.href = "{{ url('/') }}";
            }
        }, 1000);
    </script>
</body>
</html>
