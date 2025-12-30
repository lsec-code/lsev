<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#111] border border-[#222] overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <h2 class="text-xl font-bold text-white uppercase tracking-wider">Log Aktivitas Sistem</h2>
                    </div>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">{{ $logs->total() }} Log Tercatat</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#222]">
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">User</th>
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Aksi</th>
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Deskripsi</th>
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Informasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#222]">
                            @forelse($logs as $log)
                                <tr class="hover:bg-[#1a1a1a]/50 transition-colors">
                                    <td class="py-4">
                                        <div class="font-bold text-gray-200">{{ $log->user->username }}</div>
                                        <div class="text-[9px] text-gray-600 uppercase">{{ $log->created_at->format('d M Y, H:i') }} WIB</div>
                                    </td>
                                    <td class="py-4">
                                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest bg-blue-500/10 border border-blue-500/30 text-blue-400">
                                            {{ str_replace('_', ' ', $log->action) }}
                                        </span>
                                    </td>
                                    <td class="py-4">
                                        <div class="text-[11px] text-gray-300 leading-relaxed max-w-md">{{ $log->description }}</div>
                                    </td>
                                    <td class="py-4 text-right">
                                        <div class="text-[9px] text-gray-500 font-mono">{{ $log->ip_address }}</div>
                                        <div class="text-[8px] text-gray-700 truncate max-w-[100px] inline-block" title="{{ $log->user_agent }}">{{ $log->user_agent }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-600 font-bold uppercase tracking-widest text-xs">Belum ada log aktivitas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
