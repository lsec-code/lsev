<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="max-w-full mx-auto space-y-8">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-[#222]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-900/20 flex items-center justify-center border border-red-500/20">
                        <i class="fa-solid fa-user-slash text-red-500 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Banned Users</h1>
                        <p class="text-gray-400 text-sm">Suspended user accounts</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-4 py-2 bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg text-sm font-bold">
                        {{ $users->total() }} Banned
                    </span>
                    <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-[#0a0a0a] border border-[#222] rounded-xl shadow-[0_0_50px_rgba(0,0,0,0.3)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#111] border-b border-[#222]">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Videos</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Banned Date</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#222]">
                            @forelse($users as $user)
                                <tr class="hover:bg-[#111] transition">
                                    <!-- User -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <img src="{{ $user->getAvatarUrl() }}" class="w-10 h-10 rounded-full border-2 border-red-500" alt="Avatar">
                                                <div class="absolute -bottom-1 -right-1 bg-red-600 text-[8px] font-black text-white px-1.5 py-0.5 rounded border border-[#111]">BAN</div>
                                            </div>
                                            <div>
                                                <p class="text-white font-medium text-sm">{{ $user->username }}</p>
                                                <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Videos -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                                            <i class="fa-solid fa-video text-purple-500"></i>
                                            <span>{{ number_format($user->videos_count) }} videos</span>
                                        </div>
                                    </td>

                                    <!-- Banned Date -->
                                    <td class="px-6 py-4">
                                        <div class="text-gray-400 text-xs">
                                            <p>{{ $user->suspended_at ? $user->suspended_at->format('d M Y') : 'N/A' }}</p>
                                            <p class="text-gray-600">{{ $user->suspended_at ? $user->suspended_at->diffForHumans() : '' }}</p>
                                        </div>
                                    </td>

                                    <!-- Reason -->
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs">
                                            <p class="text-red-400 text-xs">{{ $user->suspension_reason ?? 'No reason provided' }}</p>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <form action="{{ route('admin.bans.unban_user', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-green-500/10 hover:bg-green-500/20 text-green-400 border border-green-500/20 px-3 py-1.5 rounded text-xs font-bold transition">
                                                <i class="fa-solid fa-check mr-1"></i> Unban
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <i class="fa-solid fa-user-check text-4xl mb-3 text-[#222]"></i>
                                            <p class="text-sm">No banned users</p>
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
