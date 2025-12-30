<x-app-layout>
@php
    $isDomainEnabled = \App\Models\SiteSetting::where('setting_key', 'custom_domain_enabled')->value('setting_value') !== 'false';
@endphp
    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" 
        x-data="{ 
            tab: (new URLSearchParams(window.location.search)).get('tab') || localStorage.getItem('active_profile_tab') || 'account',
            init() {
                this.$watch('tab', value => localStorage.setItem('active_profile_tab', value));
                
                // If URL has tab param, clear it after initialization to prevent it from overriding user clicks on refresh
                const url = new URL(window.location);
                if (url.searchParams.has('tab')) {
                    url.searchParams.delete('tab');
                    window.history.replaceState({}, '', url);
                }

                // Restore Scroll Position
                const scrollPos = localStorage.getItem('profile_scroll_pos');
                if (scrollPos) {
                    setTimeout(() => {
                        window.scrollTo({
                            top: parseInt(scrollPos),
                            behavior: 'instant'
                        });
                    }, 50);
                }

                // Track Scroll Position
                window.addEventListener('scroll', () => {
                    localStorage.setItem('profile_scroll_pos', window.scrollY);
                });
            }
        }">
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <!-- Sidebar -->
            <div class="w-full lg:w-64 shrink-0">
                <div class="bg-[#111] border border-[#00ffff]/20 shadow-[0_0_20px_rgba(0,255,255,0.05)] rounded-lg overflow-hidden grayscale-hover">
                    <button @click="tab = 'account'" :class="tab === 'account' ? 'bg-[#050505] text-[#00ffff] border-l-4 border-l-primary' : 'text-gray-400 hover:bg-[#050505]'" class="w-full text-left px-5 py-4 text-sm font-bold transition-all flex items-center justify-between">
                        {{ __('ui.account_settings') }}
                    </button>
                    <button @click="tab = 'password'" :class="tab === 'password' ? 'bg-[#050505] text-[#00ffff] border-l-4 border-l-primary' : 'text-gray-400 hover:bg-[#050505]'" class="w-full text-left px-5 py-4 text-sm font-bold transition-all flex items-center justify-between border-t border-[#00ffff]/10">
                        {{ __('ui.change_password') }}
                    </button>
                    <button @click="tab = 'activity'" :class="tab === 'activity' ? 'bg-[#050505] text-[#00ffff] border-l-4 border-l-primary' : 'text-gray-400 hover:bg-[#050505]'" class="w-full text-left px-5 py-4 text-sm font-bold transition-all flex items-center justify-between border-t border-[#00ffff]/10">
                        {{ __('ui.activity') }}
                    </button>
                    <button @click="tab = 'domain'" :class="tab === 'domain' ? 'bg-[#050505] text-[#00ffff] border-l-4 border-l-primary' : 'text-gray-400 hover:bg-[#050505]'" class="w-full text-left px-5 py-4 text-sm font-bold transition-all flex items-center justify-between border-t border-[#00ffff]/10">
                        {{ __('ui.custom_domain') }}
                    </button>
                    <button @click="tab = 'delete'" :class="tab === 'delete' ? 'bg-[#050505] text-red-500 border-l-4 border-l-red-500' : 'text-red-600/70 hover:bg-[#050505]'" class="w-full text-left px-5 py-4 text-sm font-bold transition-all flex items-center justify-between border-t border-red-600/10">
                        {{ __('ui.delete_account') }}
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 min-w-0">
                
                <!-- Account Settings Tab -->
                <div x-show="tab === 'account'" x-cloak class="space-y-8 animate-fade-in">
                    
                    <!-- Account Details Section -->
                    <div class="bg-[#111] border border-[#00ffff]/30 rounded-xl p-4 md:p-8 shadow-[0_0_30px_rgba(0,255,255,0.1)]">
                        <h3 class="text-white font-bold mb-8 flex items-center gap-2">
                             {{ __('ui.account_details') }}
                        </h3>

                        <!-- Avatar Form -->
                        <!-- Avatar Form -->
                        <div class="flex flex-col lg:flex-row gap-8 items-center lg:items-start mb-8 text-center lg:text-left">
                            <form id="avatar-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="relative group shrink-0">
                                @csrf
                                @method('patch')
                                
                                @php $avatarType = Auth::user()->getAvatarType(); @endphp
                                
                                <div class="relative w-28 h-28 rounded-full p-1 
                                    {{ $avatarType === 'dev' ? 'bg-gradient-to-tr from-red-600 to-blue-600 animate-pulse' : '' }}
                                    {{ $avatarType === 'gold' ? 'bg-gradient-to-tr from-[#FFC107] to-yellow-600' : '' }}
                                    {{ $avatarType === 'silver' ? 'bg-gradient-to-tr from-gray-300 to-gray-600' : '' }}
                                    {{ $avatarType === 'bronze' ? 'bg-gradient-to-tr from-orange-400 to-orange-800' : '' }}
                                    {{ $avatarType === 'default' ? 'bg-[#0a0a0a] border-2 border-[#00ffff]/30' : '' }}
                                    ">
                                    
                                    <img id="avatar-preview" src="{{ Auth::user()->getAvatarUrl() }}" class="w-full h-full rounded-full object-cover border-4 border-bg-dark">
                                    
                                    @if($avatarType === 'dev')
                                        <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-red-600 text-white font-black text-[10px] px-3 py-0.5 rounded-full shadow-[0_0_10px_rgba(220,38,38,0.5)] tracking-widest border border-white/20">
                                            DEV
                                        </div>
                                    @elseif($avatarType === 'gold')
                                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 text-3xl text-[#FFC107] drop-shadow-md animate-bounce">
                                            <i class="fa-solid fa-crown"></i>
                                        </div>
                                        <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-[#FFC107] text-black font-black text-[10px] px-3 py-0.5 rounded-full shadow-lg border border-white/20">
                                            #1 KING
                                        </div>
                                    @elseif($avatarType === 'silver')
                                        <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-gray-400 text-black font-black text-[10px] px-3 py-0.5 rounded-full shadow-lg border border-white/20">
                                            #2
                                        </div>
                                    @elseif($avatarType === 'bronze')
                                        <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-orange-600 text-white font-black text-[10px] px-3 py-0.5 rounded-full shadow-lg border border-white/20">
                                            #3
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4 text-center">
                                    <label class="cursor-pointer bg-[#0a0a0a] border border-[#00ffff]/40 text-gray-200 px-3 py-1.5 rounded text-[11px] font-bold hover:bg-[#050505] transition-all shadow-[0_0_10px_rgba(0,255,255,0.05)] inline-block">
                                        {{ __('ui.change_photo') }}
                                        <input type="file" name="avatar" class="hidden" onchange="document.getElementById('avatar-form').submit();">
                                    </label>
                                    <p class="text-[9px] text-gray-500 mt-2">Max 2MB</p>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('profile.update') }}" class="flex-1 space-y-8 w-full">
                                @csrf
                                @method('patch')

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full">
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Username</label>
                                        <input type="text" value="{{ Auth::user()->username }}" disabled class="w-full bg-[#0a0a0a] border border-[#00ffff]/10 rounded-lg px-4 py-3 text-sm text-gray-500 cursor-not-allowed transition-all">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Email</label>
                                        <input type="email" value="{{ Auth::user()->email }}" disabled class="w-full bg-[#0a0a0a] border border-[#00ffff]/10 rounded-lg px-4 py-3 text-sm text-gray-500 cursor-not-allowed transition-all">
                                    </div>
                                </div>

                                <div class="border-t border-[#00ffff]/20 pt-8 mt-4 shadow-[0_-5px_15px_rgba(0,255,255,0.05)]">
                                    <h3 class="text-white font-bold mb-6 flex items-center gap-2">
                                        {{ __('ui.payment_details') }}
                                    </h3>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">{{ __('ui.payment_method') }}</label>
                                            <select name="payment_method" class="w-full bg-[#0a0a0a] border @error('payment_method') border-red-500 @else border-[#00ffff]/30 @enderror rounded-lg px-4 py-3 text-sm text-gray-200 focus:border-[#00ffff] focus:shadow-[0_0_10px_rgba(0,255,255,0.2)] focus:outline-none appearance-none transition-all">
                                                <option value="">-- Pilih Metode --</option>
                                                <optgroup label="-- Bank --">
                                                    <option value="Bank BCA" {{ Auth::user()->payment_method == 'Bank BCA' ? 'selected' : '' }}>Bank BCA</option>
                                                    <option value="Bank blu (BCA Digital)" {{ Auth::user()->payment_method == 'Bank blu (BCA Digital)' ? 'selected' : '' }}>Bank blu (BCA Digital)</option>
                                                    <option value="Bank BRI" {{ Auth::user()->payment_method == 'Bank BRI' ? 'selected' : '' }}>Bank BRI</option>
                                                    <option value="Bank BNI" {{ Auth::user()->payment_method == 'Bank BNI' ? 'selected' : '' }}>Bank BNI</option>
                                                    <option value="Bank Syariah Indonesia" {{ Auth::user()->payment_method == 'Bank Syariah Indonesia' ? 'selected' : '' }}>Bank Syariah Indonesia</option>
                                                    <option value="Bank CIMB Niaga / Syariah" {{ Auth::user()->payment_method == 'Bank CIMB Niaga / Syariah' ? 'selected' : '' }}>Bank CIMB Niaga / Syariah</option>
                                                    <option value="Bank MANDIRI" {{ Auth::user()->payment_method == 'Bank MANDIRI' ? 'selected' : '' }}>Bank MANDIRI</option>
                                                    <option value="Bank JAGO" {{ Auth::user()->payment_method == 'Bank JAGO' ? 'selected' : '' }}>Bank JAGO</option>
                                                    <option value="Bank Superbank" {{ Auth::user()->payment_method == 'Bank Superbank' ? 'selected' : '' }}>Bank Superbank</option>
                                                    <option value="Bank BTPN/Jenius" {{ Auth::user()->payment_method == 'Bank BTPN/Jenius' ? 'selected' : '' }}>Bank BTPN/Jenius</option>
                                                    <option value="Bank Danamon" {{ Auth::user()->payment_method == 'Bank Danamon' ? 'selected' : '' }}>Bank Danamon</option>
                                                    <option value="Bank Seabank" {{ Auth::user()->payment_method == 'Bank Seabank' ? 'selected' : '' }}>Bank Seabank</option>
                                                    <option value="Line Bank" {{ Auth::user()->payment_method == 'Line Bank' ? 'selected' : '' }}>Line Bank</option>
                                                    <option value="Bank NEO Commerce (BNC)" {{ Auth::user()->payment_method == 'Bank NEO Commerce (BNC)' ? 'selected' : '' }}>Bank NEO Commerce (BNC)</option>
                                                </optgroup>
                                                <optgroup label="-- e-Wallet --">
                                                    <option value="DANA" {{ Auth::user()->payment_method == 'DANA' ? 'selected' : '' }}>DANA</option>
                                                    <option value="GOPAY" {{ Auth::user()->payment_method == 'GOPAY' ? 'selected' : '' }}>GOPAY</option>
                                                    <option value="iSAKU" {{ Auth::user()->payment_method == 'iSAKU' ? 'selected' : '' }}>iSAKU</option>
                                                    <option value="OVO" {{ Auth::user()->payment_method == 'OVO' ? 'selected' : '' }}>OVO</option>
                                                    <option value="SHOPEEPAY" {{ Auth::user()->payment_method == 'SHOPEEPAY' ? 'selected' : '' }}>SHOPEEPAY</option>
                                                    <option value="LINKAJA" {{ Auth::user()->payment_method == 'LINKAJA' ? 'selected' : '' }}>LINKAJA</option>
                                                </optgroup>
                                                <optgroup label="-- Crypto --">
                                                    <option value="USDT (TRC20)" {{ Auth::user()->payment_method == 'USDT (TRC20)' ? 'selected' : '' }}>USDT (TRC20)</option>
                                                </optgroup>
                                            </select>
                                            @error('payment_method')
                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">{{ __('ui.payment_number') }}</label>
                                            <input 
                                                type="text" 
                                                name="payment_number" 
                                                value="{{ old('payment_number', Auth::user()->payment_number) }}" 
                                                class="w-full bg-[#0a0a0a] border @error('payment_number') border-red-500 @else border-[#222] @enderror rounded-lg px-4 py-3 text-sm text-gray-200 focus:border-[#00ffff] focus:shadow-[0_0_10px_rgba(0,255,255,0.2)] focus:outline-none transition-all" 
                                                placeholder="Nomor Rekening / HP"
                                                pattern="[0-9]+"
                                                title="Hanya boleh berisi angka"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            >
                                            @error('payment_number')
                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="space-y-2 lg:col-span-2">
                                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">{{ __('ui.account_name') }}</label>
                                            <input 
                                                type="text" 
                                                name="payment_name" 
                                                value="{{ old('payment_name', Auth::user()->payment_name) }}" 
                                                class="w-full bg-[#0a0a0a] border @error('payment_name') border-red-500 @else border-[#222] @enderror rounded-lg px-4 py-3 text-sm text-gray-200 focus:border-[#00ffff] focus:shadow-[0_0_10px_rgba(0,255,255,0.2)] focus:outline-none transition-all" 
                                                placeholder="Nama Pemilik Akun"
                                                pattern="[a-zA-Z\s]+"
                                                title="Hanya boleh berisi huruf dan spasi"
                                                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                                            >
                                            @error('payment_name')
                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end pt-4">
                                    <button type="submit" class="bg-[#00ffff]/10 border border-[#00ffff] text-[#00ffff] hover:bg-[#00ffff] hover:text-black font-black px-8 py-3 rounded-xl text-xs uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(0,255,255,0.1)] hover:shadow-[0_0_30px_rgba(0,255,255,0.4)]">
                                        {{ __('SIMPAN PERUBAHAN') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Video Settings Section -->
                    <div class="bg-[#111] border border-[#00ffff]/30 rounded-xl p-4 md:p-8 shadow-[0_0_30px_rgba(0,255,255,0.1)]">
                        <h3 class="text-white font-bold mb-6 flex items-center gap-2">
                            {{ __('ui.video_settings') }}
                        </h3>
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="video_settings_update" value="1">
                            
                            <div class="space-y-4">
                                <label class="flex flex-col gap-1 cursor-pointer group">
                                    <div class="flex items-center gap-3">
                                        <div class="relative w-10 h-6">
                                            <input type="checkbox" name="allow_download" class="sr-only peer" {{ Auth::user()->allow_download ? 'checked' : '' }}>
                                            <div class="w-10 h-6 bg-gray-700 rounded-full peer peer-checked:bg-[#00ffff] transition-all"></div>
                                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-all peer-checked:left-5"></div>
                                        </div>
                                        <span class="text-sm font-bold text-gray-300 group-hover:text-white transition-colors">{{ __('ui.enable_download') }}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 ml-13">(Allow or forbid viewers to download your videos)</p>
                                </label>
                            </div>

                            <div class="flex justify-end pt-6">
                                <button type="submit" class="bg-[#00ffff]/10 border border-[#00ffff] text-[#00ffff] hover:bg-[#00ffff] hover:text-black font-black px-8 py-3 rounded-xl text-xs uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(0,255,255,0.1)] hover:shadow-[0_0_30px_rgba(0,255,255,0.4)]">
                                    {{ __('SIMPAN PENGATURAN') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Hint Info Box -->
                    <div class="bg-[#111] border border-[#00ffff]/20 rounded-xl p-4 md:p-8 shadow-[0_0_20px_rgba(0,255,255,0.05)]">
                        <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                             {{ __('ui.security_hint') }}
                        </h3>
                        <p class="text-[11px] text-gray-500 mb-6">{{ __('ui.security_hint_note') }}</p>
                        
                        <div class="bg-blue-900/10 border border-blue-500/30 rounded-lg p-5 flex items-center gap-4 text-blue-400">
                            <i class="fa-solid fa-circle-info text-xl"></i>
                            <p class="text-[11px] font-medium leading-relaxed">
                                {{ __('ui.security_lock_note') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Change Password Tab -->
                <div x-show="tab === 'password'" x-cloak class="animate-fade-in">
                    <div class="bg-[#111] border border-[#00ffff]/30 rounded-xl p-4 md:p-8 shadow-[0_0_30px_rgba(0,255,255,0.1)]">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Custom Domain Tab -->
                <div x-show="tab === 'domain'" x-cloak class="space-y-8 animate-fade-in">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="custom_domain_update" value="1">

                        <!-- Domain Settings -->
                        <div class="bg-[#111]/80 backdrop-blur-md border border-[#00ffff]/30 rounded-xl p-4 md:p-8 shadow-[0_0_30px_rgba(0,255,255,0.1)]">
                            <h3 class="text-white font-bold mb-6 flex items-center gap-2">
                                <i class="fa-solid fa-globe text-[#00ffff]"></i>
                                Domain Kustom
                            </h3>

                            <!-- WARNING: FEATURE DISABLED -->
                            @if(!$isDomainEnabled)
                            <div class="mb-6 bg-red-500/10 border border-red-500/50 rounded-xl p-4 flex items-center gap-4 shadow-[0_0_20px_rgba(220,38,38,0.2)]">
                                <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-ban text-red-500 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-red-500 font-bold text-sm uppercase tracking-wider">FITUR DINONAKTIFKAN</h4>
                                    <p class="text-gray-400 text-xs mt-1">
                                        Fitur Kustom Domain sedang dalam pemeliharaan. Harap hubungi admin untuk informasi lebih lanjut.
                                    </p>
                                </div>
                            </div>
                            @endif

                            <div class="space-y-6 {{ !$isDomainEnabled ? 'opacity-50 pointer-events-none' : '' }}">
                                <!-- Domain Input -->
                                <div>
                                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2 block">
                                        Domain Anda
                                    </label>
                                    <input 
                                        type="text" 
                                        name="custom_domain" 
                                        value="{{ old('custom_domain', Auth::user()->custom_domain) }}" 
                                        class="w-full bg-[#0a0a0a] border @error('custom_domain') border-red-500 @else border-[#00ffff]/30 @enderror rounded-lg px-4 py-3 text-sm text-gray-200 focus:border-[#00ffff] focus:shadow-[0_0_10px_rgba(0,255,255,0.2)] focus:outline-none transition-all" 
                                        placeholder="myvideos.com"
                                        {{ !$isDomainEnabled ? 'disabled' : '' }}
                                    >
                                    <p class="text-[10px] text-gray-600 mt-2">
                                        <i class="fa-solid fa-info-circle mr-1"></i>
                                        Jangan sertakan http:// atau https://
                                    </p>
                                    @error('custom_domain')
                                        <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Verification Status -->
                                @if(Auth::user()->custom_domain)
                                    <div class="bg-[#0a0a0a]/50 border @if(Auth::user()->domain_verified) border-green-500/30 @else border-yellow-500/30 @endif rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                @if(Auth::user()->domain_verified)
                                                    <i class="fa-solid fa-circle-check text-2xl text-green-500"></i>
                                                    <div>
                                                        <p class="text-sm font-bold text-green-500">Domain Terverifikasi</p>
                                                        <p class="text-[10px] text-gray-500">
                                                            Verified: {{ Auth::user()->domain_verified_at->format('d M Y H:i') }}
                                                        </p>
                                                    </div>
                                                @else
                                                    <i class="fa-solid fa-circle-exclamation text-2xl text-yellow-500"></i>
                                                    <div>
                                                        <p class="text-sm font-bold text-yellow-500">Belum Terverifikasi</p>
                                                        <p class="text-[10px] text-gray-500">
                                                            DNS belum mengarah ke server atau masih dalam propagasi
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Save Button (Updated Style) -->
                                <div class="flex justify-end">
                                    <button type="submit" {{ !$isDomainEnabled ? 'disabled' : '' }} class="bg-[#00ffff]/10 border border-[#00ffff]/50 text-[#00ffff] font-black px-8 py-3 rounded-xl text-xs uppercase tracking-widest hover:bg-[#00ffff] hover:text-black transition-all shadow-[0_0_15px_rgba(0,255,255,0.1)] hover:shadow-[0_0_30px_rgba(0,255,255,0.4)] flex items-center gap-2">
                                        <i class="fa-solid fa-check"></i>
                                        Simpan & Verifikasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tutorial Section -->
                    <div class="bg-[#111]/80 backdrop-blur-md border border-[#00ffff]/30 rounded-xl p-4 md:p-8 shadow-[0_0_30px_rgba(0,255,255,0.1)]">
                        <h3 class="text-white font-bold mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-book text-[#00ffff]"></i>
                            Tutorial Setup DNS
                        </h3>

                        <div class="space-y-6">
                            <!-- Step 1 -->
                            <div class="bg-[#0a0a0a]/50 border border-[#00ffff]/20 rounded-lg p-6">
                                <h4 class="text-[#00ffff] font-bold mb-3 flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 bg-[#00ffff] text-black rounded-full text-xs font-black">1</span>
                                    Dapatkan IP Server
                                </h4>
                                <p class="text-sm text-gray-300 leading-relaxed">
                                    Hubungi administrator untuk mendapatkan <strong class="text-[#00ffff]">IP Address Server</strong>. Anda memerlukan ini untuk konfigurasi DNS.
                                </p>
                                <div class="mt-3 flex gap-3">
                                    <a href="https://wa.me/628999800022" target="_blank" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-4 py-2 rounded transition-all">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        Admin
                                    </a>
                                    <a href="https://wa.me/6283888530 05" target="_blank" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded transition-all">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        Staff Admin
                                    </a>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="bg-[#0a0a0a]/50 border border-[#00ffff]/20 rounded-lg p-6">
                                <h4 class="text-[#00ffff] font-bold mb-3 flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 bg-[#00ffff] text-black rounded-full text-xs font-black">2</span>
                                    Setup DNS Record
                                </h4>
                                <p class="text-sm text-gray-300 leading-relaxed mb-4">
                                    Login ke DNS provider Anda (Cloudflare, Namecheap, GoDaddy, dll) dan tambahkan <strong class="text-[#00ffff]">A Record</strong>:
                                </p>
                                <div class="bg-black/50 rounded-lg p-4 font-mono text-xs">
                                    <table class="w-full">
                                        <tr class="border-b border-gray-800">
                                            <td class="py-2 text-gray-500">Type:</td>
                                            <td class="py-2 text-[#00ffff]">A</td>
                                        </tr>
                                        <tr class="border-b border-gray-800">
                                            <td class="py-2 text-gray-500">Name/Host:</td>
                                            <td class="py-2 text-[#00ffff]">@ <span class="text-gray-600">(atau subdomain)</span></td>
                                        </tr>
                                        <tr class="border-b border-gray-800">
                                            <td class="py-2 text-gray-500">Value/Points to:</td>
                                            <td class="py-2 text-[#00ffff]">IP Server dari Admin</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 text-gray-500">TTL:</td>
                                            <td class="py-2 text-[#00ffff]">Auto / 3600</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="bg-[#0a0a0a]/50 border border-[#00ffff]/20 rounded-lg p-6">
                                <h4 class="text-[#00ffff] font-bold mb-3 flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 bg-[#00ffff] text-black rounded-full text-xs font-black">3</span>
                                    Tunggu Propagasi DNS
                                </h4>
                                <p class="text-sm text-gray-300 leading-relaxed mb-3">
                                    DNS propagation membutuhkan waktu <strong class="text-[#00ffff]">1-2 jam</strong> (maksimal 24-48 jam).
                                </p>
                                <p class="text-xs text-gray-500">
                                    <i class="fa-solid fa-lightbulb text-yellow-500 mr-1"></i>
                                    Cek status propagasi di: <a href="https://dnschecker.org" target="_blank" class="text-[#00ffff] hover:underline">dnschecker.org</a>
                                </p>
                            </div>

                            <!-- Step 4 -->
                            <div class="bg-[#0a0a0a]/50 border border-[#00ffff]/20 rounded-lg p-6">
                                <h4 class="text-[#00ffff] font-bold mb-3 flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 bg-[#00ffff] text-black rounded-full text-xs font-black">4</span>
                                    Set Domain di Profile
                                </h4>
                                <p class="text-sm text-gray-300 leading-relaxed">
                                    Masukkan domain Anda di form di atas, lalu klik <strong class="text-[#00ffff]">Simpan & Verifikasi</strong>. Sistem akan otomatis mengecek apakah DNS sudah mengarah ke server yang benar.
                                </p>
                            </div>

                            <!-- Important Note -->
                            <div class="bg-yellow-900/20 border border-yellow-500/30 rounded-lg p-4">
                                <p class="text-xs text-yellow-500 leading-relaxed">
                                    <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                                    <strong>Penting:</strong> Jika domain belum terverifikasi, tunggu beberapa saat untuk DNS propagation, lalu klik "Simpan & Verifikasi" lagi untuk re-check.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Account Tab -->
                <div x-show="tab === 'delete'" x-cloak class="animate-fade-in">
                    <div class="bg-[#111]/50 border border-red-600/40 rounded-xl p-4 md:p-8 shadow-[0_0_30px_rgba(220,38,38,0.1)]">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

                <!-- Activity & Sessions Tab -->
                <div x-show="tab === 'activity'" x-cloak class="space-y-8 animate-fade-in">
                    <!-- Active Sessions Section -->
                    <div class="bg-[#111]/80 backdrop-blur-md border border-[#00ffff]/30 rounded-xl p-4 md:p-8 shadow-[0_0_30px_rgba(0,255,255,0.1)]">
                        <h3 class="text-white font-bold mb-6 flex items-center gap-2">
                             Sesi Aktif Saat Ini
                        </h3>
                        <div class="space-y-4">
                            @foreach($sessions as $session)
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-[#0a0a0a] border border-[#00ffff]/10 rounded-xl gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="text-2xl text-[#00ffff] shrink-0">
                                            @php
                                                $browser = strtolower($session->browser);
                                                $icon = 'fa-solid fa-globe';
                                                if (str_contains($browser, 'chrome')) $icon = 'fa-brands fa-chrome';
                                                elseif (str_contains($browser, 'safari')) $icon = 'fa-brands fa-safari';
                                                elseif (str_contains($browser, 'firefox')) $icon = 'fa-brands fa-firefox';
                                                elseif (str_contains($browser, 'opera')) $icon = 'fa-brands fa-opera';
                                                elseif (str_contains($browser, 'edge')) $icon = 'fa-brands fa-edge';
                                                elseif (str_contains($browser, 'uc browser')) $icon = 'fa-solid fa-u';
                                                elseif (str_contains($browser, 'vivaldi')) $icon = 'fa-solid fa-v';
                                            @endphp
                                            <i class="{{ $icon }}"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-sm font-bold text-gray-200 truncate">{{ $session->platform }} - {{ $session->browser }}</span>
                                                @if($session->is_current_device)
                                                    <span class="text-[9px] px-2 py-0.5 bg-[#00ffff]/20 text-[#00ffff] rounded-full font-black uppercase tracking-widest border border-[#00ffff]/30 shrink-0">Perangkat Ini</span>
                                                @endif
                                            </div>
                                            <div class="text-[10px] text-gray-500 uppercase tracking-widest mt-1 truncate">
                                                {{ $session->ip_address }} • 
                                                @php
                                                    $lastActivityTime = \Carbon\Carbon::createFromTimestamp($session->last_activity);
                                                    $minutesAgo = now()->diffInMinutes($lastActivityTime);
                                                @endphp
                                                @if($minutesAgo < 5)
                                                    <span class="text-green-500 font-black">SEDANG AKTIF</span>
                                                @else
                                                    <span class="text-gray-600">IDLE</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if(!$session->is_current_device)
                                        <form action="{{ route('profile.sessions.logout', $session->id) }}" method="POST" class="w-full sm:w-auto mt-2 sm:mt-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full sm:w-auto bg-red-500/10 hover:bg-red-500/20 text-red-500 px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors border border-red-500/20">
                                                Log Out
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Login History Section -->
                    <div class="bg-[#111]/80 backdrop-blur-md border border-[#00ffff]/30 rounded-xl p-4 md:p-8 shadow-[0_0_30px_rgba(0,255,255,0.1)]">
                        <h3 class="text-white font-bold mb-6 flex items-center gap-2">
                             Riwayat Aktivitas Login
                        </h3>
                        <div class="overflow-x-auto custom-scrollbar -mx-4 px-4 md:mx-0 md:px-0">
                            <table class="w-full text-left min-w-[600px]">
                                <thead>
                                    <tr class="text-[10px] text-gray-500 uppercase tracking-widest border-b border-[#00ffff]/10 whitespace-nowrap">
                                        <th class="pb-4 pr-4 font-black">Browser / Device</th>
                                        <th class="pb-4 pr-4 font-black">IP Address</th>
                                        <th class="pb-4 pr-4 font-black">Lokasi</th>
                                        <th class="pb-4 font-black">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-primary/5">
                                    @foreach($activities as $activity)
                                        <tr class="group hover:bg-[#050505] transition-colors">
                                            <td class="py-4 pr-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    @php
                                                        $browser = strtolower($activity->browser);
                                                        $icon = 'fa-solid fa-globe';
                                                        if (str_contains($browser, 'chrome')) $icon = 'fa-brands fa-chrome';
                                                        elseif (str_contains($browser, 'safari')) $icon = 'fa-brands fa-safari';
                                                        elseif (str_contains($browser, 'firefox')) $icon = 'fa-brands fa-firefox';
                                                        elseif (str_contains($browser, 'opera')) $icon = 'fa-brands fa-opera';
                                                        elseif (str_contains($browser, 'edge')) $icon = 'fa-brands fa-edge';
                                                        elseif (str_contains($browser, 'uc browser')) $icon = 'fa-solid fa-u';
                                                        elseif (str_contains($browser, 'vivaldi')) $icon = 'fa-solid fa-v';
                                                    @endphp
                                                    <i class="{{ $icon }} text-[#00ffff]/60 group-hover:text-[#00ffff] transition-colors"></i>
                                                    <div>
                                                        <div class="text-xs font-bold text-gray-300">{{ $activity->browser }}</div>
                                                        <div class="text-[9px] text-gray-500 uppercase tracking-widest">{{ $activity->platform }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 pr-4 whitespace-nowrap text-xs text-gray-400 font-mono">
                                                @if($activity->ip_address && filter_var($activity->ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
                                                    {{ $activity->ip_address }}
                                                    @if($activity->ipv6_address && $activity->ipv6_address !== $activity->ip_address)
                                                        <br><span class="text-gray-600">{{ $activity->ipv6_address }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-[#00ffff]/60">{{ $activity->ip_address }}</span>
                                                    <span class="text-[9px] text-gray-600 ml-1">(IPv6 Only)</span>
                                                @endif
                                            </td>
                                            <td class="py-4">
                                                <div class="flex items-center gap-2 text-[10px] text-gray-400">
                                                    <i class="fa-solid fa-location-dot text-[#00ffff]/40"></i>
                                                    {{ $activity->location }}
                                                </div>
                                                @if($activity->status !== 'success')
                                                    <div class="mt-1">
                                                        @if($activity->status === 'failed')
                                                            <span class="inline-block px-2 py-0.5 bg-red-900/30 border border-red-500/50 text-red-500 text-[8px] font-black uppercase tracking-wider rounded">
                                                                <i class="fa-solid fa-circle-xmark mr-1"></i>GAGAL
                                                            </span>
                                                        @elseif($activity->status === 'cancelled')
                                                            <span class="inline-block px-2 py-0.5 bg-orange-900/30 border border-orange-500/50 text-orange-500 text-[8px] font-black uppercase tracking-wider rounded">
                                                                <i class="fa-solid fa-ban mr-1"></i>DIBATALKAN
                                                            </span>
                                                        @elseif($activity->status === 'blocked')
                                                            <span class="inline-block px-2 py-0.5 bg-yellow-900/30 border border-yellow-500/50 text-yellow-500 text-[8px] font-black uppercase tracking-wider rounded">
                                                                <i class="fa-solid fa-shield-halved mr-1"></i>DICEGAH
                                                            </span>
                                                        @endif
                                                        @if($activity->reason)
                                                            <p class="text-[9px] text-gray-600 mt-1 leading-relaxed">{{ $activity->reason }}</p>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-4 pr-4 whitespace-nowrap text-[10px] text-gray-500 uppercase tracking-widest font-medium">
                                                {{ $activity->login_at->format('d M Y - H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($activities->hasPages())
                            <div class="mt-6 flex justify-center">
                                <div class="flex items-center gap-2">
                                    @if($activities->onFirstPage())
                                        <span class="px-3 py-1.5 text-xs text-gray-600 bg-[#0a0a0a] rounded cursor-not-allowed">Previous</span>
                                    @else
                                        <a href="{{ $activities->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-[#00ffff] bg-[#0a0a0a] border border-[#00ffff]/30 rounded hover:bg-[#00ffff]/10 transition-all">Previous</a>
                                    @endif

                                    <span class="px-3 py-1.5 text-xs text-gray-400">
                                        Page {{ $activities->currentPage() }} of {{ $activities->lastPage() }}
                                    </span>

                                    @if($activities->hasMorePages())
                                        <a href="{{ $activities->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-[#00ffff] bg-[#0a0a0a] border border-[#00ffff]/30 rounded hover:bg-[#00ffff]/10 transition-all">Next</a>
                                    @else
                                        <span class="px-3 py-1.5 text-xs text-gray-600 bg-[#0a0a0a] rounded cursor-not-allowed">Next</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>



            </div>
        </div>
    </div>

    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .grayscale-hover button:hover:not(.text-[#00ffff]) { filter: grayscale(1); }
    </style>

    <!-- Success Notifications -->
    @if(session('status') === 'profile-updated')
        <script>window.addEventListener('DOMContentLoaded', () => showToast('PROFIL BERHASIL DIPERBARUI', 'success'));</script>
    @elseif(session('status') === 'password-updated')
        <script>window.addEventListener('DOMContentLoaded', () => showToast('PASSWORD BERHASIL DIUBAH', 'success'));</script>
    @elseif(session('status') === 'session-logged-out')
        <script>window.addEventListener('DOMContentLoaded', () => showToast('SESI PERANGKAT BERHASIL DIKELUARKAN', 'success'));</script>
    @endif
</x-app-layout>

