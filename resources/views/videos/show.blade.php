<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $video->title }}
        </h2>
    </x-slot>

    <!-- Load Custom Styles -->
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <style>
        .py-12 { padding-top: 20px; }
        .bg-white { background-color: #1a1a1a !important; color: white; border: 1px solid #333; }
        
        /* Player Styles */
        .video-player-wrapper { position: relative; width: 100%; height: 500px; background: black; transition: all 0.3s ease; }
        @media(max-width: 768px) { .video-player-wrapper { height: 250px; } }

        /* Vertical Video Mode */
        .video-player-wrapper.is-vertical {
            max-width: 400px;
            height: 700px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid #333;
        }
        @media(max-width: 768px) { 
            .video-player-wrapper.is-vertical { 
                max-width: 100%; 
                height: 80vh; 
                border-radius: 0;
            } 
        }

        .video-loader {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            z-index: 100; display: none; flex-direction: column; align-items: center; gap: 10px;
            color: white; background: rgba(0,0,0,0.8); padding: 20px; border-radius: 12px; pointer-events: none;
        }
        .spinner {
            width: 40px; height: 40px; border: 4px solid rgba(255,255,255,0.1);
            border-left-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .custom-fs-btn {
            position: absolute; bottom: 20px; right: 20px; z-index: 20;
            background: rgba(0,0,0,0.5); color: white; border: none; border-radius: 4px;
            padding: 5px 10px; cursor: pointer; display: none;
        }
        .video-player-wrapper:hover .custom-fs-btn { display: block; }
        
        /* Hide ALL browser download buttons and overlays */
        video::-webkit-media-controls-fullscreen-button { display: none !important; }
        video::-internal-media-controls-download-button { display: none !important; }
        video::-webkit-media-controls-enclosure { overflow: hidden !important; }
        video::-webkit-media-controls-panel { width: calc(100% + 35px) !important; }
        
        /* Ensure video takes full container */
        #mainVideo {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- ADS: Video Watch Page -->
            {!! \App\Models\SiteSetting::where('setting_key', 'ad_script_video_watch')->value('setting_value') !!}
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="video-container">
                        <div class="video-player-wrapper" id="videoWrapper">
                            <div id="videoLoader" class="video-loader">
                                <div class="spinner"></div>
                                <span>Loading Video...</span>
                            </div>

                            <button id="customFsBtn" class="custom-fs-btn" onclick="toggleCustomFullscreen()">
                                <i class="fa-solid fa-expand"></i>
                            </button>
                            
                            <video id="mainVideo" 
                                   controls 
                                   controlsList="{{ $video->user->allow_download ? 'noplaybackrate nofullscreen' : 'nodownload noplaybackrate nofullscreen' }}" 
                                   disablePictureInPicture 
                                   width="100%" 
                                   height="100%" 
                                   style="background: black;" 
                                   preload="metadata" 
                                   playsinline
                                   crossorigin="anonymous">
                                <source src="{{ asset('uploads/videos/'.$video->filename) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>

                        <div id="videoMeta" class="mt-10 border-t border-gray-800 pt-6 transition-all duration-300">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h1 class="text-3xl font-bold text-white">{{ $video->title }}</h1>
                                    <div class="flex items-center gap-4 text-gray-400 text-sm mt-3">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-eye"></i> 
                                            <span>{{ number_format($video->views) }} views</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-clock"></i> 
                                            <span>{{ $video->created_at->format('d M Y') }}</span>
                                            <span class="text-gray-600">•</span>
                                            <span>{{ $video->created_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($video->user->allow_download)
                                    <div class="flex items-center">
                                        <a href="{{ asset('uploads/videos/'.$video->filename) }}" download="{{ $video->title }}.mp4" class="inline-flex items-center gap-2 bg-[#00ffff] hover:bg-white text-black font-bold px-6 py-2.5 rounded-lg text-sm transition-all shadow-[0_0_15px_rgba(0,255,255,0.2)]">
                                            <i class="fa-solid fa-download"></i>
                                            Download Video
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            
        </div>
    </div>

    <!-- Player Logic Script -->
    <script>
        const videoElement = document.getElementById('mainVideo');
        const loader = document.getElementById('videoLoader');
        const wrapper = document.getElementById('videoWrapper');
        
        // Auto-detect Vertical Video
        videoElement.addEventListener('loadedmetadata', function() {
            const width = this.videoWidth;
            const height = this.videoHeight;
            
            if (height > width) {
                // Vertical detected
                wrapper.classList.add('is-vertical');
                const meta = document.getElementById('videoMeta');
                if(meta) {
                    meta.style.maxWidth = '400px';
                    meta.style.margin = '40px auto 0';
                }
                console.log("Vertical Video Detected: " + width + "x" + height);
            }
        });


        // Loading States - Improved
        videoElement.addEventListener('loadstart', () => {
            console.log('Video loading started');
            loader.style.display = 'flex';
        });
        
        videoElement.addEventListener('waiting', () => {
            console.log('Video waiting/buffering');
            loader.style.display = 'flex';
        });
        
        videoElement.addEventListener('canplay', () => {
            console.log('Video can play');
            loader.style.display = 'none';
        });
        
        videoElement.addEventListener('playing', () => {
            console.log('Video is playing');
            loader.style.display = 'none';
        });
        
        videoElement.addEventListener('loadeddata', () => {
            console.log('Video data loaded');
            loader.style.display = 'none';
        });
        
        videoElement.addEventListener('error', (e) => {
            console.error('Video error:', e);
            loader.style.display = 'none';
        });
        
        function toggleCustomFullscreen() {
            const wrapper = document.getElementById('videoWrapper');
            if (!document.fullscreenElement) {
                wrapper.requestFullscreen().catch(err => {
                   console.log(`Error attempting to enable fullscreen: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }
        
        document.addEventListener('fullscreenchange', () => {
             const btn = document.getElementById('customFsBtn');
             if(document.fullscreenElement) {
                 btn.innerHTML = '<i class="fa-solid fa-compress"></i>';
             } else {
                 btn.innerHTML = '<i class="fa-solid fa-expand"></i>';
             }
        });

        // Active Viewer Heartbeat System
        (function() {
            // Generate unique session ID
            const sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            let heartbeatInterval = null;
            const heartbeatUrl = '{{ route("videos.heartbeat", $video->slug) }}';
            const csrfToken = '{{ csrf_token() }}';

            function sendHeartbeat() {
                fetch(heartbeatUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ session_id: sessionId })
                }).catch(err => console.log('Heartbeat error:', err));
            }

            // Send initial heartbeat
            sendHeartbeat();

            // Start interval (5 seconds)
            heartbeatInterval = setInterval(() => {
                if (document.visibilityState === 'visible') {
                    sendHeartbeat();
                }
            }, 5000);

            // Send heartbeat when tab becomes visible again
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    sendHeartbeat();
                }
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', () => {
                if (heartbeatInterval) {
                    clearInterval(heartbeatInterval);
                }
            });
        })();

        // Unique View Tracking System
        (function() {
            let viewRecorded = false;
            let watchStartTime = null;
            let durationUpdateInterval = null;
            const recordViewUrl = '{{ route("videos.record-view", $video->slug) }}';
            const updateDurationUrl = '{{ route("videos.update-duration", $video->slug) }}';
            const csrfToken = '{{ csrf_token() }}';

            // Record view when video starts playing (first time only)
            videoElement.addEventListener('play', function() {
                if (!viewRecorded) {
                    fetch(recordViewUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        viewRecorded = true;
                        console.log('View tracking:', data.message);
                        
                        // Start watch duration tracking
                        watchStartTime = Date.now();
                        
                        // Update duration every 10 seconds
                        durationUpdateInterval = setInterval(() => {
                            if (!videoElement.paused) {
                                const duration = Math.floor((Date.now() - watchStartTime) / 1000);
                                updateWatchDuration(duration);
                            }
                        }, 10000);
                    })
                    .catch(err => console.log('View tracking error:', err));
                }
            });

            function updateWatchDuration(duration) {
                fetch(updateDurationUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ duration: duration })
                }).catch(err => console.log('Duration update error:', err));
            }

            // Cleanup on page unload
            window.addEventListener('beforeunload', () => {
                if (durationUpdateInterval) {
                    clearInterval(durationUpdateInterval);
                }
                
                // Send final duration update
                if (watchStartTime) {
                    const finalDuration = Math.floor((Date.now() - watchStartTime) / 1000);
                    navigator.sendBeacon(updateDurationUrl, JSON.stringify({
                        duration: finalDuration,
                        _token: csrfToken
                    }));
                }
            });
        })();
    </script>
</x-app-layout>
