<section class="space-y-6">
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
        <h2 class="text-xl font-bold text-red-500 uppercase tracking-wider">
            Hapus Akun
        </h2>

        <p class="text-xs text-gray-500 max-w-sm md:text-right font-medium leading-relaxed">
            Setelah akun Anda dihapus, semua data dan sumber daya akan dihapus secara permanen.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Hapus Akun</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-[#0a0a0a]/80 backdrop-blur-md border border-red-600/30 shadow-[0_0_40px_rgba(220,38,38,0.15)] rounded-xl">
            @csrf
            @method('delete')

            <h2 class="text-xl font-black text-white uppercase tracking-wider mb-4">
                {{ __('Apakah Anda yakin ingin menghapus akun?') }}
            </h2>

            <p class="text-xs text-gray-400 leading-relaxed mb-8">
                {{ __('Sekali akun Anda dihapus, semua data dan sumber daya akan dihapus secara permanen. Silakan masukkan password dan kode keamanan Anda untuk mengonfirmasi.') }}
            </p>

            <div class="space-y-6">
                <div>
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full bg-[#0a0a0a] border border-gray-800 text-gray-200 px-4 py-4 rounded-lg focus:border-red-600 focus:shadow-[0_0_15px_rgba(220,38,38,0.2)] focus:outline-none transition-all"
                        placeholder="{{ __('Password') }}"
                    />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-text-input
                        id="security_answer"
                        name="security_answer"
                        type="password"
                        class="w-full bg-[#0a0a0a] border border-gray-800 text-gray-200 px-4 py-4 rounded-lg focus:border-red-600 focus:shadow-[0_0_15px_rgba(220,38,38,0.2)] focus:outline-none transition-all shadow-inner"
                        placeholder="Kode Keamanan"
                    />
                    <x-input-error :messages="$errors->userDeletion->get('security_answer')" class="mt-2" />
                </div>
            </div>

            <div class="mt-10 flex justify-end gap-4">
                <button type="button" x-on:click="$dispatch('close')" class="px-8 py-3 rounded-lg border border-gray-700 text-gray-400 hover:bg-gray-800 text-[10px] font-black uppercase tracking-widest transition-all">
                    BATAL
                </button>

                <button type="submit" class="px-8 py-3 rounded-lg bg-red-600 text-white hover:bg-red-500 text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_4px_15px_rgba(220,38,38,0.3)]">
                    HAPUS AKUN
                </button>
            </div>
        </form>
    </x-modal>
</section>
