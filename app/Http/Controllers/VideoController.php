<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->videos()->latest();
        
        if ($request->has('folder_id') && $request->folder_id != '') {
             $currentFolder = \App\Models\Folder::where('user_id', auth()->id())
                                ->where('id', $request->folder_id)
                                ->first();
             
             if (!$currentFolder) {
                 abort(404); // Trigger the custom "Access Denied" page
             }

             $query->where('folder_id', $request->folder_id);
        } else {
             // Show ONLY root videos (videos not in any folder)
             $query->whereNull('folder_id');
             $currentFolder = null;
        }

        $videos = $query->paginate(15);
        $folders = \App\Models\Folder::where('user_id', auth()->id())->get();
        // $currentFolder is set above
        
        return view('videos.index', compact('videos', 'folders', 'currentFolder'));
    }

    public function create()
    {
        $folders = \App\Models\Folder::where('user_id', auth()->id())->get();
        return view('videos.create', compact('folders'));
    }

    public function storeFolder(\Illuminate\Http\Request $request) 
    {
        $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255', 
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
                \Illuminate\Validation\Rule::unique('folders')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ],
        ], [
            'name.regex' => 'Nama folder hanya boleh berisi huruf, angka, spasi, strip (-), dan underscore (_).'
        ]);

        \App\Models\Folder::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name), // Clean slug, no random chars
        ]);

        return back()->with('success', 'Folder berhasil dibuat!');
    }

    public function updateFolder(\Illuminate\Http\Request $request, $id)
    {
        $folder = \App\Models\Folder::where('user_id', auth()->id())->findOrFail($id);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\-_]+$/'],
        ], [
            'name.regex' => 'Nama folder hanya boleh berisi huruf, angka, spasi, strip (-), dan underscore (_).'
        ]);
        
        $folder->update(['name' => $request->name]);
        
        return back()->with('success', 'Nama folder berhasil diubah!');
    }

    public function destroyFolder(\Illuminate\Http\Request $request, $id)
    {
        $folder = \App\Models\Folder::where('user_id', auth()->id())->find($id);
        
        if (!$folder) {
            // Tampering Attempt: Trying to delete someone else's folder
            \App\Models\SecurityAlert::create([
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'alert_type' => 'unauthorized_access',
                'severity' => 'high',
                'pattern_detected' => "Folder Deletion Tampering: User tried to delete Folder ID #{$id}",
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
            ]);

            \App\Models\Notification::notifyAdmins(
                'security',
                'Percobaan Akses Ilegal!',
                "User " . auth()->user()->username . " mencoba menghapus folder milik orang lain (ID #{$id}).",
                route('admin.security_alerts')
            );

            return back()->with('error', 'Akses Ditolak: Folder tidak ditemukan atau milik orang lain.');
        }

        $videoCount = $folder->videos()->count();

        // 1. Security Check Logic: If deleting ANY video inside folder
        if ($videoCount > 0) {
            if (!$request->has('security_code')) {
                 return back()->with('error', 'Kode keamanan diperlukan untuk menghapus folder yang berisi video.');
            }

            $user = auth()->user();
            if ($request->security_code !== $user->security_answer) {
                 return back()->with('error', 'Kode keamanan salah! Penghapusan dibatalkan.');
            }
        }
        
        // 2. Recursive Deletion (Delete videos inside instead of moving)
        // Physical File Deletion for all videos in folder
        $videos = $folder->videos;
        foreach ($videos as $video) {
             if (file_exists(public_path('uploads/videos/' . $video->filename))) {
                @unlink(public_path('uploads/videos/' . $video->filename));
            }
        }
        
        // Delete Video Records
        $folder->videos()->delete();

        // Delete Physical Folder Directory if exists
        // Structure: uploads/videos/username/foldername
        // But some older files might be elsewhere, however "storeChunk" now puts them in username/foldername
        // Let's safe delete the directory
        $dirPath = public_path('uploads/videos/' . auth()->user()->username . '/' . $folder->slug);
        if (is_dir($dirPath)) {
            // Check if empty? We just deleted all DB known videos.
            // Force delete directory
             \Illuminate\Support\Facades\File::deleteDirectory($dirPath);
        }
        
        $folder->delete();
        
        return redirect()->route('videos.index')->with('success', 'Folder dan seluruh isinya berhasil dihapus permanen!');
    }

    public function storeChunk(\Illuminate\Http\Request $request) {
        try {
            // Validation
            $request->validate([
                'file' => 'required|file',
                'uuid' => 'required|string',
                'chunkIndex' => 'required|integer',
                'totalChunks' => 'required|integer',
                'folder_id' => 'nullable|exists:folders,id'
            ]);

            // GARBAGE COLLECTION (2% Chance)
            // Automates cleanup without needing 'php artisan schedule:work'
            if (mt_rand(1, 50) === 1) { 
                \Illuminate\Support\Facades\Artisan::call('cleanup:chunks');
            }

            $file = $request->file('file');
            $uuid = $request->uuid;
            $chunkIndex = $request->chunkIndex;
            $totalChunks = $request->totalChunks;

            // CHECK MAX UPLOAD SIZE (On First Chunk Only for Performance, or Every Chunk for Security)
            // Most chunk uploaders send 'totalSize' or 'dztotalfilesize'
            $totalSize = $request->input('totalSize') ?? $request->input('dztotalfilesize') ?? 0;
            
            if ($totalSize > 0) {
                 $maxSizeMB = \App\Models\SiteSetting::where('setting_key', 'max_upload_size')->value('setting_value') ?? 500;
                 $maxSizeBytes = $maxSizeMB * 1024 * 1024;

                 if ($totalSize > $maxSizeBytes) {
                     return response()->json(['error' => "File too large. Max allowed: {$maxSizeMB}MB"], 422);
                 }
            }

            // Temp storage path
            $tempPath = storage_path('app/chunks/' . $uuid);
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0777, true);
            }

            // Append chunk to temp file
            $out = fopen($tempPath, $chunkIndex == 0 ? "wb" : "ab");
            $in = fopen($file->getRealPath(), "rb");
            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }
            fclose($out);
            fclose($in);

            // If this is the last chunk, finalize synchronously
            if ($chunkIndex == $totalChunks - 1) {
                try {
                    set_time_limit(0); 
                    
                    $originalName = $request->input('originalName') ?? 'video.mp4';
                    $folderId = $request->folder_id;
                    $userId = auth()->id();
                    $user = auth()->user();

                    // Determine Final Path
                    $storageDir = 'uploads/videos/' . $user->username;
                    $folder = null;
                    
                    if ($folderId) {
                        $folder = \App\Models\Folder::find($folderId);
                        if ($folder) {
                            $storageDir .= '/' . $folder->slug;
                        }
                    }

                    if (!file_exists(public_path($storageDir))) {
                        mkdir(public_path($storageDir), 0777, true);
                    }

                    $filename = time() . '_' . str_replace(' ', '_', $originalName);
                    $finalPath = public_path($storageDir . '/' . $filename);

                    // Move temp file to final path
                    rename($tempPath, $finalPath);

                    // MIME Validation
                    $mime = mime_content_type($finalPath);
                    $allowedMimes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-matroska'];

                    if (!in_array($mime, $allowedMimes)) {
                        @unlink($finalPath);
                        return response()->json(['error' => 'Tipe file tidak didukung: ' . $mime], 403);
                    }

                    $dbFilename = $user->username . '/' . ($folder ? $folder->slug . '/' : '') . $filename;
                    $title = pathinfo($originalName, PATHINFO_FILENAME);
                    $fileSize = filesize($finalPath);

                    Video::create([
                        'user_id' => $userId,
                        'folder_id' => $folderId,
                        'title' => $title,
                        'slug' => \Illuminate\Support\Str::random(16),
                        'filename' => $dbFilename,
                        'file_size' => $fileSize,
                        'status' => 'active',
                        'views' => 0
                    ]);

                    return response()->json(['success' => true]);

                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('FINALIZATION_ERROR: ' . $e->getMessage());
                    return response()->json(['error' => 'Gagal menyelesaikan upload: ' . $e->getMessage()], 500);
                }
            }

            return response()->json(['success' => true, 'chunk' => $chunkIndex]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('CHUNK_UPLOAD_ERROR: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function store(\Illuminate\Http\Request $request)
    {
        try {
        // Legacy Store method (kept for fallback or non-chunked small files if needed)
        // ... (existing code)
            // STRICT SECURITY: Check MIME Type Manually before Validation
            // This prevents "Inspect Element" bypass or renamed files
            if ($request->hasFile('video_file')) {
                $file = $request->file('video_file');
                $mime = $file->getMimeType();
                $allowedMimes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-matroska'];

                if (!in_array($mime, $allowedMimes)) {
                    \Illuminate\Support\Facades\Log::warning('SECURITY_VIOLATION: User ' . auth()->id() . ' tried to upload ' . $mime);
                    
                    return redirect()->route('dashboard')->with('error', 'AKSES DITOLAK: Format file tidak valid atau data dimanipulasi.');
                }
            }

            // Check if file is present (if not, likely post_max_size exceeded)
            if (!$request->hasFile('video_file')) {
                \Illuminate\Support\Facades\Log::error('UPLOAD_ERROR: No file detected. Likely post_max_size limit.');
                return response()->json(['error' => 'No file detected. File too large?'], 400); 
            }

            $maxSizeMB = \App\Models\SiteSetting::where('setting_key', 'max_upload_size')->value('setting_value') ?? 500;
            $maxSizeKB = $maxSizeMB * 1024;

            $request->validate([
                'title' => 'nullable|string|max:255',
                'video_file' => "required|file|mimes:mp4,webm,ogg,mov|max:$maxSizeKB",
                'folder_id' => 'nullable|exists:folders,id' 
            ]);
            
            $file = $request->file('video_file');
            $user = auth()->user();
            
            // Determine storage path: uploads/videos/{username}/{folder_slug?}/
            $storagePath = 'uploads/videos/' . $user->username;
            if ($request->folder_id) {
                $folder = \App\Models\Folder::find($request->folder_id);
                if ($folder) {
                    $storagePath .= '/' . $folder->slug;
                }
            }
            
            // Ensure directory exists
            if (!file_exists(public_path($storagePath))) {
                mkdir(public_path($storagePath), 0777, true);
            }

            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path($storagePath), $filename);
            
            // Store Relative Path in Filename or just Filename? 
            // Current system uses just Filename. 
            // To support folders without breaking everything, we should store RELATIVE path from uploads/videos/ 
            // OR store the path in a separate column.
            // BUT, `asset('uploads/videos/' . $video->filename)` is used in views.
            // So if I store `$user->username . '/' . $folder->slug . '/' . $filename` as the 'filename', existing code works!
            
            $dbFilename = $user->username . '/' . ($request->folder_id && isset($folder) ? $folder->slug . '/' : '') . $filename;

            $title = $request->title ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            
            \App\Models\Video::create([
                'user_id' => auth()->id(),
                'folder_id' => $request->folder_id,
                'title' => $title,
                'slug' => \Illuminate\Support\Str::random(16), // Obfuscated Slug
                'filename' => $dbFilename, // Storing relative path
                'status' => 'active',
                'views' => 0
            ]);
            
            // Return JSON for AJAX success
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('videos.index')->with('success', 'Video berhasil diunggah!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('UPLOAD_ERROR: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function show($slug)
    {
        $video = \App\Models\Video::where('slug', $slug)->with('user')->firstOrFail();
        
        // View recording is handled via AJAX to videos.record-view endpoint
        // which uses ViewTrackingService for fingerprinting and validation.
        
        // Fetch Comments
        $comments = \App\Models\Comment::where('video_id', $video->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();
            
        return view('videos.show', compact('video', 'comments'));
    }

    public function postComment(\Illuminate\Http\Request $request, $slug)
    {
        $video = \App\Models\Video::where('slug', $slug)->firstOrFail();
        
        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);
        
        \App\Models\Comment::create([
            'user_id' => auth()->id(),
            'video_id' => $video->id,
            'parent_id' => $request->parent_id,
            'comment' => $request->comment
        ]);
        
        return back()->with('success', 'Komentar terkirim!');
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $video = \App\Models\Video::where('user_id', auth()->id())->findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        $video->update([
            'title' => $request->title,
            // 'slug' => ... // Keep slug persistent to avoid breaking links
        ]);
        
        return back()->with('success', 'Video berhasil diubah namanya!');
    }

    public function destroy($id)
    {
        $video = \App\Models\Video::where('user_id', auth()->id())->find($id);
        
        if (!$video) {
            // Tampering Attempt
            \App\Models\SecurityAlert::create([
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'alert_type' => 'unauthorized_access',
                'severity' => 'high',
                'pattern_detected' => "Video Deletion Tampering: User tried to delete Video ID #{$id}",
                'url' => request()->fullUrl(),
                'user_agent' => request()->userAgent(),
            ]);

            \App\Models\Notification::notifyAdmins(
                'security',
                'Percobaan Akses Ilegal!',
                "User " . auth()->user()->username . " mencoba menghapus video milik orang lain (ID #{$id}).",
                route('admin.security_alerts')
            );

            return back()->with('error', 'Akses Ditolak: Video tidak ditemukan.');
        }

        // Delete file
        if (file_exists(public_path('uploads/videos/' . $video->filename))) {
            unlink(public_path('uploads/videos/' . $video->filename));
        }
        
        $video->delete();
        
        return back()->with('success', 'Video berhasil dihapus!');
    }
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:videos,id',
            'security_code' => 'nullable|string'
        ]);

        // Security Check for > 3 videos
        if (count($request->ids) > 3) {
            if (!$request->filled('security_code')) {
                 return back()->with('error', 'Kode keamanan diperlukan untuk menghapus lebih dari 3 video.');
            }
            if ($request->security_code !== auth()->user()->security_answer) {
                 return back()->with('error', 'Kode keamanan salah! Penghapusan dibatalkan.');
            }
        }

        $videos = \App\Models\Video::where('user_id', auth()->id())
                    ->whereIn('id', $request->ids)
                    ->get();

        if ($videos->count() !== count($request->ids)) {
             // Tampering Attempt
             \App\Models\SecurityAlert::create([
                 'user_id' => auth()->id(),
                 'ip_address' => request()->ip(),
                 'alert_type' => 'unauthorized_access',
                 'severity' => 'critical',
                 'pattern_detected' => "Bulk Deletion Tampering: User tried to delete " . count($request->ids) . " videos but only owns " . $videos->count(),
                 'url' => request()->fullUrl(),
                 'user_agent' => request()->userAgent(),
             ]);

             \App\Models\Notification::notifyAdmins(
                 'security',
                 'Manipulasi Bulk Delete!',
                 "User " . auth()->user()->username . " mencoba menghapus banyak video sekaligus termasuk data yang bukan miliknya.",
                 route('admin.security_alerts')
             );
        }
        
        $count = 0;
        foreach ($videos as $video) {
             if (file_exists(public_path('uploads/videos/' . $video->filename))) {
                @unlink(public_path('uploads/videos/' . $video->filename));
            }
            $video->delete();
            $count++;
        }
        
        return back()->with('success', "$count video berhasil dihapus!");
    }

    public function bulkMove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:videos,id',
            'folder_id' => 'nullable|exists:folders,id'
        ]);

        $user = auth()->user();
        $targetFolder = null;

        // Ensure folder belongs to user if provided
        if ($request->folder_id) {
             $targetFolder = \App\Models\Folder::where('user_id', $user->id)->where('id', $request->folder_id)->firstOrFail();
        }

        $videos = \App\Models\Video::where('user_id', $user->id)
                    ->whereIn('id', $request->ids)
                    ->get();

        if ($videos->count() !== count($request->ids)) {
             // Tampering Attempt
             \App\Models\SecurityAlert::create([
                 'user_id' => $user->id,
                 'ip_address' => request()->ip(),
                 'alert_type' => 'unauthorized_access',
                 'severity' => 'critical',
                 'pattern_detected' => "Bulk Move Tampering: User tried to move " . count($request->ids) . " videos but only owns " . $videos->count(),
                 'url' => request()->fullUrl(),
                 'user_agent' => request()->userAgent(),
             ]);

             \App\Models\Notification::notifyAdmins(
                 'security',
                 'Manipulasi Bulk Move!',
                 "User " . $user->username . " mencoba memindahkan video yang bukan miliknya.",
                 route('admin.security_alerts')
             );
        }
        
        $count = 0;
        foreach ($videos as $video) {
            // Calculate Old Path
            // Assuming filename in DB is relative "username/folder/file.mp4" OR "username/file.mp4"
            // Note: In older implementation it might just be the filename. 
            // We should rely on 'filename' column being the trustworthy relative path from 'public/uploads/videos/'?
            // Actually, in `storeChunk`, we saved it as "$user->username . '/' . ($folder ? $folder->slug . '/' : '') . $filename".
            
            $currentRelativePath = $video->filename;
            $currentFullPath = public_path('uploads/videos/' . $currentRelativePath); // Check if correct prefix
            
            // Wait, if filename already includes "uploads/videos", this double prefix is bad.
            // Let's check `storeChunk` logic again in memory or assumption.
            // `storeChunk` saved: `$dbFilename = $user->username . '/' . ... . $filename;`
            // So prefix `uploads/videos/` is needed.
            
            if (file_exists($currentFullPath)) {
                $basename = basename($currentFullPath);
                
                // Determine New Directory
                $newDirRelative = $user->username;
                if ($targetFolder) {
                    $newDirRelative .= '/' . $targetFolder->slug;
                }
                
                $newFullPathDir = public_path('uploads/videos/' . $newDirRelative);
                
                if (!file_exists($newFullPathDir)) {
                    mkdir($newFullPathDir, 0777, true);
                }
                
                $newFullPath = $newFullPathDir . '/' . $basename;
                $newDbFilename = $newDirRelative . '/' . $basename;

                // Move File
                if ($currentFullPath !== $newFullPath) {
                    rename($currentFullPath, $newFullPath);
                }

                // Update DB
                $video->update([
                    'folder_id' => $request->folder_id,
                    'filename' => $newDbFilename
                ]);
                $count++;
            } else {
                // If file doesn't exist on disk, just update the folder_id?
                // Or maybe the path in DB is just filename (legacy)?
                // Safer to just update folder_id if file missing to avoid data loss state logic
                 $video->update(['folder_id' => $request->folder_id]);
            }
        }
        
        return back()->with('success', $count . ' video berhasil dipindahkan!');
    }

    /**
     * Handle heartbeat from active video viewers
     */
    public function heartbeat(Request $request, $slug)
    {
        $video = \App\Models\Video::where('slug', $slug)->firstOrFail();
        
        $request->validate([
            'session_id' => 'required|string|max:64'
        ]);

        $sessionId = $request->session_id;
        $userId = auth()->check() ? auth()->id() : null;

        // Upsert viewer record
        \DB::table('active_viewers')->updateOrInsert(
            ['session_id' => $sessionId],
            [
                'video_id' => $video->id,
                'user_id' => $userId,
                'last_heartbeat' => now(),
                'updated_at' => now(),
                'created_at' => \DB::raw('COALESCE(created_at, NOW())')
            ]
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Record a unique view (called when video starts playing)
     */
    public function recordView(Request $request, $slug)
    {
        \Log::info('recordView called', ['slug' => $slug, 'ip' => $request->ip()]);
        $video = \App\Models\Video::where('slug', $slug)->firstOrFail();
        $viewService = new \App\Services\ViewTrackingService();

        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $fingerprint = $viewService->generateFingerprint($ip, $userAgent);
        $userId = auth()->check() ? auth()->id() : null;

        $counted = $viewService->recordView(
            $video->id,
            $fingerprint,
            $ip,
            $userAgent,
            $userId
        );

        return response()->json([
            'counted' => $counted,
            'message' => $counted ? 'View recorded' : 'Already viewed recently'
        ]);
    }

    /**
     * Update watch duration for analytics
     */
    public function updateWatchDuration(Request $request, $slug)
    {
        $video = \App\Models\Video::where('slug', $slug)->firstOrFail();
        $viewService = new \App\Services\ViewTrackingService();

        $request->validate([
            'duration' => 'required|integer|min:0'
        ]);

        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $fingerprint = $viewService->generateFingerprint($ip, $userAgent);

        $viewService->updateWatchDuration($video->id, $fingerprint, $request->duration);

        return response()->json(['status' => 'ok']);
    }
}
