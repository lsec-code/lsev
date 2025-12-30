<x-app-layout>
    <div class="w-full px-6" x-data="uploadHandler()">
        
        <!-- UPLOAD AREA -->
        <div class="w-full max-w-4xl mx-auto">
            
            <!-- Dropzone -->
            <div 
                x-show="!isUploading && !isSuccess"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleDrop($event)"
                class="relative border-2 border-dashed border-[#333] bg-[#111]/50 backdrop-blur-sm rounded-2xl p-12 flex flex-col items-center justify-center transition-all duration-300 min-h-[500px] h-auto group"
                :class="{ 'border-[#00ffff] bg-[#00ffff]/10 shadow-[0_0_30px_rgba(0,255,255,0.1)]': isDragging, 'hover:border-gray-600': !isDragging }"
            >
                <!-- Background Decoration -->
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-primary/[0.02] to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none rounded-2xl"></div>

                <div class="relative z-10 flex flex-col items-center w-full max-w-md">
                    <div class="mb-8 w-24 h-24 rounded-full bg-black border border-[#222] flex items-center justify-center group-hover:scale-110 group-hover:border-[#00ffff]/30 group-hover:shadow-[0_0_20px_rgba(0,255,255,0.1)] transition-all duration-300">
                        <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-600 group-hover:text-[#00ffff] transition-colors"></i>
                    </div>
                    
                    <h3 class="text-2xl font-black text-white mb-3 tracking-tight group-hover:text-[#00ffff] transition-colors">
                        Upload Video Baru
                    </h3>
                    <p class="text-gray-500 text-sm mb-8 text-center leading-relaxed">
                        Tarik & lepas file video di sini, atau gunakan tombol di bawah untuk memilih dari perangkat Anda.
                    </p>
                    
                    <!-- Folder Selection -->
                    <div class="mb-6 w-full relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-folder text-gray-600 group-focus-within:text-[#FFC107] transition-colors"></i>
                        </div>
                        <select x-ref="folderSelect" class="w-full bg-[#1a1a1a] border border-[#222] text-gray-300 text-xs font-bold rounded-xl pl-10 pr-4 py-3.5 focus:outline-none focus:border-[#00ffff] focus:shadow-[0_0_15px_rgba(0,255,255,0.1)] cursor-pointer transition-all hover:border-gray-600 appearance-none">
                            <option value="">Upload ke Root (Tanpa Folder)</option>
                            @foreach($folders as $folder)
                                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-xs text-gray-600"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 w-full">
                         <div class="h-px bg-gray-800 flex-1"></div>
                         <span class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">ATAU</span>
                         <div class="h-px bg-gray-800 flex-1"></div>
                    </div>
                    
                    <input type="file" x-ref="fileInput" class="hidden" accept="video/mp4,video/webm,video/ogg" @change="handleFileSelect($event)">
                    
                    <div class="mt-8">
                        <button @click="$refs.fileInput.click()" class="bg-[#00ffff]/10 border border-[#00ffff] text-[#00ffff] hover:bg-[#00ffff] hover:text-black font-black py-4 px-12 rounded-xl text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 shadow-[0_0_20px_rgba(0,255,255,0.1)] hover:shadow-[0_0_40px_rgba(0,255,255,0.4)] flex items-center gap-2 mx-auto uppercase">
                            <i class="fa-solid fa-plus"></i> PILIH FILE VIDEO
                        </button>
                    </div>
                    
                    <p class="mt-6 text-[10px] font-mono text-gray-600 border border-gray-800 rounded-full px-4 py-1">
                        Max Upload 500MB
                    </p>
                </div>
            </div>

            <!-- Upload Progress UI -->
            <div x-show="isUploading" style="display: none;" class="bg-[#111]/80 backdrop-blur-xl border border-[#00ffff]/20 rounded-xl p-8 shadow-[0_0_30px_rgba(0,255,255,0.1)] relative overflow-hidden">
                <!-- Glow -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#00ffff]/5 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

                <div class="flex items-center justify-between mb-6 relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#00ffff]/10 border border-[#00ffff]/30 flex items-center justify-center shadow-[0_0_15px_rgba(0,255,255,0.1)]">
                            <i class="fa-solid fa-video text-[#00ffff] text-xl animate-pulse"></i>
                        </div>
                        <div>
                            <h4 class="text-white font-black text-base tracking-tight mb-1" x-text="fileName">video.mp4</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#00ffff] animate-pulse"></span>
                                Sedang Mengupload...
                            </p>
                        </div>
                    </div>
                    <span class="text-4xl font-black text-[#00ffff] drop-shadow-[0_0_10px_rgba(0,255,255,0.5)]" x-text="progress + '%'">0%</span>
                </div>
                
                <!-- Progress Bar -->
                <div class="w-full bg-black rounded-full h-3 mb-6 overflow-hidden border border-[#222] relative z-10">
                    <div class="bg-gradient-to-r from-primary to-cyan-400 h-full rounded-full transition-all duration-200 shadow-[0_0_20px_rgba(0,255,255,0.4)] relative" :style="'width: ' + progress + '%'">
                        <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                    </div>
                </div>

                <!-- Detailed Stats & Cancel -->
                <div class="flex items-end justify-between relative z-10">
                    <div class="space-y-1">
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Ukuran</div>
                        <div class="text-xs text-gray-300 font-mono">
                            <span x-text="uploadedSize">0 MB</span> / <span x-text="totalSize">0 MB</span>
                        </div>
                    </div>
                    
                    <div class="space-y-1 text-right">
                         <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Kecepatan</div>
                         <div class="text-xs text-[#00ffff] font-mono shadow-cyan-500 drop-shadow-sm" x-text="uploadSpeed">0 MB/s</div>
                    </div>

                    <div class="ml-6">
                         <button @click="abortUpload()" class="group flex items-center gap-2 px-4 py-2 rounded-lg bg-red-500/10 border border-red-500/30 text-red-500 hover:bg-red-500 hover:text-white transition-all text-[10px] font-black uppercase tracking-widest">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </button>
                    </div>
                </div>
            </div>

            <!-- Success UI -->
            <div x-show="isSuccess" style="display: none;" class="bg-[#0a0a0a] border border-green-500/30 rounded-xl p-12 flex flex-col items-center justify-center text-center relative overflow-hidden">
                <!-- Glow Effect -->
                <div class="absolute inset-0 bg-green-500/5 blur-3xl"></div>
                
                <div class="w-20 h-20 bg-green-900/20 rounded-full border border-green-500/30 flex items-center justify-center mb-6 z-10 shadow-[0_0_20px_rgba(34,197,94,0.2)]">
                    <i class="fa-solid fa-check text-4xl text-green-500"></i>
                </div>
                <h3 class="text-2xl font-black text-white mb-2 z-10 tracking-tight">UPLOAD BERHASIL!</h3>
                <p class="text-gray-400 text-sm mb-8 z-10">Video Anda telah berhasil diunggah ke server.</p>
                
                <div class="flex gap-4 z-10">
                    <a href="{{ route('videos.index') }}" class="px-8 py-3 rounded-xl bg-black border border-[#222] text-gray-300 hover:text-white hover:border-gray-500 hover:bg-[#111] text-xs font-bold uppercase tracking-widest transition-all">Lihat Video</a>
                    <button @click="resetForm()" class="px-8 py-3 rounded-xl bg-[#00ffff]/10 border border-[#00ffff]/50 text-[#00ffff] hover:bg-[#00ffff] hover:text-black text-xs font-bold uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(0,255,255,0.1)] hover:shadow-[0_0_30px_rgba(0,255,255,0.4)]">Upload Lagi</button>
                </div>
            </div>

    <!-- Upload Logic Script -->
    <script>
        function uploadHandler() {
            return {
                isDragging: false,
                isUploading: false,
                isSuccess: false,
                progress: 0,
                fileName: '',
                uploadedSize: '0 MB',
                totalSize: '0 MB',
                uploadSpeed: '0 MB/s',
                fileStatus: 'Preparing...',
                xhr: null,
                chunkSize: 2 * 1024 * 1024, // 2MB
                totalChunks: 0,
                currentChunk: 0,
                file: null,
                uuid: '',
                lastSpeedUpdate: 0,
                loadedAtLastSpeedUpdate: 0,

                handleDrop(e) {
                    this.isDragging = false;
                    const files = e.dataTransfer.files;
                    if (files.length > 0) this.validateAndUpload(files[0]);
                },

                handleFileSelect(e) {
                    const files = e.target.files;
                    if (files.length > 0) this.validateAndUpload(files[0]);
                },

                resetForm() {
                    this.isSuccess = false;
                    this.isUploading = false;
                    this.progress = 0;
                    this.$refs.fileInput.value = '';
                },

                abortUpload() {
                    if (this.xhr) {
                        this.xhr.abort();
                        this.isUploading = false;
                        this.fileStatus = 'Upload dibatalkan.';
                        showToast('UPLOAD DIBATALKAN', 'info');
                        this.resetForm();
                    }
                },

                validateAndUpload(file) {
                    // Client-Side Validation using Server Setting
                    @php
                        $maxUploadMB = \App\Models\SiteSetting::where('setting_key', 'max_upload_size')->value('setting_value') ?? 500;
                        $maxUploadBytes = $maxUploadMB * 1024 * 1024;
                    @endphp

                    const maxSizeBytes = {{ $maxUploadBytes }};
                    
                    if (file.size > maxSizeBytes) { 
                        showToast('FILE TERLALU BESAR. Max: {{ $maxUploadMB }}MB', 'error');
                        return;
                    }

                    this.file = file;
                    this.fileName = file.name;
                    this.totalSize = this.formatSize(file.size);
                    this.isUploading = true;
                    this.progress = 0;
                    this.fileStatus = 'Preparing Upload...';
                    this.uuid = Math.random().toString(36).substring(2) + Date.now().toString(36);
                    
                    this.lastSpeedUpdate = (new Date()).getTime();
                    this.loadedAtLastSpeedUpdate = 0;

                    this.totalChunks = Math.ceil(file.size / this.chunkSize);
                    this.currentChunk = 0;
                    
                    this.uploadNextChunk();
                },

                uploadNextChunk() {
                    const start = this.currentChunk * this.chunkSize;
                    const end = Math.min(start + this.chunkSize, this.file.size);
                    const chunk = this.file.slice(start, end);
                    
                    const formData = new FormData();
                    formData.append('file', chunk);
                    formData.append('uuid', this.uuid);
                    formData.append('chunkIndex', this.currentChunk);
                    formData.append('totalChunks', this.totalChunks);
                    formData.append('originalName', this.file.name);
                    formData.append('totalSize', this.file.size); // Send Total Size for Backend Validation
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    // Append Folder ID
                    const folderId = this.$refs.folderSelect.value;
                    if (folderId) {
                        formData.append('folder_id', folderId);
                    }

                    this.xhr = new XMLHttpRequest();
                    const vm = this;
                    
                    // Track chunk progress
                    this.xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                             // Calculate overall progress
                             const chunkProgress = evt.loaded;
                             const totalUploaded = (vm.currentChunk * vm.chunkSize) + chunkProgress;
                             let percentComplete = Math.round((totalUploaded / vm.file.size) * 100);
                             
                             if (percentComplete > 100) percentComplete = 100;
                             
                             // Prevent 100% until final response
                             if (vm.currentChunk === vm.totalChunks - 1 && percentComplete >= 100) {
                                 percentComplete = 99;
                                 vm.fileStatus = 'Finalizing Video...';
                             } else {
                                 // vm.fileStatus = `Uploading Part ${vm.currentChunk + 1}/${vm.totalChunks}`; // REMOVED
                             }
                             
                             vm.progress = percentComplete;
                             vm.uploadedSize = vm.formatSize(totalUploaded);

                             // Speed Calculation
                             const currentTime = (new Date()).getTime();
                             const timeDiff = (currentTime - vm.lastSpeedUpdate) / 1000; // seconds

                             if (timeDiff > 0.5) { // Update every 500ms
                                 const loadedDiff = totalUploaded - vm.loadedAtLastSpeedUpdate;
                                 const speed = loadedDiff / timeDiff; // bytes per second
                                 
                                 vm.uploadSpeed = vm.formatSize(speed) + '/s';
                                 
                                 vm.lastSpeedUpdate = currentTime;
                                 vm.loadedAtLastSpeedUpdate = totalUploaded;
                             }
                        }
                    }, false);

                    this.xhr.addEventListener("load", function(evt) {
                        if (vm.xhr.status === 200) {
                            const response = JSON.parse(vm.xhr.responseText);
                            if (response.success) {
                                vm.currentChunk++;
                                if (vm.currentChunk < vm.totalChunks) {
                                    vm.uploadNextChunk();
                                } else {
                                    // Done
                                    vm.progress = 100;
                                    vm.isUploading = false;
                                    vm.isSuccess = true;
                                    showToast('UPLOAD SELESAI. SEDANG DI PROSES...', 'success');
                                }
                            } else {
                                vm.handleError('Upload Error: ' + (response.error || 'Unknown'));
                            }
                        } else {
                            try {
                                const response = JSON.parse(vm.xhr.responseText);
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                    return;
                                }
                                showToast(response.error || 'UPLOAD GAGAL', 'error');
                            } catch (e) {
                                showToast('SERVER ERROR', 'error');
                            }
                            vm.isUploading = false;
                            vm.fileStatus = 'Gagal.';
                        }
                    }, false);

                    this.xhr.addEventListener("error", function(evt) {
                        vm.handleError('Network Error');
                    }, false);

                    this.xhr.open("POST", "{{ route('videos.store.chunk') }}");
                    this.xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                    this.xhr.send(formData);
                },
                
                handleError(msg) {
                    this.isUploading = false;
                    showToast(msg, 'error');
                },

                formatSize(bytes) {
                    if (bytes === 0) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }
            }
        }
    </script>
</x-app-layout>
