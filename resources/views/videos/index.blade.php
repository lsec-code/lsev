<x-app-layout>
    <script>
        window.videoPageData = {
            allVideoIds: @json($videos->pluck('id')),
            videoUrls: @json($videos->mapWithKeys(function($item) { return [$item->id => route('videos.show', $item->slug)]; }))
        };
    </script>

    <div class="max-w-7xl mx-auto px-4 md:px-6 space-y-6 md:space-y-8 min-h-screen text-white" x-data="videoManager(window.videoPageData)">
        
        <!-- ADS: User Videos Page -->
        {!! \App\Models\SiteSetting::where('setting_key', 'ad_script_user_videos')->value('setting_value') !!}

        <!-- RENAME FOLDER MODAL -->
        <div x-show="showRenameFolderModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div @click.away="showRenameFolderModal = false" class="bg-[#0a0a0a]/90 backdrop-blur-xl border border-[#FFC107]/30 w-full max-w-md rounded-xl shadow-[0_0_50px_rgba(255,193,7,0.15)] p-8 animate-fade-in relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#FFC107]/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="relative z-10">
                    <h3 class="text-xl font-black text-white mb-6 uppercase tracking-wider flex items-center gap-3">
                        <i class="fa-solid fa-pen text-[#FFC107]"></i> Ubah Nama Folder
                    </h3>
                    <form :action="'/folders/' + folderRenameId" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-6">
                            <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2">NAMA BARU FOLDER</label>
                            <input type="text" name="name" x-model="folderRenameName" class="w-full bg-[#111] border border-gray-800 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-[#FFC107] focus:shadow-[0_0_15px_rgba(255,193,7,0.2)] transition-all font-medium placeholder-gray-700" required>
                        </div>
                        
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showRenameFolderModal = false" class="px-6 py-2.5 rounded-lg border border-gray-700 text-gray-400 hover:bg-gray-800 text-[10px] font-black uppercase tracking-widest transition-all">Batal</button>
                            <button type="submit" class="px-6 py-2.5 rounded-lg bg-[#FFC107] text-black hover:bg-[#ffcd38] text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(255,193,7,0.3)]">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- DELETE FOLDER MODAL -->
        <div x-show="showDeleteFolderModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div @click.away="showDeleteFolderModal = false" class="bg-[#0a0a0a]/90 backdrop-blur-xl border border-red-600/30 w-full max-w-sm rounded-xl shadow-[0_0_50px_rgba(220,38,38,0.15)] p-8 animate-fade-in relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-red-600/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-full bg-red-600/10 flex items-center justify-center border border-red-600/20 mb-6 mx-auto shadow-[0_0_15px_rgba(220,38,38,0.2)]">
                        <i class="fa-solid fa-trash-can text-xl text-red-500"></i>
                    </div>
                    
                    <h3 class="text-lg font-black text-white uppercase tracking-wider mb-2 text-center">Hapus Folder?</h3>
                    <p class="text-xs text-center text-gray-400 leading-relaxed mb-1">Anda akan menghapus folder <span x-text="folderDeleteName" class="font-bold text-white"></span>.</p>
                    <p class="text-xs text-center text-gray-500 italic mb-8">Video di dalamnya akan dipindahkan ke root (tidak dihapus).</p>
                    
                    <div class="flex justify-center gap-4">
                        <button @click="showDeleteFolderModal = false" class="px-6 py-2.5 rounded-lg border border-gray-700 text-gray-400 hover:bg-gray-800 text-[10px] font-black uppercase tracking-widest transition-all">BATAL</button>
                        <form :action="'/folders/' + folderDeleteId" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-6 py-2.5 rounded-lg bg-red-600 text-white hover:bg-red-500 text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_4px_15px_rgba(220,38,38,0.3)]">YA, HAPUS</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- VIDEO GRID -->

        <!-- HEADER & ACTIONS -->
        <div class="space-y-6">
            <!-- Title Section -->
            <div>
                 <h1 class="text-2xl md:text-3xl font-black text-white mb-1 md:mb-2 tracking-tight">Video Saya</h1>
                 <p class="text-gray-400 text-[10px] md:text-sm font-medium">Kelola koleksi video anda: <span class="text-[#00ffff] font-bold">{{ $folders->count() }} folder</span>, <span class="text-[#00ffff] font-bold">{{ $videos->total() }} video</span></p>
            </div>
            
            <!-- Controls Toolbar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-1">
                
                <!-- Bulk Actions (Left) -->
                <div class="flex items-center gap-2">
                     <button @click="bulkShare()" class="bg-[#111] border border-gray-800 text-gray-400 hover:text-white hover:border-gray-600 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-all shadow-sm hover:shadow-lg hover:-translate-y-0.5">
                        <i class="fa-solid fa-share-nodes"></i> Bagikan
                    </button>
                    <button @click="openBulkMove()" class="bg-[#111] border border-gray-800 text-gray-400 hover:text-white hover:border-gray-600 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-all shadow-sm hover:shadow-lg hover:-translate-y-0.5">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Pindahkan
                    </button>
                     <button @click="bulkDelete()" class="bg-red-500/5 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-all shadow-sm hover:shadow-[0_0_15px_rgba(220,38,38,0.4)] hover:-translate-y-0.5">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </div>

                <!-- Primary Actions (Right) -->
                <div class="flex items-center gap-2 md:gap-3">
                    <button @click="showFolderModal = true" class="flex-1 md:flex-none bg-[#FFC107]/10 hover:bg-[#FFC107] text-[#FFC107] hover:text-black border border-[#FFC107]/50 hover:border-[#FFC107] font-black px-4 md:px-6 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-[0_0_15px_rgba(255,193,7,0.1)]">
                        <i class="fa-solid fa-folder-plus"></i> <span class="text-[9px] md:text-[10px] uppercase tracking-widest">Folder</span>
                    </button>
                    <a href="{{ route('videos.index') }}" class="flex-1 md:flex-none bg-green-500/10 hover:bg-green-500 text-green-500 hover:text-black border border-green-500/50 hover:border-green-500 font-black px-4 md:px-6 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-[0_0_15px_rgba(34,197,94,0.1)]">
                        <i class="fa-solid fa-rotate"></i> <span class="text-[9px] md:text-[10px] uppercase tracking-widest">Ref</span>
                    </a>
                </div>

            </div>
        </div>

        <!-- FOLDERS GRID -->
        @if($folders->count() > 0 && !isset($currentFolder))
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            @foreach($folders as $folder)
            <div class="group relative bg-[#111]/40 border border-[#222] hover:border-[#FFC107]/50 rounded-xl p-4 flex flex-col items-center justify-center gap-3 transition-all hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(255,193,7,0.1)]">
                <a href="{{ route('videos.index', ['folder_id' => $folder->id]) }}" class="absolute inset-0 z-0"></a>
                
                <div class="w-12 h-12 bg-[#FFC107]/10 rounded-full flex items-center justify-center group-hover:bg-[#FFC107]/20 transition-colors relative z-10 pointer-events-none">
                    <i class="fa-solid fa-folder text-2xl text-[#FFC107] group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="text-center w-full relative z-10 pointer-events-none">
                    <h3 class="text-xs font-bold text-gray-300 group-hover:text-white truncate w-full uppercase tracking-wider">{{ $folder->name }}</h3>
                    <p class="text-[10px] text-gray-600">{{ $folder->videos->count() }} Video</p>
                </div>

                <!-- Folder Actions (Always Visible) -->
                <div class="absolute top-2 right-2 flex gap-1 z-20">
                    <button @click.prevent="openRenameFolder({{ $folder->id }}, '{{ addslashes($folder->name) }}')" class="w-6 h-6 rounded bg-[#111] border border-gray-700 hover:border-[#FFC107] text-gray-400 hover:text-[#FFC107] flex items-center justify-center text-[10px] transition-colors shadow-black/80 shadow-md" title="Rename Folder">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button @click.prevent="openDeleteFolder({{ $folder->id }}, '{{ addslashes($folder->name) }}', {{ $folder->videos->count() }})" class="w-6 h-6 rounded bg-[#111] border border-gray-700 hover:border-red-500 text-gray-400 hover:text-red-500 flex items-center justify-center text-[10px] transition-colors shadow-black/80 shadow-md" title="Delete Folder">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @elseif(isset($currentFolder))
        <div class="flex items-center gap-2 mb-6">
            <a href="{{ route('videos.index') }}" class="text-xs font-bold text-gray-500 hover:text-white flex items-center gap-1 transition-colors">
                <i class="fa-solid fa-arrow-left"></i> KEMBALI
            </a>
            <span class="text-gray-700">/</span>
            <span class="text-xs font-black text-[#FFC107] uppercase tracking-wider"><i class="fa-solid fa-folder-open mr-1"></i> {{ $currentFolder->name }}</span>
        </div>
        @endif

        <!-- VIDEOS TABLE -->
        @if($videos->count() > 0)
            <div class="bg-[#111]/80 backdrop-blur-md border border-[#00ffff]/20 rounded-xl overflow-hidden shadow-[0_0_30px_rgba(0,0,0,0.3)]">
                <!-- Table Header -->
                <div class="grid grid-cols-12 gap-4 p-5 text-[10px] font-black uppercase tracking-widest text-gray-500 border-b border-gray-800 bg-[#0f0f0f]/80">
                    <div class="col-span-1 text-center">
                        <input type="checkbox" 
                            class="rounded bg-[#222] border-[#444] text-[#00ffff] focus:ring-0 focus:ring-offset-0 w-4 h-4 cursor-pointer" 
                            @change="toggleAll($event)"
                            :checked="selectedVideos.length === {{ $videos->count() }} && {{ $videos->count() }} > 0"
                        >
                    </div>
                    <div class="col-span-11 md:col-span-5">File Video</div>
                    <div class="col-span-0 md:col-span-2 hidden md:block">Ukuran</div>
                    <div class="col-span-0 md:col-span-2 hidden md:block">Tanggal Upload</div>
                    <div class="col-span-0 md:col-span-1 hidden md:block">Views</div>
                    <div class="col-span-0 md:col-span-1 hidden md:block text-right">Aksi</div>
                </div>

                <!-- Table Body -->
                <div class="divide-y divide-gray-800/50">
                    @foreach($videos as $video)
                    <div class="grid grid-cols-12 gap-6 p-4 items-center hover:bg-[#00ffff]/5 transition-colors group relative">
                        <div class="col-span-1 text-center">
                             <input type="checkbox" 
                                class="rounded bg-[#222] border-[#444] text-[#00ffff] focus:ring-0 focus:ring-offset-0 w-4 h-4 cursor-pointer" 
                                :value="{{ $video->id }}" 
                                x-model="selectedVideos"
                            >
                        </div>
                        
                        <!-- Title & Thumbnail Column -->
                        <div class="col-span-11 md:col-span-5 flex items-center gap-5">
                            <!-- Clickable Thumbnail Container -->
                            <a href="{{ route('videos.show', $video->slug) }}" 
                               class="relative w-32 h-20 bg-black rounded-lg overflow-hidden flex-shrink-0 border border-[#222] group-hover:border-[#00ffff]/50 group-hover:shadow-[0_0_15px_rgba(0,255,255,0.2)] transition-all duration-300 flex items-center justify-center group/thumb">
                                
                                <video 
                                    src="{{ asset('uploads/videos/' . $video->filename) }}#t=1" 
                                    class="w-full h-full object-cover opacity-80 group-hover/thumb:opacity-100 transition-opacity" 
                                    muted 
                                    preload="metadata"
                                    onmouseover="this.play()" 
                                    onmouseout="this.pause();this.currentTime=1;"
                                ></video>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-60"></div>
                                <div class="absolute bottom-1 right-2 text-[10px] font-bold text-white bg-black/60 px-1.5 py-0.5 rounded backdrop-blur-sm border border-white/10">HD</div>
                            </a>

                            <div class="overflow-hidden space-y-2">
                                 <!-- Clickable Title -->
                                <a href="{{ $video->status === 'active' ? route('videos.show', $video->slug) : 'javascript:void(0)' }}" class="text-gray-200 font-bold text-sm block truncate hover:text-[#00ffff] transition-colors tracking-wide">
                                    {{ $video->title ?? $video->filename }}
                                    @if($video->status === 'processing')
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black bg-amber-500/10 border border-amber-500/20 text-amber-500 uppercase">Dalam Proses</span>
                                    @endif
                                </a>
                                
                                <!-- Copy Link Button & Metadata -->
                                <div class="flex items-center gap-2">
                                    <button 
                                        @click="copyToClipboard('{{ route('videos.show', $video->slug) }}')"
                                        class="text-[10px] bg-[#1a1a1a] hover:bg-[#111] border border-[#222] text-[#00ffff] px-2.5 py-1 rounded-md flex items-center gap-1.5 transition-colors font-bold uppercase tracking-wider hover:border-[#00ffff]/30"
                                        title="Salin Link Video"
                                    >
                                        <i class="fa-regular fa-copy"></i> Salin Link
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-0 md:col-span-2 hidden md:block text-xs font-mono text-gray-400">
                            @if($video->file_size)
                                {{ number_format($video->file_size / 1048576, 2) }} MB
                            @else
                                <span class="text-gray-600">N/A</span>
                            @endif
                        </div>
                        
                        <div class="col-span-0 md:col-span-2 hidden md:block text-xs font-medium text-gray-400">
                            {{ $video->created_at->format('d M Y') }}
                            <div class="text-[10px] text-gray-600">{{ $video->created_at->format('H:i') }} WIB</div>
                        </div>
                        
                        <div class="col-span-0 md:col-span-1 hidden md:block">
                             <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-[10px] font-bold text-blue-400">
                                <i class="fa-regular fa-eye"></i> {{ $video->views }}
                             </span>
                        </div>

                                <!-- Actions Column -->
                                <div class="col-span-0 md:col-span-1 hidden md:block text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button 
                                            @click="openRename({{ $video->id }}, '{{ addslashes($video->title) }}')"
                                            class="bg-blue-600/10 hover:bg-blue-600/20 text-blue-500 border border-blue-500/30 px-2.5 py-2 rounded-lg text-[10px] hover:shadow-[0_0_10px_rgba(59,130,246,0.2)] transition-all"
                                            title="Rename"
                                        >
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        
                                        <div x-data="{ showDeleteConfirm: false }">
                                            <button @click="showDeleteConfirm = true" class="bg-red-600/10 hover:bg-red-600/20 text-red-500 border border-red-600/30 px-2.5 py-2 rounded-lg text-[10px] hover:shadow-[0_0_10px_rgba(220,38,38,0.2)] transition-all" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                            <!-- Custom Delete Confirmation Modal -->
                                            <template x-teleport="body">
                                                <div x-show="showDeleteConfirm" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-cloak>
                                                    <div @click.away="showDeleteConfirm = false" class="bg-[#0a0a0a]/90 backdrop-blur-xl border border-red-600/30 w-full max-w-sm rounded-xl shadow-[0_0_50px_rgba(220,38,38,0.15)] p-8 animate-fade-in relative overflow-hidden">
                                                        <div class="absolute top-0 right-0 w-32 h-32 bg-red-600/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                                                        
                                                        <div class="relative z-10">
                                                            <div class="w-12 h-12 rounded-full bg-red-600/10 flex items-center justify-center border border-red-600/20 mb-6 mx-auto shadow-[0_0_15px_rgba(220,38,38,0.2)]">
                                                                <i class="fa-solid fa-trash-can text-xl text-red-500"></i>
                                                            </div>
                                                            
                                                            <h3 class="text-lg font-black text-white uppercase tracking-wider mb-2 text-center">Hapus Video?</h3>
                                                            <p class="text-xs text-center text-gray-400 leading-relaxed mb-8">Video ini akan dihapus permanen dari server dan tidak dapat dikembalikan.</p>
                                                            
                                                            <div class="flex justify-center gap-4">
                                                                <button @click="showDeleteConfirm = false" class="px-6 py-2.5 rounded-lg border border-gray-700 text-gray-400 hover:bg-gray-800 text-[10px] font-black uppercase tracking-widest transition-all">BATAL</button>
                                                                <form action="{{ route('videos.destroy', $video->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-red-600 text-white hover:bg-red-500 text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_4px_15px_rgba(220,38,38,0.3)]">YA, HAPUS</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                </div>
            
            <div class="mt-8">
                {{ $videos->links() }}
            </div>
        @else
            <!-- EMPTY STATE -->
            <div class="flex flex-col items-center justify-center py-24 bg-[#111]/50 border border-dashed border-[#222] rounded-xl backdrop-blur-sm hover:border-[#00ffff]/30 transition-all group">
                <div class="w-24 h-24 bg-black rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500 border border-[#222] group-hover:border-[#00ffff]/30 shadow-[0_0_30px_rgba(0,0,0,0.5)] group-hover:shadow-[0_0_30px_rgba(0,255,255,0.1)]">
                    <i class="fa-solid fa-folder-open text-5xl text-gray-700 group-hover:text-[#00ffff] transition-colors duration-300"></i>
                </div>
                <h3 class="text-xl font-black text-white tracking-tight mb-2">Tidak ada video atau folder</h3>
                <p class="text-gray-500 text-sm font-medium">Upload video pertama Anda untuk memulai koleksi.</p>
                <a href="{{ route('videos.create') }}" class="mt-8 px-8 py-3 bg-[#FFC107] hover:bg-[#ffcd38] text-black font-black uppercase tracking-widest rounded-lg shadow-[0_0_20px_rgba(255,193,7,0.3)] hover:shadow-[0_0_30px_rgba(255,193,7,0.5)] transition-all transform hover:-translate-y-1">
                    Upload Video Sekarang <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        @endif

        <!-- RENAME MODAL -->
        <div x-show="showRenameModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div @click.away="showRenameModal = false" class="bg-[#0a0a0a]/90 backdrop-blur-xl border border-[#00ffff]/30 w-full max-w-md rounded-xl shadow-[0_0_50px_rgba(0,255,255,0.15)] p-8 animate-fade-in relative overflow-hidden">
                 <!-- Background Glow -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#00ffff]/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>

                <div class="relative z-10">
                    <h3 class="text-xl font-black text-white mb-6 uppercase tracking-wider flex items-center gap-3">
                        <i class="fa-solid fa-pen-to-square text-[#00ffff]"></i> Rename Video
                    </h3>
                    <form :action="'/videos/' + renameId" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-6">
                            <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2">JUDUL BARU</label>
                            <input type="text" name="title" x-model="renameTitle" class="w-full bg-black border border-[#222] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-[#00ffff] focus:shadow-[0_0_15px_rgba(0,255,255,0.2)] transition-all font-medium placeholder-gray-700">
                        </div>
                        
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showRenameModal = false" class="px-6 py-2.5 rounded-lg border border-gray-700 text-gray-400 hover:bg-gray-800 text-[10px] font-black uppercase tracking-widest transition-all">Batal</button>
                            <button type="submit" class="px-6 py-2.5 rounded-lg bg-[#00ffff] text-black hover:bg-cyan-300 text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(0,255,255,0.3)]">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- BULK MOVE MODAL -->
        <div x-show="showBulkMoveModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div @click.away="showBulkMoveModal = false" class="bg-[#111]/90 backdrop-blur-xl border border-[#00ffff]/30 w-full max-w-md rounded-xl shadow-[0_0_50px_rgba(0,255,255,0.15)] p-8 animate-fade-in relative overflow-hidden">
                <!-- Background Glow -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#00ffff]/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="relative z-10">
                    <h3 class="text-xl font-black text-white mb-6 uppercase tracking-wider flex items-center gap-3">
                        <i class="fa-solid fa-folder-tree text-[#00ffff]"></i> Pindahkan Video
                    </h3>
                    
                    <div class="mb-6">
                        <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2">PILIH FOLDER TUJUAN</label>
                        <select x-model="moveTargetFolderId" class="w-full bg-black border border-[#222] text-white rounded-lg px-4 py-3 focus:outline-none focus:border-[#00ffff] focus:shadow-[0_0_15px_rgba(0,255,255,0.2)] transition-all font-medium appearance-none">
                            <option value="">(Tanpa Folder / Root)</option>
                            @foreach($folders as $folder)
                                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showBulkMoveModal = false" class="px-6 py-2.5 rounded-lg border border-gray-700 text-gray-400 hover:bg-gray-800 text-[10px] font-black uppercase tracking-widest transition-all">Batal</button>
                        <button type="button" @click="submitBulkMove()" class="px-6 py-2.5 rounded-lg bg-[#00ffff] text-black hover:bg-cyan-300 text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(0,255,255,0.3)]">Pindahkan</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CREATE FOLDER MODAL -->
        <div x-show="showFolderModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
             <!-- ... existing content ... -->
            <div @click.away="showFolderModal = false" class="bg-[#0a0a0a]/90 backdrop-blur-xl border border-[#FFC107]/30 w-full max-w-md rounded-xl shadow-[0_0_50px_rgba(255,193,7,0.15)] p-8 animate-fade-in relative overflow-hidden">
                 <!-- Background Glow -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#FFC107]/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>

                <div class="relative z-10">
                    <h3 class="text-xl font-black text-white mb-6 uppercase tracking-wider flex items-center gap-3">
                        <i class="fa-solid fa-folder-plus text-[#FFC107]"></i> Buat Folder Baru
                    </h3>
                    <form action="{{ route('folders.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2">NAMA FOLDER</label>
                            <input type="text" name="name" class="w-full bg-[#111] border border-gray-800 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-[#FFC107] focus:shadow-[0_0_15px_rgba(255,193,7,0.2)] transition-all font-medium placeholder-gray-700" placeholder="Contoh: Tutorial Laravel" required>
                        </div>
                        
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showFolderModal = false" class="px-6 py-2.5 rounded-lg border border-gray-700 text-gray-400 hover:bg-gray-800 text-[10px] font-black uppercase tracking-widest transition-all">Batal</button>
                            <button type="submit" class="px-6 py-2.5 rounded-lg bg-[#FFC107] text-black hover:bg-[#ffcd38] text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(255,193,7,0.3)]">Buat Folder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- SECURITY VERIFICATION MODAL -->
        <div x-show="showSecurityModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div @click.away="showSecurityModal = false" class="bg-[#0a0a0a]/90 backdrop-blur-xl border border-red-600/30 w-full max-w-sm rounded-xl shadow-[0_0_50px_rgba(220,38,38,0.15)] p-8 animate-fade-in relative overflow-hidden">
                 <!-- Background Glow -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-red-600/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>

                <div class="relative z-10">
                    <h3 class="text-xl font-black text-white mb-4 uppercase tracking-wider flex items-center gap-3 justify-center text-center">
                        <i class="fa-solid fa-shield-halved text-red-600"></i> Verifikasi Keamanan
                    </h3>
                    
                    <form @submit.prevent="submitSecurityCheck" class="space-y-4">
                        <div>
                            <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2">KODE KEAMANAN</label>
                            <input type="password" x-model="securityCodeInput" class="w-full bg-[#111] border border-gray-800 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-red-600 focus:shadow-[0_0_15px_rgba(220,38,38,0.2)] transition-all font-medium text-center tracking-widest" placeholder="••••••" required>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showSecurityModal = false" class="px-6 py-2.5 rounded-lg border border-gray-700 text-gray-400 hover:bg-gray-800 text-[10px] font-black uppercase tracking-widest transition-all">Batal</button>
                            <button type="submit" class="px-6 py-2.5 rounded-lg bg-red-600 text-white hover:bg-red-500 text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(220,38,38,0.3)]">Verifikasi & Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        function videoManager(initialData) {
            return {
                showRenameModal: false, 
                showFolderModal: false,
                showBulkMoveModal: false,
                showRenameFolderModal: false,
                showDeleteFolderModal: false,
                showSecurityModal: false,
                securityCodeInput: '',
                pendingAction: null, // { type: 'folder'|'bulk', id: int }
                renameId: null, 
                renameTitle: '',
                folderRenameId: null,
                folderRenameName: '',
                folderDeleteId: null,
                folderDeleteName: '',
                moveTargetFolderId: '',
                selectedVideos: [],
                videoUrls: initialData.videoUrls,
                allVideoIds: initialData.allVideoIds,

                toggleAll(e) {
                    if (e.target.checked) {
                        this.selectedVideos = this.allVideoIds;
                    } else {
                        this.selectedVideos = [];
                    }
                },
                copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(() => {
                        window.showToast('LINK BERHASIL DISALIN!<br><span class="text-[10px] text-gray-300 font-mono normal-case tracking-normal block mt-1">' + text + '</span>', 'success');
                    });
                },
                // ...
                bulkShare() {
                    if (this.selectedVideos.length === 0) return window.showToast('Pilih video terlebih dahulu', 'warning');
                    let text = '';
                    this.selectedVideos.forEach(id => {
                        if (this.videoUrls[id]) text += this.videoUrls[id] + '\n';
                    });
                    navigator.clipboard.writeText(text).then(() => {
                        window.showToast(this.selectedVideos.length + ' LINK BERHASIL DISALIN!', 'success');
                    });
                },

                // Bulk Actions
                openBulkMove() {
                    if (this.selectedVideos.length === 0) return window.showToast('Pilih video terlebih dahulu', 'warning');
                    this.showBulkMoveModal = true;
                },

                submitBulkMove() {
                    this.submitForm('/videos/bulk-move', {
                        ids: this.selectedVideos,
                        folder_id: this.moveTargetFolderId
                    });
                },

                bulkDelete() {
                    if (this.selectedVideos.length === 0) return window.showToast('Pilih video terlebih dahulu', 'warning');
                    this.pendingAction = { type: 'bulk', action: 'delete' };
                    this.securityCodeInput = '';
                    this.showSecurityModal = true;
                },

                // Folder Actions
                openRenameFolder(id, name) {
                    this.folderRenameId = id;
                    this.folderRenameName = name;
                    this.showRenameFolderModal = true;
                },

                openDeleteFolder(id, name, count) {
                    this.folderDeleteId = id;
                    this.folderDeleteName = name;
                    if (count > 0) {
                        this.pendingAction = { type: 'folder', action: 'delete', id: id };
                        this.securityCodeInput = '';
                        this.showSecurityModal = true;
                    } else {
                        this.showDeleteFolderModal = true;
                    }
                },

                // Video Actions
                openRename(id, title) {
                    this.renameId = id;
                    this.renameTitle = title;
                    this.showRenameModal = true;
                },

                // Security Check
                submitSecurityCheck() {
                    if (this.pendingAction && this.pendingAction.type === 'bulk' && this.pendingAction.action === 'delete') {
                        this.submitForm('/videos/bulk-delete', {
                            ids: this.selectedVideos,
                            security_code: this.securityCodeInput
                        });
                    } else if (this.pendingAction && this.pendingAction.type === 'folder' && this.pendingAction.action === 'delete') {
                        this.submitForm('/folders/' + this.pendingAction.id, {
                            _method: 'DELETE',
                            security_code: this.securityCodeInput
                        });
                    }
                },
                submitForm(action, data) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = action;
                    let csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);
                    for (let key in data) {
                        if (Array.isArray(data[key])) {
                            data[key].forEach(val => {
                                let input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = key + '[]';
                                input.value = val;
                                form.appendChild(input);
                            });
                        } else {
                            let input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = data[key];
                            form.appendChild(input);
                        }
                    }
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
    </script>
</x-app-layout>
