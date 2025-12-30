<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SECURITY ALERT - Cloud Host</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #000; color: #fff; font-family: 'Courier New', Courier, monospace; overflow: hidden; }
        .glitch { position: relative; }
        .glitch::before, .glitch::after { content: attr(data-text); position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
        .glitch::before { left: 2px; text-shadow: -1px 0 red; clip: rect(44px, 450px, 56px, 0); animation: glitch-anim-1 5s infinite linear alternate-reverse; }
        .glitch::after { left: -2px; text-shadow: -1px 0 blue; clip: rect(44px, 450px, 56px, 0); animation: glitch-anim-2 5s infinite linear alternate-reverse; }
        @keyframes glitch-anim-1 { 0% { clip: rect(20px, 9999px, 86px, 0); } 100% { clip: rect(59px, 9999px, 16px, 0); } }
        @keyframes glitch-anim-2 { 0% { clip: rect(10px, 9999px, 30px, 0); } 100% { clip: rect(80px, 9999px, 86px, 0); } }
    </style>
</head>
<body class="flex items-center justify-center h-screen bg-black">
    <div class="text-center p-8 border-2 border-red-600 rounded-xl bg-red-900/10 shadow-[0_0_50px_rgba(220,38,38,0.5)] max-w-2xl">
        <div class="mb-6">
            <i class="fa-solid fa-triangle-exclamation text-8xl text-red-600 animate-pulse"></i>
        </div>
        
        <h1 class="text-4xl font-bold text-red-500 mb-2 glitch" data-text="ACCESS DENIED">ACCESS DENIED</h1>
        <h2 class="text-xl text-red-400 mb-6 font-bold tracking-widest">SECURITY VIOLATION DETECTED</h2>
        
        <div class="text-left bg-black p-4 rounded border border-red-800 font-mono text-sm text-red-300 mb-8 space-y-2">
            <p>> SYSTEM_ALERT: Malicious payload detected in upload stream.</p>
            <p>> ERROR_CODE: 0xSEC_INVALID_FILE_SIGNATURE</p>
            <p>> ACTION: Request Terminated.</p>
            <p>> LOG: User IP and Device ID have been logged for security review.</p>
        </div>

        <p class="text-gray-400 mb-8">
            Sistem kami mendeteksi percobaan upload file ilegal atau manipulasi data. 
            <br>
            Aknivitas ini telah dicatat. Jangan mencoba melakukan bypass sistem keamanan.
        </p>

        <a href="{{ url('/') }}" class="inline-block px-8 py-3 bg-red-600 text-white font-bold rounded hover:bg-red-700 transition-colors uppercase tracking-wider">
            Kembali ke Halaman Utama
        </a>
    </div>
    {!! \App\Models\SiteSetting::where('setting_key', 'ad_script_security')->value('setting_value') !!}
</body>
</html>
