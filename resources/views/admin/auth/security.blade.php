<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#0a0a0a]">
        <!-- Logo -->
        <div class="mb-8">
            <a href="/" class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-600 rounded-xl flex items-center justify-center shadow-[0_0_20px_rgba(220,38,38,0.5)]">
                     <i class="fa-solid fa-user-shield text-2xl text-white"></i>
                </div>
                <span class="text-2xl font-black text-white tracking-tighter">ADMIN <span class="text-red-600">PANEL</span></span>
            </a>
        </div>

        <!-- Card -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-[#111] border border-red-600/30 shadow-[0_0_50px_rgba(220,38,38,0.1)] overflow-hidden sm:rounded-xl relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-red-600/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-8">
                    <h2 class="text-xl font-black text-white uppercase tracking-wider mb-2">Verifikasi Keamanan</h2>
                    <p class="text-xs text-gray-500">Akses Admin membutuhkan kode otentikasi tambahan.</p>
                </div>

                <form method="POST" action="{{ route('admin.verify.security.check') }}">
                    @csrf

                    <!-- Security Code -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2" for="security_code">
                            Kode Keamanan
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                            <input id="security_code" type="password" name="security_code" required autofocus
                                class="w-full bg-[#0a0a0a] border border-gray-800 text-white text-center text-lg font-bold tracking-[0.5em] rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:border-red-600 focus:shadow-[0_0_15px_rgba(220,38,38,0.3)] transition-all placeholder-gray-700" 
                                placeholder="••••••">
                        </div>
                        @error('security_code')
                            <p class="mt-2 text-xs text-red-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-black uppercase tracking-widest py-3 rounded-lg shadow-[0_0_20px_rgba(220,38,38,0.4)] hover:shadow-[0_0_30px_rgba(220,38,38,0.6)] transition-all transform hover:-translate-y-0.5">
                        Verifikasi Akses
                    </button>
                    
                    <div class="mt-6 text-center">
                        <a href="/" class="text-[10px] text-gray-600 hover:text-white font-bold uppercase tracking-widest transition-colors">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Beranda
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-[10px] text-gray-600 font-mono">
                IP Address: <span class="text-gray-400">{{ request()->ip() }}</span>
            </p>
        </div>
    </div>
</x-guest-layout>
