<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NO ACCESS - Security Violation</title>
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
    <div class="max-w-2xl w-full">
        <!-- Warning Card -->
        <div class="bg-[#0a0a0a]/90 backdrop-blur-md border-2 border-red-500/50 rounded-xl shadow-[0_0_60px_rgba(239,68,68,0.3)] p-8 md:p-12">
            <!-- Lock Icon -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-red-900/30 border-2 border-red-500/50 rounded-full mb-6 animate-pulse">
                    <i class="fa-solid fa-lock text-5xl text-red-500"></i>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-black text-red-500 uppercase tracking-wider mb-3">
                    NO ACCESS
                </h1>
                
                <p class="text-sm text-gray-400 uppercase tracking-widest">
                    Akses Anda Telah Diblokir
                </p>
            </div>

            <!-- Warning Message -->
            <div class="bg-red-900/20 border border-red-500/50 rounded-lg p-6 mb-8">
                <div class="flex items-start gap-4">
                    <i class="fa-solid fa-triangle-exclamation text-3xl text-red-500 mt-1"></i>
                    <div class="w-full">
                        <h2 class="text-lg font-black text-red-500 uppercase tracking-wider mb-3">
                            Aktivitas Ilegal Terdeteksi
                        </h2>
                        <p class="text-sm text-gray-300 leading-relaxed mb-4">
                            Sistem keamanan kami telah mendeteksi aktivitas mencurigakan dari IP address Anda.
                        </p>
                        
                        @if(isset($ipBan) && $ipBan->last_pattern)
                            <div class="bg-black/50 rounded-lg p-4 mb-4">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Pelanggaran Terdeteksi:</p>
                                <p class="text-sm text-red-400 font-bold">{{ $ipBan->last_pattern }}</p>
                            </div>
                        @endif

                        <!-- Ban Status -->
                        @php
                            $isPermanent = !$ipBan->expires_at;
                            $isExpired = $ipBan->expires_at && now()->gte($ipBan->expires_at);
                            $remainingSeconds = $ipBan->expires_at ? now()->diffInSeconds($ipBan->expires_at, false) : 0;
                        @endphp

                        @if($isPermanent)
                            <!-- PERMANENT BAN -->
                            <div class="bg-red-900/30 border-2 border-red-500 rounded-lg p-6 mb-4">
                                <div class="text-center">
                                    <i class="fa-solid fa-ban text-5xl text-red-500 mb-4"></i>
                                    <h3 class="text-2xl font-black text-red-500 uppercase tracking-wider mb-2">
                                        BAN PERMANEN
                                    </h3>
                                    <p class="text-sm text-gray-300">
                                        Akun/IP Anda telah di-ban secara permanen setelah <strong class="text-red-500">{{ $ipBan->attempt_count }}</strong> percobaan pelanggaran.
                                    </p>
                                </div>
                            </div>
                        @elseif($remainingSeconds > 0)
                            <!-- TEMPORARY BAN WITH COUNTDOWN -->
                            <div class="bg-yellow-900/30 border-2 border-yellow-500 rounded-lg p-6 mb-4">
                                <div class="text-center">
                                    <i class="fa-solid fa-clock text-4xl text-yellow-500 mb-4"></i>
                                    <h3 class="text-xl font-black text-yellow-500 uppercase tracking-wider mb-2">
                                        Ban Sementara - Percobaan ke-{{ $ipBan->attempt_count }}
                                    </h3>
                                    <p class="text-xs text-gray-400 mb-4">
                                        @if($ipBan->attempt_count == 1)
                                            Cooldown 30 menit. Percobaan berikutnya: 2 jam.
                                        @elseif($ipBan->attempt_count == 2)
                                            Cooldown 2 jam. Percobaan berikutnya: BAN PERMANEN!
                                        @endif
                                    </p>
                                    <div class="bg-black/50 rounded-lg p-6">
                                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Waktu Tersisa:</p>
                                        <p id="countdown" class="text-4xl text-yellow-500 font-black tabular-nums" data-seconds="{{ $remainingSeconds }}">
                                            {{ gmdate('H:i:s', $remainingSeconds) }}
                                        </p>
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
                        @endif

                        <!-- Info Details -->
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <p class="text-gray-600 uppercase tracking-widest mb-1">IP Address</p>
                                <p class="text-white font-mono">{{ $ipBan->ip_address ?? 'Unknown' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 uppercase tracking-widest mb-1">Total Percobaan</p>
                                <p class="text-red-500 font-black">{{ $ipBan->attempt_count ?? 0 }} kali</p>
                            </div>
                            <div>
                                <p class="text-gray-600 uppercase tracking-widest mb-1">Status</p>
                                @if($isPermanent)
                                    <p class="text-red-500 font-black uppercase text-lg">BANNED PERMANEN</p>
                                @else
                                    <p class="text-red-500 font-black uppercase text-lg">BANNED</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-gray-600 uppercase tracking-widest mb-1">Waktu Ban</p>
                                <p class="text-white">{{ $ipBan->banned_at ? $ipBan->banned_at->format('d M Y H:i') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Violations List -->
            @if(isset($violations) && count($violations) > 0)
                <div class="bg-black/30 border border-red-900/50 rounded-lg p-6 mb-8">
                    <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">
                        <i class="fa-solid fa-list mr-2"></i>Riwayat Pelanggaran
                    </h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach(array_slice($violations, -5) as $violation)
                            <div class="bg-red-900/10 border border-red-900/30 rounded p-3">
                                <p class="text-xs text-red-400 font-bold mb-1">{{ $violation['pattern'] ?? 'Unknown' }}</p>
                                <p class="text-[9px] text-gray-600 font-mono truncate">{{ $violation['url'] ?? '' }}</p>
                                <p class="text-[9px] text-gray-700 mt-1">{{ $violation['timestamp'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Contact Admin -->
            <div class="space-y-4">
                <div class="bg-cyan-900/20 border border-cyan-500/30 rounded-lg p-4">
                    <p class="text-xs text-cyan-500 leading-relaxed mb-4">
                        <i class="fa-solid fa-info-circle mr-2"></i>
                        Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator sistem.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Admin WhatsApp -->
                        <a href="https://wa.me/628999800022" target="_blank" class="flex items-center justify-center gap-3 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-all">
                            <i class="fa-brands fa-whatsapp text-2xl"></i>
                            <div class="text-left">
                                <p class="text-[10px] text-green-200 uppercase tracking-wider">Admin</p>
                                <p class="text-sm font-black">0899-9800-022</p>
                            </div>
                        </a>

                        <!-- Staff Admin WhatsApp -->
                        <a href="https://wa.me/6283888530 05" target="_blank" class="flex items-center justify-center gap-3 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-all">
                            <i class="fa-brands fa-whatsapp text-2xl"></i>
                            <div class="text-left">
                                <p class="text-[10px] text-blue-200 uppercase tracking-wider">Staff Admin</p>
                                <p class="text-sm font-black">0838-8853-005</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-800 text-center">
                <p class="text-[9px] text-gray-700 uppercase tracking-widest">
                    Security System © {{ date('Y') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
