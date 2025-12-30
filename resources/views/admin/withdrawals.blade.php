<x-app-layout>
    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
            <div class="bg-[#0a0a0a] border border-[#222] overflow-hidden shadow-xl rounded-2xl p-5 md:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white uppercase tracking-wider">Permintaan Penarikan</h2>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">{{ $withdrawals->total() }} Menunggu</span>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-900/20 border border-green-500/50 text-green-400 rounded text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-border-color">
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Pengguna</th>
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Metode & Detail</th>
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Jumlah</th>
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-center">Status</th>
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-color">
                            @forelse($withdrawals as $w)
                                <tr>
                                    <td class="py-4">
                                        <div class="font-bold text-gray-200">{{ $w->user->username }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $w->user->email }}</div>
                                    </td>
                                    <td class="py-4">
                                        <div class="space-y-1">
                                            <div class="text-[10px] text-[#00ffff] font-black uppercase tracking-widest">{{ $w->payment_method }}</div>
                                            @php
                                                $parts = explode(' | ', $w->payment_details);
                                                $account = $parts[0] ?? $w->payment_details;
                                                $stats = $parts[1] ?? '';
                                            @endphp
                                            <div class="text-[11px] text-gray-200 font-black uppercase tracking-tight">{{ $account }}</div>
                                            @if($stats)
                                                <div class="text-[9px] text-gray-500 font-medium italic">{{ $stats }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 text-right">
                                        <div class="text-sm font-bold text-white">Rp {{ number_format($w->amount, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="py-4 text-center">
                                        @if($w->status == 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-yellow-500/10 border border-yellow-500/30 text-[8px] font-black uppercase tracking-widest text-yellow-500 shadow-[0_0_10px_rgba(234,179,8,0.1)]">
                                                <i class="fa-solid fa-spinner animate-spin"></i> Sedang Diproses
                                            </span>
                                        @elseif($w->status == 'approved')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-green-500/10 border border-green-500/30 text-[8px] font-black uppercase tracking-widest text-green-500 shadow-[0_0_10px_rgba(34,197,94,0.1)]">
                                                <i class="fa-solid fa-check"></i> Berhasil
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-red-500/10 border border-red-500/30 text-[8px] font-black uppercase tracking-widest text-red-500 shadow-[0_0_10px_rgba(220,38,38,0.1)]">
                                                <i class="fa-solid fa-xmark"></i> Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-right">
                                        @if($w->status === 'pending')
                                            <div class="flex justify-end gap-2" x-data="{ showApprove: false, showReject: false }">
                                                <!-- Approve Button -->
                                                <button @click="showApprove = true" class="bg-green-600 hover:bg-green-500 text-black font-bold px-3 py-1 rounded text-[10px] uppercase transition-all shadow-[0_2px_10px_rgba(34,197,94,0.2)]">Setuju</button>
                                                
                                                <!-- Reject Button -->
                                                <button @click="showReject = true" class="bg-red-600 hover:bg-red-500 text-white font-bold px-3 py-1 rounded text-[10px] uppercase transition-all shadow-[0_2px_10px_rgba(220,38,38,0.2)]">Tolak</button>

                                                <!-- Approve Modal -->
                                                <template x-teleport="body">
                                                    <div x-show="showApprove" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-cloak>
                                                        <div @click.away="showApprove = false" class="bg-bg-card/80 backdrop-blur-md border border-green-600/30 w-full max-w-sm rounded-xl shadow-[0_0_50px_rgba(34,197,94,0.15)] p-8 animate-fade-in">
                                                            <h3 class="text-lg font-black text-white uppercase tracking-wider mb-2 text-green-500 text-center">Setujui Penarikan?</h3>
                                                            <p class="text-[10px] text-gray-400 leading-relaxed mb-8 text-center uppercase tracking-widest">Konfirmasi untuk menyetujui penarikan sebesar <br><span class="text-white text-sm font-black">Rp {{ number_format($w->amount, 0, ',', '.') }}</span></p>
                                                            
                                                            <div class="flex justify-center gap-4">
                                                                <button @click="showApprove = false" class="px-6 py-2.5 rounded-lg border border-gray-700 text-gray-400 hover:bg-gray-800 text-[10px] font-black uppercase tracking-widest transition-all">BATAL</button>
                                                                <form action="{{ route('admin.withdrawals.update', [$w->id, 'approve']) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-green-600 text-black hover:bg-green-500 text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_4px_15px_rgba(34,197,94,0.3)]">SETUJU</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Reject Modal -->
                                                <template x-teleport="body">
                                                    <div x-show="showReject" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-cloak>
                                                        <div @click.away="showReject = false" class="bg-bg-card/80 backdrop-blur-md border border-red-600/30 w-full max-w-sm rounded-xl shadow-[0_0_50px_rgba(220,38,38,0.15)] p-8 animate-fade-in">
                                                            <h3 class="text-lg font-black text-white uppercase tracking-wider mb-2 text-red-500 text-center">Tolak Penarikan?</h3>
                                                            <p class="text-[10px] text-gray-400 leading-relaxed mb-8 text-center uppercase tracking-widest">Penarikan <span class="text-white">Rp {{ number_format($w->amount, 0, ',', '.') }}</span> akan ditolak dan saldo tidak otomatis dikembalikan (proses manual untuk refund).</p>
                                                            
                                                            <div class="flex justify-center gap-4">
                                                                <button @click="showReject = false" class="px-6 py-2.5 rounded-lg border border-gray-700 text-gray-400 hover:bg-gray-800 text-[10px] font-black uppercase tracking-widest transition-all">BATAL</button>
                                                                <form action="{{ route('admin.withdrawals.update', [$w->id, 'reject']) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-red-600 text-white hover:bg-red-500 text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_4px_15px_rgba(220,38,38,0.3)]">TOLAK</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-gray-600 font-bold uppercase tracking-widest">{{ $w->updated_at->format('d/m/Y') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-600 font-bold uppercase tracking-widest text-xs">Tidak ada permintaan penarikan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    {{ $withdrawals->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
