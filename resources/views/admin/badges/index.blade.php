<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="max-w-full mx-auto space-y-8">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-[#222]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-yellow-900/20 flex items-center justify-center border border-yellow-500/20">
                        <i class="fa-solid fa-trophy text-yellow-500 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Badge Management</h1>
                        <p class="text-gray-400 text-sm">Manage user achievements and badges</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.badges.seed') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                            <i class="fa-solid fa-seedling mr-2"></i> Seed Default Badges
                        </button>
                    </form>
                    <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg">
                    <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Badges Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($badges as $badge)
                    <div class="bg-[#0a0a0a] border border-[#222] rounded-xl p-6 hover:border-{{ $badge->color }}-500/50 transition">
                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 rounded-full bg-{{ $badge->color }}-900/20 flex items-center justify-center border border-{{ $badge->color }}-500/20">
                                <i class="fa-solid {{ $badge->icon }} text-{{ $badge->color }}-500 text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-white font-bold text-lg">{{ $badge->name }}</h3>
                                <p class="text-gray-400 text-sm mt-1">{{ $badge->description }}</p>
                                
                                <div class="mt-3 flex items-center gap-2 text-xs">
                                    <span class="px-2 py-1 bg-[#111] border border-[#333] rounded text-gray-400">
                                        {{ ucfirst($badge->requirement_type) }}: {{ number_format($badge->requirement_value) }}
                                    </span>
                                    <span class="px-2 py-1 bg-{{ $badge->color }}-500/10 border border-{{ $badge->color }}-500/20 text-{{ $badge->color }}-400 rounded font-bold">
                                        {{ $badge->users_count }} users
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <i class="fa-solid fa-trophy text-4xl mb-3 text-[#222]"></i>
                        <p class="text-gray-500 text-sm">No badges yet</p>
                        <p class="text-gray-600 text-xs mt-1">Click "Seed Default Badges" to create default badges</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
