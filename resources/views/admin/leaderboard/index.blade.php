<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="max-w-full mx-auto space-y-8">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-[#222]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-purple-900/20 flex items-center justify-center border border-purple-500/20">
                        <i class="fa-solid fa-ranking-star text-purple-500 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Leaderboard</h1>
                        <p class="text-gray-400 text-sm">Top performers on the platform</p>
                    </div>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>

            <!-- Leaderboards Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Top Earners -->
                <div class="bg-[#0a0a0a] border border-[#222] rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#222] bg-yellow-900/10">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-dollar-sign text-yellow-500"></i>
                            Top Earners
                        </h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($topEarners as $index => $user)
                            <div class="flex items-center gap-3 p-3 bg-[#111] border border-[#222] rounded-lg hover:border-yellow-500/50 transition">
                                <div class="text-2xl font-bold 
                                    @if($index == 0) text-yellow-500
                                    @elseif($index == 1) text-gray-400
                                    @elseif($index == 2) text-orange-600
                                    @else text-gray-600
                                    @endif">
                                    #{{ $index + 1 }}
                                </div>
                                <img src="{{ $user->getAvatarUrl() }}" class="w-10 h-10 rounded-full border border-[#333]" alt="Avatar">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-medium text-sm truncate">{{ $user->username }}</p>
                                    <p class="text-yellow-500 text-xs font-bold">Rp {{ number_format($user->balance, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 text-sm py-8">No data</p>
                        @endforelse
                    </div>
                </div>

                <!-- Top Uploaders -->
                <div class="bg-[#0a0a0a] border border-[#222] rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#222] bg-blue-900/10">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-video text-blue-500"></i>
                            Top Uploaders
                        </h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($topUploaders as $index => $user)
                            <div class="flex items-center gap-3 p-3 bg-[#111] border border-[#222] rounded-lg hover:border-blue-500/50 transition">
                                <div class="text-2xl font-bold 
                                    @if($index == 0) text-yellow-500
                                    @elseif($index == 1) text-gray-400
                                    @elseif($index == 2) text-orange-600
                                    @else text-gray-600
                                    @endif">
                                    #{{ $index + 1 }}
                                </div>
                                <img src="{{ $user->getAvatarUrl() }}" class="w-10 h-10 rounded-full border border-[#333]" alt="Avatar">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-medium text-sm truncate">{{ $user->username }}</p>
                                    <p class="text-blue-500 text-xs font-bold">{{ number_format($user->videos_count) }} videos</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 text-sm py-8">No data</p>
                        @endforelse
                    </div>
                </div>

                <!-- Top Viewed -->
                <div class="bg-[#0a0a0a] border border-[#222] rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#222] bg-purple-900/10">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-eye text-purple-500"></i>
                            Most Viewed
                        </h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($topViewed as $index => $user)
                            <div class="flex items-center gap-3 p-3 bg-[#111] border border-[#222] rounded-lg hover:border-purple-500/50 transition">
                                <div class="text-2xl font-bold 
                                    @if($index == 0) text-yellow-500
                                    @elseif($index == 1) text-gray-400
                                    @elseif($index == 2) text-orange-600
                                    @else text-gray-600
                                    @endif">
                                    #{{ $index + 1 }}
                                </div>
                                <img src="{{ $user->getAvatarUrl() }}" class="w-10 h-10 rounded-full border border-[#333]" alt="Avatar">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-medium text-sm truncate">{{ $user->username }}</p>
                                    <p class="text-purple-500 text-xs font-bold">{{ number_format($user->videos_sum_views ?? 0) }} views</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 text-sm py-8">No data</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
