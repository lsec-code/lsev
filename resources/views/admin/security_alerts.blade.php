<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#050505] border border-red-900/30 overflow-hidden shadow-[0_0_50px_rgba(220,38,38,0.05)] sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-black text-red-500 uppercase tracking-wider flex items-center gap-3">
                        <i class="fa-solid fa-shield-halved animate-pulse"></i> Peringatan Keamanan
                    </h2>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">{{ $alerts->total() }} Ancaman Terdeteksi</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-red-900/20">
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Target/User</th>
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Jenis Serangan</th>
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Detail Teknis</th>
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Lokasi & Device</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-red-900/10">
                            @forelse($alerts as $alert)
                                <tr class="hover:bg-red-900/5 transition-colors group">
                                    <td class="py-4">
                                        @if($alert->user)
                                            <div class="font-bold text-white group-hover:text-red-400 transition-colors">{{ $alert->user->username }}</div>
                                            <div class="text-[9px] text-gray-500">{{ $alert->user->email }}</div>
                                        @else
                                            <div class="font-bold text-gray-400 italic">Guest / Unknown</div>
                                        @endif
                                        <div class="text-[8px] text-gray-600 font-mono mt-1">{{ $alert->created_at->format('d/m/Y H:i:s') }}</div>
                                    </td>
                                    <td class="py-4">
                                        <div class="flex flex-col gap-1">
                                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest bg-red-500/10 border border-red-500/30 text-red-500 w-fit">
                                                {{ str_replace('_', ' ', $alert->alert_type) }}
                                            </span>
                                            <span class="text-[10px] font-bold text-red-400/80">{{ $alert->pattern_detected }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <div class="space-y-1">
                                            <div class="text-[10px] text-gray-300 font-mono break-all max-w-xs bg-black/40 p-1.5 rounded border border-gray-800">{{ $alert->url }}</div>
                                            <div class="text-[9px] text-gray-500 italic">{{ Str::limit($alert->user_agent, 50) }}</div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-right">
                                        <div class="inline-flex flex-col items-end gap-1">
                                            <div class="text-[10px] font-black text-white bg-red-900/20 px-2 py-0.5 rounded">{{ $alert->ip_address }}</div>
                                            <div class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">{{ $alert->location ?? 'Unknown Location' }}</div>
                                            <div class="text-[8px] text-gray-600 font-mono">{{ $alert->platform }} / {{ $alert->browser }}</div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-600 font-bold uppercase tracking-widest text-xs italic">Aman. Belum ada ancaman keamanan serius terdeteksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    {{ $alerts->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
