<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="max-w-full mx-auto space-y-8">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-6 border-[#222]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full 
                        @if($status == 'pending') bg-orange-900/20 border-orange-500/20
                        @elseif($status == 'approved') bg-green-900/20 border-green-500/20
                        @else bg-red-900/20 border-red-500/20
                        @endif
                        flex items-center justify-center border">
                        <i class="fa-solid 
                            @if($status == 'pending') fa-clock text-orange-500
                            @elseif($status == 'approved') fa-check text-green-500
                            @else fa-times text-red-500
                            @endif
                            text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            @if($status == 'pending') Pending Withdrawals
                            @elseif($status == 'approved') Approved Withdrawals
                            @else Rejected Withdrawals
                            @endif
                        </h1>
                        <p class="text-gray-400 text-sm">Withdrawal requests with {{ $status }} status</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-4 py-2 
                        @if($status == 'pending') bg-orange-500/10 border-orange-500/20 text-orange-400
                        @elseif($status == 'approved') bg-green-500/10 border-green-500/20 text-green-400
                        @else bg-red-500/10 border-red-500/20 text-red-400
                        @endif
                        border rounded-lg text-sm font-bold">
                        {{ $withdrawals->total() }} Requests
                    </span>
                    <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl">
                    <div class="text-gray-400 text-xs font-bold uppercase mb-2">Total Amount</div>
                    <div class="text-2xl font-bold 
                        @if($status == 'pending') text-orange-500
                        @elseif($status == 'approved') text-green-500
                        @else text-red-500
                        @endif">
                        Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}
                    </div>
                </div>
                
                <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl">
                    <div class="text-gray-400 text-xs font-bold uppercase mb-2">Total Requests</div>
                    <div class="text-2xl font-bold text-blue-500">{{ number_format($stats['count']) }}</div>
                </div>
            </div>

            <!-- Withdrawals Table -->
            <div class="bg-[#0a0a0a] border border-[#222] rounded-xl shadow-[0_0_50px_rgba(0,0,0,0.3)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#111] border-b border-[#222]">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Payment Details</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#222]">
                            @forelse($withdrawals as $withdrawal)
                                <tr class="hover:bg-[#111] transition">
                                    <!-- User -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $withdrawal->user->getAvatarUrl() }}" class="w-10 h-10 rounded-full border border-[#333]" alt="Avatar">
                                            <div>
                                                <p class="text-white font-medium text-sm">{{ $withdrawal->user->username }}</p>
                                                <p class="text-gray-500 text-xs">{{ $withdrawal->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Amount -->
                                    <td class="px-6 py-4">
                                        <div class="text-yellow-500 font-bold text-lg">
                                            Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    <!-- Payment Details -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <p class="text-white text-xs font-medium">{{ $withdrawal->payment_method ?? 'N/A' }}</p>
                                            <p class="text-gray-500 text-xs">{{ $withdrawal->payment_number ?? 'N/A' }}</p>
                                            <p class="text-gray-600 text-xs">{{ $withdrawal->payment_name ?? 'N/A' }}</p>
                                        </div>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-6 py-4">
                                        <div class="text-gray-400 text-xs">
                                            <p>{{ $withdrawal->created_at->format('d M Y') }}</p>
                                            <p class="text-gray-600">{{ $withdrawal->created_at->diffForHumans() }}</p>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded text-xs font-bold
                                            @if($withdrawal->status == 'pending') bg-orange-500/10 border border-orange-500/20 text-orange-500
                                            @elseif($withdrawal->status == 'approved') bg-green-500/10 border border-green-500/20 text-green-500
                                            @else bg-red-500/10 border border-red-500/20 text-red-500
                                            @endif">
                                            {{ strtoupper($withdrawal->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <i class="fa-solid fa-inbox text-4xl mb-3 text-[#222]"></i>
                                            <p class="text-sm">No {{ $status }} withdrawals found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($withdrawals->hasPages())
                    <div class="px-6 py-4 border-t border-[#222]">
                        {{ $withdrawals->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
