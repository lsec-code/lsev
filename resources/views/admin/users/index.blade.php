@php
    $title = isset($isOnlineView) && $isOnlineView ? 'Online Users' : 'User List';
    $subtitle = isset($isOnlineView) && $isOnlineView ? 'List of users active in the last 5 minutes.' : 'Manage and inspect user files.';
    $searchRoute = isset($isOnlineView) && $isOnlineView ? route('admin.users.online') : route('admin.users.list');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __($title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#0a0a0a] border border-[#222] rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-[#222] flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 bg-[#050505] hover:bg-[#111] border border-[#222] rounded-lg flex items-center justify-center text-gray-400 hover:text-white transition-all shadow-lg group">
                            <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                        </a>
                        <div>
                             <h3 class="text-xl font-black text-white uppercase tracking-wider">{{ $title }}</h3>
                             <p class="text-xs text-gray-500">{{ $subtitle }}</p>
                        </div>
                    </div>
                    
                    <form action="{{ $searchRoute }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                             <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                             <input type="text" name="search" value="{{ request('search') }}" placeholder="Search username or email..." class="w-full bg-[#050505] border border-[#222] text-white text-xs rounded-lg pl-8 pr-4 py-2 focus:outline-none focus:border-[#00ffff] transition-all placeholder-text-muted/50">
                        </div>
                        <button type="submit" class="bg-[#050505] hover:bg-[#111] border border-[#222] text-gray-500 px-3 py-2 rounded-lg text-xs transition-colors hover:text-white">
                            Search
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="bg-[#050505] text-xs uppercase font-bold text-gray-500/60">
                            <tr>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Videos</th>
                                <th class="px-6 py-4 text-center">Views</th>
                                <th class="px-6 py-4">Balance</th>
                                <th class="px-6 py-4">Registered</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-color">
                            @foreach($users as $user)
                            <tr class="hover:bg-[#111] transition">
                                <td class="px-6 py-4 font-mono text-gray-600">#{{ $user->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center overflow-hidden border border-gray-700">
                                            <img src="{{ $user->getAvatarUrl() }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <div class="font-bold text-white">{{ $user->name }}</div>
                                            <div class="text-[10px] text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_suspended)
                                        <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-500 text-[10px] font-bold border border-red-500/20">BANNED</span>
                                    @elseif($user->last_activity_at && $user->last_activity_at->diffInMinutes(now()) < 5)
                                        <span class="px-2 py-0.5 rounded bg-green-500/10 text-green-500 text-[10px] font-bold border border-green-500/20 animate-pulse">ONLINE</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-gray-700 text-gray-400 text-[10px] font-bold">OFFLINE</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold text-white">{{ number_format($user->videos_count) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold text-green-500">{{ number_format($user->videos_sum_views ?? 0) }}</span>
                                </td>
                                <td class="px-6 py-4 font-mono text-yellow-500 font-bold">
                                    Rp {{ number_format($user->balance, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-[10px] text-gray-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.users.files', $user->id) }}" class="inline-flex items-center gap-2 bg-[#00ffff]/10 hover:bg-[#00ffff] text-[#00ffff] hover:text-black border border-[#00ffff] px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(0,255,255,0.1)] hover:shadow-[0_0_25px_rgba(0,255,255,0.3)]">
                                        <i class="fa-solid fa-folder-open"></i> INSPECT FILES
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                <div class="p-6 border-t border-[#333]">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
