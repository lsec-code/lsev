<section>
    <div class="mb-10">
        <h2 class="text-2xl font-black text-white uppercase tracking-[0.25em]">
            {{ __('Perbarui Password') }}
        </h2>
        <div class="h-1 w-20 bg-red-600 mt-2"></div>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-8">
        @csrf
        @method('put')

        <!-- Warning Box -->
        <div class="mb-6 p-5 rounded-lg bg-red-900/10 border border-red-600/30 text-red-500 text-[11px] leading-relaxed flex items-start gap-4">
            <i class="fa-solid fa-triangle-exclamation text-xl mt-0.5"></i>
            <div>
                <strong class="font-black text-red-500 uppercase tracking-widest block mb-1">PERINGATAN KERAS:</strong> 
                <span>Jangan sampai lupa password baru Anda! Gunakan kombinasi angka, huruf, dan simbol untuk keamanan maksimal.</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3">
                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Kata Sandi Saat Ini</label>
                <input id="update_password_current_password" name="current_password" type="password" class="w-full bg-[#0a0a0a] border border-red-600/20 rounded-lg px-4 py-4 text-sm text-gray-200 focus:border-red-600 focus:shadow-[0_0_15px_rgba(220,38,38,0.2)] focus:outline-none transition-all" autocomplete="current-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div class="hidden md:block"></div>

            <div class="space-y-3">
                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Kata Sandi Baru</label>
                <input id="update_password_password" name="password" type="password" class="w-full bg-[#0a0a0a] border border-red-600/20 rounded-lg px-4 py-4 text-sm text-gray-200 focus:border-red-600 focus:shadow-[0_0_15px_rgba(220,38,38,0.2)] focus:outline-none transition-all" autocomplete="new-password" placeholder="Minimal 8 karakter" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div class="space-y-3">
                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Konfirmasi Kata Sandi Baru</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full bg-[#0a0a0a] border border-red-600/20 rounded-lg px-4 py-4 text-sm text-gray-200 focus:border-red-600 focus:shadow-[0_0_15px_rgba(220,38,38,0.2)] focus:outline-none transition-all" autocomplete="new-password" placeholder="Ulangi kata sandi baru" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="border-t border-red-600/20 pt-8 mt-10 shadow-[0_-5px_15px_rgba(220,38,38,0.05)]">
            <div>
                <label class="text-[11px] font-bold text-red-500 uppercase tracking-widest">Jawaban Keamanan</label>
                <input id="update_password_security_answer" name="security_answer" type="password" class="w-full bg-[#0a0a0a] border border-red-600/30 rounded-lg px-4 py-4 mt-3 text-sm text-white focus:border-red-600 focus:shadow-[0_0_15px_rgba(220,38,38,0.2)] focus:outline-none transition-all shadow-inner" placeholder="Masukkan Kode Keamanan Anda" />
                <p class="mt-3 text-[10px] text-gray-500 italic leading-relaxed">Verifikasi kode keamanan diperlukan untuk setiap perubahan data sensitif akun.</p>
                <x-input-error :messages="$errors->updatePassword->get('security_answer')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-start gap-6 pt-8">
            <button type="submit" class="bg-red-600 text-white font-black px-12 py-4 rounded-lg text-xs uppercase hover:bg-red-700 transition-all shadow-[0_4px_15px_rgba(220,38,38,0.3)] active:scale-95">
                Simpan Perubahan
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-bold text-green-500 uppercase tracking-widest flex items-center gap-2"
                >
                    <i class="fa-solid fa-circle-check"></i>
                    Berhasil Disimpan
                </p>
            @endif
        </div>
    </form>
</section>
