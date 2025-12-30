<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="max-w-full mx-auto space-y-8">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-[#222]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-purple-900/20 flex items-center justify-center border border-purple-500/20">
                        <i class="fa-solid fa-video text-purple-500 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">All Videos</h1>
                        <p class="text-gray-400 text-sm">Manage all videos uploaded by users</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-4 py-2 bg-purple-500/10 border border-purple-500/20 text-purple-400 rounded-lg text-sm font-bold">
                        {{ $videos->total() }} Total Videos
                    </span>
                    <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </div>

            <!-- Videos Table -->
            <div class="bg-[#0a0a0a] border border-[#222] rounded-xl shadow-[0_0_50px_rgba(0,0,0,0.3)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#111] border-b border-[#222]">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Video</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Owner</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Folder</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Stats</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Uploaded</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#222]">
                            @forelse($videos as $video)
                                <tr class="hover:bg-[#111] transition">
                                    <!-- Video Info -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="relative w-24 h-16 rounded-lg overflow-hidden bg-[#1a1a1a] border border-[#333] flex-shrink-0">
                                                @if($video->thumbnail)
                                                    <img src="{{ asset('storage/' . $video->thumbnail) }}" class="w-full h-full object-cover" alt="Thumbnail">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <i class="fa-solid fa-video text-gray-600 text-2xl"></i>
                                                    </div>
                                                @endif
                                                <div class="absolute bottom-1 right-1 bg-black/80 px-1.5 py-0.5 rounded text-[10px] text-white font-bold">
                                                    @if($video->duration)
                                                        {{ $video->duration }}
                                                    @else
                                                        --:--
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-white font-medium text-sm truncate max-w-xs" title="{{ $video->title }}">
                                                    {{ $video->title }}
                                                </p>
                                                <p class="text-gray-500 text-xs mt-1">
                                                    ID: {{ $video->id }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Owner -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $video->user->getAvatarUrl() }}" class="w-8 h-8 rounded-full border border-[#333]" alt="Avatar">
                                            <div>
                                                <p class="text-white text-sm font-medium">{{ $video->user->username }}</p>
                                                <p class="text-gray-500 text-xs">{{ $video->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Folder -->
                                    <td class="px-6 py-4">
                                        @if($video->folder)
                                            <div class="flex items-center gap-2 text-yellow-500">
                                                <i class="fa-solid fa-folder text-sm"></i>
                                                <span class="text-sm">{{ $video->folder->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-600 text-sm italic">No folder</span>
                                        @endif
                                    </td>

                                    <!-- Stats -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2 text-gray-400 text-xs">
                                                <i class="fa-solid fa-eye text-blue-500"></i>
                                                <span>{{ number_format($video->views) }} views</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-gray-400 text-xs">
                                                <i class="fa-solid fa-heart text-red-500"></i>
                                                <span>{{ number_format($video->likes) }} likes</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Uploaded -->
                                    <td class="px-6 py-4">
                                        <div class="text-gray-400 text-xs">
                                            <p>{{ $video->created_at->format('d M Y') }}</p>
                                            <p class="text-gray-600">{{ $video->created_at->diffForHumans() }}</p>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('videos.show', $video->slug) }}" target="_blank" class="bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 border border-blue-500/20 px-3 py-1.5 rounded text-xs font-bold transition">
                                                <i class="fa-solid fa-play mr-1"></i> Watch
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <i class="fa-solid fa-video text-4xl mb-3 text-[#222]"></i>
                                            <p class="text-sm">No videos found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($videos->hasPages())
                    <div class="px-6 py-4 border-t border-[#222]">
                        {{ $videos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
