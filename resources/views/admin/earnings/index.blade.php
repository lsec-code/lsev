<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="max-w-full mx-auto space-y-8">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-[#222]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-yellow-900/20 flex items-center justify-center border border-yellow-500/20">
                        <i class="fa-solid fa-wallet text-yellow-500 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Earnings Overview</h1>
                        <p class="text-gray-400 text-sm">User earnings and balance statistics</p>
                    </div>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl">
                    <div class="text-gray-400 text-xs font-bold uppercase mb-2">Total Earnings</div>
                    <div class="text-2xl font-bold text-yellow-500">Rp {{ number_format($stats['total_earnings'], 0, ',', '.') }}</div>
                    <p class="text-[10px] text-gray-600 mt-1">Balance + Withdrawn</p>
                </div>
                
                <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl">
                    <div class="text-gray-400 text-xs font-bold uppercase mb-2">Current Balance</div>
                    <div class="text-2xl font-bold text-green-500">Rp {{ number_format($stats['total_balance'], 0, ',', '.') }}</div>
                    <p class="text-[10px] text-gray-600 mt-1">Available to withdraw</p>
                </div>
                
                <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl">
                    <div class="text-gray-400 text-xs font-bold uppercase mb-2">Total Withdrawn</div>
                    <div class="text-2xl font-bold text-blue-500">Rp {{ number_format($stats['total_withdrawn'], 0, ',', '.') }}</div>
                    <p class="text-[10px] text-gray-600 mt-1">Pending + Approved</p>
                </div>
                
                <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl">
                    <div class="text-gray-400 text-xs font-bold uppercase mb-2">Users with Balance</div>
                    <div class="text-2xl font-bold text-purple-500">{{ number_format($stats['users_with_balance']) }}</div>
                    <p class="text-[10px] text-gray-600 mt-1">Balance > Rp 0</p>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-[#0a0a0a] border border-[#222] rounded-xl shadow-[0_0_50px_rgba(0,0,0,0.3)] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#222] flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">Top Earners (Balance > 0)</h3>
                    <span class="px-3 py-1 bg-yellow-500/10 border border-yellow-500/20 text-yellow-500 rounded text-xs font-bold">
                        {{ $users->total() }} Users
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#111] border-b border-[#222]">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Rank</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Balance</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Videos</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Total Views</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#222]">
                            @forelse($users as $index => $user)
                                <tr class="hover:bg-[#111] transition">
                                    <!-- Rank -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @if($index == 0)
                                                <div class="w-8 h-8 rounded-full bg-yellow-500/20 flex items-center justify-center border border-yellow-500/50">
                                                    <i class="fa-solid fa-crown text-yellow-500 text-sm"></i>
                                                </div>
                                            @elseif($index == 1)
                                                <div class="w-8 h-8 rounded-full bg-gray-400/20 flex items-center justify-center border border-gray-400/50">
                                                    <i class="fa-solid fa-medal text-gray-400 text-sm"></i>
                                                </div>
                                            @elseif($index == 2)
                                                <div class="w-8 h-8 rounded-full bg-orange-600/20 flex items-center justify-center border border-orange-600/50">
                                                    <i class="fa-solid fa-medal text-orange-600 text-sm"></i>
                                                </div>
                                            @else
                                                <span class="text-gray-500 font-bold text-sm">{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- User -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $user->getAvatarUrl() }}" class="w-10 h-10 rounded-full border border-[#333]" alt="Avatar">
                                            <div>
                                                <p class="text-white font-medium text-sm">{{ $user->username }}</p>
                                                <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Balance -->
                                    <td class="px-6 py-4">
                                        <div class="text-yellow-500 font-bold text-lg">
                                            Rp {{ number_format($user->balance, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    <!-- Videos -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                                            <i class="fa-solid fa-video text-purple-500"></i>
                                            <span>{{ number_format($user->videos_count) }} videos</span>
                                        </div>
                                    </td>

                                    <!-- Total Views -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                                            <i class="fa-solid fa-eye text-blue-500"></i>
                                            <span>{{ number_format($user->videos_sum_views ?? 0) }} views</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <i class="fa-solid fa-wallet text-4xl mb-3 text-[#222]"></i>
                                            <p class="text-sm">No users with balance found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-[#222]">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
