<x-guest-layout>
    <div class="auth-box">
        <a href="{{ url('/') }}" class="auth-logo text-[#00ffff] text-2xl font-bold flex items-center justify-center gap-2 hover:opacity-80 transition-opacity">
            <i class="fa-solid fa-cloud"></i> Cloud Host
        </a>
        <h2 class="text-xl font-bold text-white mb-6">Lupa Password (Reset)</h2>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            
            <!-- Warning Box -->
            <div class="mb-6 p-4 rounded bg-yellow-900/20 border border-yellow-600 text-yellow-500 text-xs leading-relaxed text-center">
                <i class="fa-solid fa-triangle-exclamation mr-1 text-base relative top-0.5"></i>
                <strong class="font-bold text-yellow-400">PENTING:</strong> Pastikan Anda mengingat password baru Anda!
            </div>

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="input-group mb-4">
                <input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus placeholder="Email Address" readonly class="text-gray-500 bg-gray-900 cursor-not-allowed">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-left" />
            </div>

            <!-- Password -->
            <div class="input-group mb-4">
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Password Baru">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-left" />
            </div>

            <!-- Confirm Password -->
            <div class="input-group mb-6">
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi Password Baru">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-left" />
            </div>

            <button type="submit" class="btn btn-primary w-full py-3 mb-6 text-black font-bold" style="background-color: #00ffff; color: black; border-radius: 4px;">
                Reset Password
            </button>

            <div class="text-sm text-gray-400">
                <a href="{{ route('login') }}" class="text-gray-400 hover:text-white"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
</x-guest-layout>
