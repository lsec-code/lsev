<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACCOUNT SUSPENDED - Cloud Host</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="flex items-center justify-center h-screen bg-[#050505] text-white">
    <div class="text-center p-10 max-w-2xl w-full">
        <div class="mb-8 relative inline-block">
            <div class="absolute inset-0 bg-red-600 blur-2xl opacity-20 rounded-full animate-pulse"></div>
            <i class="fa-solid fa-ban text-8xl text-red-600 relative z-10"></i>
        </div>
        
        <h1 class="text-5xl font-black text-white mb-2 tracking-tighter uppercase">Akun Ditangguhkan</h1>
        <p class="text-red-500 font-mono text-sm tracking-widest mb-8 uppercase">Account Suspended // Security Violation</p>
        
        <div class="bg-[#0a0a0a] border border-red-900/50 p-6 rounded-xl text-left mb-8 relative overflow-hidden group">
            <div class="absolute inset-0 bg-red-600/5 group-hover:bg-red-600/10 transition-colors"></div>
            <div class="relative z-10 space-y-3">
                <div class="flex justify-between border-b border-red-900/30 pb-2">
                    <span class="text-gray-500 text-xs font-bold uppercase">Status</span>
                    <span class="text-red-500 text-xs font-bold uppercase">PERMANENT BAN</span>
                </div>
                <div class="flex justify-between border-b border-red-900/30 pb-2">
                    <span class="text-gray-500 text-xs font-bold uppercase">Reason</span>
                    <span class="text-white text-sm font-mono">{{ Auth::user()->suspension_reason ?? 'Violation of Terms & Security Policy' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs font-bold uppercase">Time</span>
                    <span class="text-gray-400 text-xs">{{ now()->format('d M Y H:i:s') }}</span>
                </div>
            </div>
        </div>

        <p class="text-gray-400 mb-8 leading-relaxed text-sm">
            Akun Anda telah dinonaktifkan secara otomatis oleh sistem keamanan kami karena terdeteksi melakukan aktivitas yang melanggar aturan (seperti percobaan upload file berbahaya atau manipulasi data berulang kali).
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-white text-black font-bold px-8 py-3 rounded hover:bg-gray-200 transition-colors uppercase tracking-widest text-xs">
                Logout Session
            </button>
        </form>
    </div>
    {!! \App\Models\SiteSetting::where('setting_key', 'ad_script_suspended')->value('setting_value') !!}
</body>
</html>
