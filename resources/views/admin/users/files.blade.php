<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            User Files: <span class="text-[#00ffff]">{{ $user->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header / Breadcrumbs -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.list') }}" class="w-10 h-10 rounded-lg bg-[#222] hover:bg-[#333] flex items-center justify-center text-gray-400 hover:text-white transition shadow-lg border border-[#333]">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    
                    <div class="flex items-center gap-2 px-4 py-2 bg-[#1a1a1a] rounded-lg border border-[#333]">
                        <a href="{{ route('admin.users.files', $user->id) }}" class="text-xs font-bold {{ !$currentFolder ? 'text-[#00ffff]' : 'text-gray-500 hover:text-gray-300' }}">ROOT</a>
                        @foreach($breadcrumbs as $crumb)
                            <span class="text-gray-700">/</span>
                            <a href="{{ route('admin.users.files.folder', ['id' => $user->id, 'folderId' => $crumb->id]) }}" class="text-xs font-bold {{ $currentFolder && $currentFolder->id == $crumb->id ? 'text-[#00ffff]' : 'text-gray-500 hover:text-gray-300' }}">
                                {{ $crumb->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="text-xs text-gray-400 font-mono">
                    {{ $videos->count() }} Videos | {{ $folders->count() }} Folders
                </div>
            </div>

            <!-- Folders Grid -->
            @if($folders->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
                @foreach($folders as $folder)
                <a href="{{ route('admin.users.files.folder', ['id' => $user->id, 'folderId' => $folder->id]) }}" class="group relative bg-[#1a1a1a] border border-[#333] hover:border-[#00ffff]/50 rounded-xl p-4 flex flex-col items-center justify-center gap-3 transition-all hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(0,255,255,0.1)]">
                    <div class="w-12 h-12 bg-[#00ffff]/10 rounded-full flex items-center justify-center group-hover:bg-[#00ffff]/20 transition-colors">
                        <i class="fa-solid fa-folder text-2xl text-[#00ffff] group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div class="text-center w-full">
                        <h3 class="text-xs font-bold text-gray-300 group-hover:text-white truncate w-full uppercase tracking-wider">{{ $folder->name }}</h3>
                        <p class="text-[10px] text-gray-600">{{ $folder->videos->count() }} Video</p>
                    </div>
                </a>
                @endforeach
            </div>
            @endif

            <!-- Videos Grid -->
            @if($videos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($videos as $video)
                <div class="bg-[#1a1a1a] border border-[#333] rounded-xl overflow-hidden group hover:border-[#00ffff]/30 transition-all">
                    <!-- Thumbnail -->
                    <div class="relative w-full aspect-video bg-black overflow-hidden">
                        <video src="{{ asset('uploads/videos/' . $video->filename) }}#t=1" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" preload="metadata"></video>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                        <div class="absolute bottom-2 right-2 text-[10px] bg-black/60 text-white px-1.5 py-0.5 rounded font-bold border border-white/10">
                            {{ $video->duration ? gmdate("H:i:s", $video->duration) : 'HD' }}
                        </div>
                         <a href="{{ route('videos.show', $video->slug) }}" target="_blank" class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/40 backdrop-blur-[2px]">
                            <i class="fa-solid fa-play text-white text-3xl drop-shadow-lg scale-90 group-hover:scale-100 transition-transform"></i>
                        </a>
                    </div>
                    
                    <!-- Info -->
                    <div class="p-4">
                        <h4 class="text-sm font-bold text-gray-200 truncate mb-1" title="{{ $video->title }}">{{ $video->title ?? $video->filename }}</h4>
                        <div class="flex items-center justify-between text-[10px] text-gray-500 uppercase tracking-wider">
                            <span><i class="fa-regular fa-eye mr-1"></i> {{ number_format($video->views) }}</span>
                            <span>{{ $video->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <div class="mt-4 flex gap-2">
                             <a href="{{ route('videos.show', $video->slug) }}" target="_blank" class="flex-1 bg-[#222] hover:bg-[#333] text-gray-300 py-1.5 rounded text-[10px] font-bold text-center border border-[#333] transition">
                                VIEW
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @elseif($folders->count() == 0)
                <div class="flex flex-col items-center justify-center py-20 text-gray-600">
                    <i class="fa-regular fa-folder-open text-4xl mb-3 opacity-50"></i>
                    <p class="text-xs font-bold uppercase tracking-widest">No files found</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
