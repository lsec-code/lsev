<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-[#0a0a0a] border border-[#333] rounded-xl p-8 shadow-[0_0_50px_rgba(0,0,0,0.5)]">
            
            <div class="flex items-center gap-4 mb-8 pb-6 border-b border-[#222]">
                <div class="w-12 h-12 rounded-full bg-[#00ffff]/10 flex items-center justify-center border border-[#00ffff]/20">
                    <i class="fa-solid fa-user-plus text-[#00ffff] text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Buat User Baru</h1>
                    <p class="text-gray-400 text-sm">Input data user secara manual (Bypass Validation)</p>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>



            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf

                <!-- Username -->
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" 
                        class="w-full bg-[#111] border border-[#333] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-[#00ffff] focus:shadow-[0_0_15px_rgba(0,255,255,0.2)] transition-all placeholder-gray-700"
                        placeholder="username_user" required autofocus>
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full bg-[#111] border border-[#333] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-[#00ffff] focus:shadow-[0_0_15px_rgba(0,255,255,0.2)] transition-all placeholder-gray-700"
                        placeholder="email@example.com" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Password</label>
                        <input type="text" name="password" 
                            class="w-full bg-[#111] border border-[#333] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-[#00ffff] focus:shadow-[0_0_15px_rgba(0,255,255,0.2)] transition-all placeholder-gray-700"
                            placeholder="Min. 1 Karakter" required>
                        <p class="text-[10px] text-gray-500 mt-1">*Bebas, bisa 1 karakter saja.</p>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Security Code -->
                    <div>
                        <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Kode Keamanan</label>
                        <input type="text" name="security_code" 
                            class="w-full bg-[#111] border border-[#333] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-[#00ffff] focus:shadow-[0_0_15px_rgba(0,255,255,0.2)] transition-all placeholder-gray-700"
                            placeholder="Untuk Withdraw">
                         <p class="text-[10px] text-gray-500 mt-1">*Opsional.</p>
                        @error('security_code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Role Selection -->
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Role / Hak Akses</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="user" class="peer sr-only" checked>
                            <div class="bg-[#111] border border-[#333] peer-checked:border-[#00ffff] peer-checked:bg-[#00ffff]/10 peer-checked:text-[#00ffff] rounded-lg p-4 text-center transition-all group">
                                <i class="fa-solid fa-user mb-2 text-xl text-gray-500 group-hover:text-white peer-checked:text-[#00ffff] transition-colors"></i>
                                <div class="font-bold text-sm">Regular User</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="admin" class="peer sr-only">
                            <div class="bg-[#111] border border-[#333] peer-checked:border-red-500 peer-checked:bg-red-500/10 peer-checked:text-red-500 rounded-lg p-4 text-center transition-all group">
                                <i class="fa-solid fa-shield-halved mb-2 text-xl text-gray-500 group-hover:text-white peer-checked:text-red-500 transition-colors"></i>
                                <div class="font-bold text-sm">Administrator</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-[#222] flex items-center justify-end gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-white text-sm font-bold uppercase tracking-wider transition-colors">Batal</a>
                    <button type="submit" class="bg-[#00ffff]/10 border border-[#00ffff] text-[#00ffff] hover:bg-[#00ffff] hover:text-black font-black py-4 px-10 rounded-xl text-xs uppercase tracking-[0.2em] transition-all transform hover:-translate-y-1 shadow-[0_0_20px_rgba(0,255,255,0.1)] hover:shadow-[0_0_30px_rgba(0,255,255,0.4)]">
                        <i class="fa-solid fa-check mr-2"></i> BUAT USER
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
</x-app-layout>
