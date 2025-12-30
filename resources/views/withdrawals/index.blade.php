<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 md:px-6 py-6 md:py-10 space-y-6 md:space-y-8 min-h-screen text-white">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                 <h1 class="text-3xl font-black text-white mb-2 tracking-tight uppercase tracking-widest">Penarikan Saldo</h1>
                 <p class="text-gray-400 text-sm font-medium">Tarik pendapatan Anda langsung ke rekening atau e-wallet.</p>
            </div>
            <div class="bg-[#00ffff]/10 border border-[#00ffff]/30 px-6 py-3 rounded-2xl backdrop-blur-md shadow-[0_0_20px_rgba(0,255,255,0.1)]">
                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-1">Total Saldo Tersedia</span>
                <span class="text-2xl font-black text-[#00ffff]">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- (LEFT) WITHDRAWAL ACTION -->
            <div class="lg:col-span-12 xl:col-span-4 space-y-6">
                
                <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-gray-800 rounded-2xl p-8 shadow-[0_0_40px_rgba(0,0,0,0.5)] h-full">
                    <h3 class="text-lg font-black text-white mb-6 uppercase tracking-wider flex items-center gap-3">
                        <i class="fa-solid fa-money-bill-transfer text-[#00ffff]"></i> Request Penarikan
                    </h3>

                    @php
                        $hasPayment = Auth::user()->payment_method && Auth::user()->payment_number && Auth::user()->payment_name;
                        $hasMinimum = Auth::user()->balance >= $min_idr;
                    @endphp

                    @if (!$hasPayment)
                        <!-- Case 1: No Payment Method -->
                        <div class="bg-red-900/10 border border-red-500/30 rounded-xl p-6 text-center space-y-4">
                            <div class="w-16 h-16 bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fa-solid fa-credit-card text-2xl text-red-500"></i>
                            </div>
                            <h4 class="text-sm font-black text-white uppercase tracking-wider">Metode Pembayaran Belum Diatur</h4>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Anda harus mengatur detail pembayaran (Bank/E-Wallet) di profil sebelum dapat melakukan penarikan.
                            </p>
                            <a href="{{ route('profile.edit', ['tab' => 'account']) }}" class="block w-full bg-red-600 hover:bg-red-500 text-white font-black py-3 rounded-lg text-xs uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(220,38,38,0.2)]">
                                ATUR DI PENGATURAN PROFIL
                            </a>
                        </div>
                    @else
                        <!-- Withdrawal Form Section -->
                        @if (!$hasMinimum)
                            <!-- High Visibility Warning for Insufficient Balance -->
                            <div class="bg-yellow-900/10 border border-yellow-500/30 rounded-xl p-4 mb-6 flex items-center gap-4">
                                <div class="w-10 h-10 bg-yellow-900/20 rounded-full flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-triangle-exclamation text-yellow-500"></i>
                                </div>
                                <div>
                                    <h4 class="text-[10px] font-black text-white uppercase tracking-wider">Saldo Belum Mencukupi</h4>
                                    <p class="text-[9px] text-gray-500">Minimal penarikan adalah <b>Rp {{ number_format($min_idr, 0, ',', '.') }}</b>. Kumpulkan lebih banyak views!</p>
                                </div>
                            </div>
                        @endif
                        <!-- Case 3: Ready to Withdraw -->
                        @if ($errors->any())
                            <div class="bg-red-900/20 border border-red-500/50 rounded-lg p-4 mb-6">
                                <ul class="text-xs text-red-400 font-bold list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="bg-green-900/20 border border-green-500/50 rounded-lg p-4 mb-6">
                                <p class="text-xs text-green-400 font-bold flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                                </p>
                            </div>
                        @endif

                        <form action="{{ route('withdraw.store') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <!-- Payment Summary Card (Premium Redesign) -->
                            <div class="bg-[#0f0f0f] border border-gray-800 rounded-2xl pt-1 pb-5 px-6 space-y-3 relative overflow-hidden group shadow-[0_10px_30px_rgba(0,0,0,0.5)] border-l-4 border-l-[#00ffff]/40">
                                <!-- Subtle Background Art -->
                                <div class="absolute -top-4 -right-4 opacity-5 group-hover:opacity-10 transition-all pointer-events-none rotate-12 scale-150">
                                    <i class="fa-solid fa-shield-halved text-8xl text-[#00ffff]"></i>
                                </div>
                                
                                <div class="flex justify-between items-start relative z-20 -mx-1 -mt-0.5">
                                    <span class="text-[7.5px] font-black uppercase tracking-[0.4em] text-gray-600 mt-1">Info Rekening Penarikan</span>
                                    <a href="{{ route('profile.edit', ['tab' => 'account']) }}" class="relative z-30 bg-white/5 hover:bg-[#00ffff] text-[#00ffff] hover:text-black px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all border border-white/10 hover:border-[#00ffff] shadow-lg">
                                        Edit
                                    </a>
                                </div>

                                <div class="space-y-3 relative z-20 -mt-2">
                                    <div class="text-2xl font-black text-white uppercase tracking-tighter italic leading-none">{{ Auth::user()->payment_name }}</div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-[10px] font-black text-[#00ffff] uppercase tracking-widest">{{ Auth::user()->payment_method }}</div>
                                        <div class="w-1 h-1 rounded-full bg-gray-800"></div>
                                        <div class="text-xs font-mono text-gray-500 tracking-[0.2em] font-medium">{{ Auth::user()->payment_number }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div x-data="{ 
                                rawAmount: {{ floor(Auth::user()->balance / 1000) * 1000 }},
                                isEwallet: ['DANA', 'GOPAY', 'OVO', 'SHOPEEPAY', 'LINKAJA', 'iSAKU'].includes('{{ Auth::user()->payment_method }}'),
                                get roundedAmount() {
                                    return Math.floor(this.rawAmount / 1000) * 1000;
                                },
                                get fee() {
                                    const percent = this.isEwallet ? 3 : 5;
                                    return Math.floor((this.roundedAmount * percent) / 100);
                                },
                                formatIdr(val) {
                                    return new Intl.NumberFormat('id-ID').format(val);
                                },
                                updateAmount(val) {
                                    let numeric = val.replace(/\./g, '').replace(/[^0-9]/g, '');
                                    if (numeric === '') numeric = 0;
                                    this.rawAmount = parseInt(numeric);
                                    
                                    if (this.rawAmount > {{ Auth::user()->balance }}) {
                                        this.rawAmount = {{ floor(Auth::user()->balance / 1000) * 1000 }};
                                    }
                                }
                            }">
                                <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">JUMLAH PENARIKAN (RP)</label>
                                <div class="relative">
                                    <input type="hidden" name="amount" :value="roundedAmount">
                                    <input type="text" 
                                        :value="formatIdr(rawAmount)"
                                        @input="updateAmount($event.target.value)"
                                        @blur="rawAmount = roundedAmount"
                                        class="w-full bg-[#111] border border-gray-800 text-white rounded-xl px-5 py-4 focus:outline-none focus:border-[#00ffff] focus:shadow-[0_0_20px_rgba(0,255,255,0.15)] transition-all font-black text-2xl placeholder-gray-800 {{ !$hasMinimum ? 'opacity-30 pointer-events-none' : '' }}"
                                        placeholder="0"
                                        {{ !$hasMinimum ? 'readonly' : '' }}
                                    >
                                    <button type="button" 
                                        @click="rawAmount = {{ floor(Auth::user()->balance / 1000) * 1000 }}"
                                        {{ !$hasMinimum ? 'disabled' : '' }}
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-[#00ffff] bg-[#00ffff]/10 hover:bg-[#00ffff] hover:text-black px-3 py-1.5 rounded-lg border border-[#00ffff] transition-all uppercase tracking-widest active:scale-95 shadow-[0_0_10px_rgba(0,255,255,0.1)] {{ !$hasMinimum ? 'hidden' : '' }}">
                                        Tarik Semua
                                    </button>
                                </div>
                                <div class="flex justify-between mt-3 px-1 {{ !$hasMinimum ? 'opacity-30' : '' }}">
                                    <p class="text-[9px] text-gray-600 italic font-bold">Maksimal: Rp <span x-text="formatIdr({{ floor(Auth::user()->balance / 1000) * 1000 }})"></span></p>
                                    <p class="text-[9px] text-yellow-500/80 font-bold uppercase tracking-wider">Biaya Admin: Rp <span x-text="formatIdr(fee)"></span> (<span x-text="isEwallet ? '3%' : '5%'"></span>)</p>
                                </div>
                                <div class="mt-2 bg-[#00ffff]/5 border border-[#00ffff]/10 rounded-lg p-3 flex justify-between items-center text-[#00ffff] shadow-[inset_0_0_10px_rgba(0,255,255,0.05)] {{ !$hasMinimum ? 'opacity-30' : '' }}">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black uppercase tracking-widest">Total Bersih Diterima</span>
                                        <template x-if="rawAmount % 1000 !== 0">
                                            <span class="text-[8px] text-gray-500">Dibulatkan ke: Rp <span x-text="formatIdr(roundedAmount)"></span></span>
                                        </template>
                                    </div>
                                    <span class="text-base font-black">Rp <span x-text="formatIdr(roundedAmount - fee)"></span></span>
                                </div>
                            </div>
                            
                            <!-- Security Check -->
                            <div x-data="{ 
                                countdown: {{ session('cooldown') ?? 0 }},
                                timer: null,
                                startTimer() {
                                    if (this.countdown > 0) {
                                        this.timer = setInterval(() => {
                                            this.countdown--;
                                            if (this.countdown <= 0) clearInterval(this.timer);
                                        }, 1000);
                                    }
                                }
                            }" x-init="startTimer()" class="space-y-4">
                                <div class="bg-red-900/5 border border-red-500/20 rounded-xl p-5 {{ !$hasMinimum ? 'opacity-30' : '' }}">
                                    <label class="block text-red-500/70 text-[10px] font-black uppercase tracking-widest mb-2 flex justify-between items-center gap-2">
                                        <span class="flex items-center gap-2 italic">
                                            <i class="fa-solid fa-shield-halved"></i> KONFIRMASI KODE KEAMANAN
                                        </span>
                                        <template x-if="countdown > 0 || {{ \Illuminate\Support\Facades\RateLimiter::remaining('withdraw-security-'.Auth::id(), 3) == 0 ? 'true' : 'false' }}">
                                            <a href="https://t.me/your_admin_contact" target="_blank" class="text-[9px] text-[#00ffff] hover:underline font-black italic">
                                                HUBUNGI ADMIN <i class="fa-solid fa-headset ml-1"></i>
                                            </a>
                                        </template>
                                    </label>
                                    <input type="password" 
                                        name="security_answer" 
                                        class="w-full bg-black/50 border border-red-500/30 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-red-500 transition-all font-bold tracking-[0.5em] text-center placeholder-gray-800"
                                        placeholder="••••••"
                                        {{ !$hasMinimum ? 'disabled' : '' }}
                                        :disabled="countdown > 0"
                                    >
                                </div>

                                <button type="submit" 
                                    :disabled="countdown > 0 || {{ !$hasMinimum ? 'true' : 'false' }}"
                                    class="w-full bg-[#00ffff]/10 hover:bg-[#00ffff] border border-[#00ffff] text-[#00ffff] hover:text-black py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] transition-all shadow-[0_0_20px_rgba(0,255,255,0.1)] hover:shadow-[0_0_40px_rgba(0,255,255,0.4)] flex items-center justify-center gap-3 group disabled:opacity-30 disabled:grayscale disabled:pointer-events-none overflow-hidden relative active:scale-95">
                                    <template x-if="countdown > 0">
                                        <div class="flex items-center gap-2 italic">
                                            <i class="fa-solid fa-lock text-xs"></i>
                                            COOLDOWN: <span x-text="Math.floor(countdown / 60) + ':' + (countdown % 60).toString().padStart(2, '0')"></span>
                                        </div>
                                    </template>
                                    <template x-if="countdown <= 0">
                                        <div class="flex items-center gap-3">
                                            <i class="fa-solid fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                                            AJUKAN PENARIKAN SEKARANG
                                        </div>
                                    </template>
                                    
                                    <!-- Glow Effect -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- (RIGHT) HISTORY TABLE -->
            <div class="lg:col-span-12 xl:col-span-8">
                <div class="bg-[#0a0a0a]/80 backdrop-blur-md border border-gray-800 rounded-2xl overflow-hidden shadow-[0_0_30px_rgba(0,0,0,0.3)] min-h-full">
                    <div class="p-6 border-b border-gray-800 flex items-center justify-between">
                        <h3 class="text-lg font-black text-white uppercase tracking-wider flex items-center gap-3">
                            <i class="fa-solid fa-clock-rotate-left text-[#00ffff]"></i> Riwayat Penarikan
                        </h3>
                    </div>

                    @if($withdrawals->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="text-[10px] font-black uppercase tracking-widest text-gray-500 bg-[#0f0f0f]/80 border-b border-gray-800">
                                    <tr>
                                        <th class="px-8 py-5">Tanggal</th>
                                        <th class="px-6 py-5">Tujuan Pembayaran</th>
                                        <th class="px-6 py-5 text-right">Jumlah</th>
                                        <th class="px-8 py-5 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-800/50">
                                    @foreach($withdrawals as $w)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="text-xs font-black text-gray-300">{{ $w->created_at->format('d M Y') }}</div>
                                            <div class="text-[10px] text-gray-600">{{ $w->created_at->format('H:i') }} WIB</div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <div class="space-y-2">
                                                <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded bg-[#00ffff]/10 border border-[#00ffff]/20">
                                                    <i class="fa-solid fa-credit-card text-[9px] text-[#00ffff]"></i>
                                                    <span class="text-[9px] font-black tracking-widest text-[#00ffff] uppercase">{{ $w->payment_method }}</span>
                                                </div>
                                                
                                                @php
                                                    $parts = explode(' | ', $w->payment_details);
                                                    $account = $parts[0] ?? $w->payment_details;
                                                    $stats = $parts[1] ?? '';
                                                @endphp

                                                <div class="text-[11px] font-black text-white uppercase tracking-tight">{{ $account }}</div>
                                                @if($stats)
                                                    <div class="text-[10px] text-gray-500 font-medium italic leading-relaxed">{{ $stats }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 text-right font-black text-[#00ff00]">
                                            Rp {{ number_format($w->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            @if($w->status == 'pending')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-yellow-500/10 border border-yellow-500/30 text-[9px] font-black uppercase tracking-widest text-yellow-500 shadow-[0_0_15px_rgba(234,179,8,0.1)]">
                                                    <i class="fa-solid fa-spinner animate-spin"></i> Sedang Diproses
                                                </span>
                                            @elseif($w->status == 'approved')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-500/10 border border-green-500/30 text-[9px] font-black uppercase tracking-widest text-green-500 shadow-[0_0_15px_rgba(34,197,94,0.1)]">
                                                    <i class="fa-solid fa-check"></i> Berhasil
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-500/10 border border-red-500/30 text-[9px] font-black uppercase tracking-widest text-red-500 shadow-[0_0_15px_rgba(220,38,38,0.1)]">
                                                    <i class="fa-solid fa-xmark"></i> Ditolak
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-8 border-t border-gray-800">
                            {{ $withdrawals->links() }}
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-20 opacity-30 grayscale">
                            <i class="fa-solid fa-receipt text-6xl mb-4"></i>
                            <p class="text-xs font-black uppercase tracking-widest">Belum ada riwayat penarikan</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
