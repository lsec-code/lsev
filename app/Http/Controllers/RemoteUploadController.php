<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Folder;

class RemoteUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'folder_id' => 'nullable|exists:folders,id'
        ]);

        // Increase limits for large downloads
        set_time_limit(0); 
        ini_set('memory_limit', '1024M');

        $url = $request->input('url');
        $user = auth()->user();
        
        // 1. Validate Headers
        try {
            // Get headers without downloading body
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);     // We want headers
            curl_setopt($ch, CURLOPT_NOBODY, true);     // We don't need body yet
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_exec($ch);
            
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $downloadSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            curl_close($ch);

            if ($httpCode != 200) {
                 return back()->with('error', "Gagal mengakses URL (HTTP $httpCode).");
            }

            // Check if it's actually a video
            if (strpos($contentType, 'text/html') !== false) {
                return back()->with('error', 'URL yang Anda masukkan adalah halaman WEB (HTML), bukan file video langsung. Silakan cari "Direct Link" (biasanya berakhiran .mp4).');
            }
            
            if (strpos($contentType, 'video/') === false && strpos($contentType, 'application/octet-stream') === false) {
                 // Be lenient with octet-stream, but strict otherwise
                 return back()->with('warning', "Tipe file terdeteksi: $contentType. Mungkin bukan video yang valid.");
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memvalidasi URL: ' . $e->getMessage());
        }

        // 2. Generate Initial Record
        $filename = basename(parse_url($url, PHP_URL_PATH));
        if (empty($filename) || !str_contains($filename, '.')) {
            $filename = 'remote-' . Str::random(10) . '.mp4';
        }
        $filename = Str::slug(pathinfo($filename, PATHINFO_FILENAME)) . '.' . pathinfo($filename, PATHINFO_EXTENSION);
        
        try {
            $storagePathRelative = $user->username;
            if ($request->folder_id) {
                $folder = \App\Models\Folder::find($request->folder_id);
                if ($folder) {
                    $storagePathRelative .= '/' . $folder->slug;
                }
            }
            
            $dbFilename = $storagePathRelative . '/' . $filename;
            $finalPathOnDisk = public_path('uploads/videos/' . $dbFilename);
            
            // Ensure directory
            $dirPath = dirname($finalPathOnDisk);
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            // STREAM DOWNLOAD (Synchronous)
            set_time_limit(0); 
            $fp = fopen($url, 'r');
            if (!$fp) {
                throw new \Exception('Could not open stream to URL.');
            }

            $targetFp = fopen($finalPathOnDisk, 'w');
            while (!feof($fp)) {
                fwrite($targetFp, fread($fp, 8192));
            }
            fclose($fp);
            fclose($targetFp);
            
            $size = filesize($finalPathOnDisk);

            // Create Final Record
            Video::create([
                'user_id' => $user->id,
                'folder_id' => $request->folder_id,
                'title' => pathinfo($filename, PATHINFO_FILENAME),
                'slug' => Str::slug(pathinfo($filename, PATHINFO_FILENAME)) . '-' . Str::random(6),
                'filename' => $dbFilename,
                'file_size' => $size,
                'status' => 'active', 
                'views' => 0
            ]);

            return back()->with('success', 'Remote Upload Berhasil!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Remote Upload Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses permintaan: ' . $e->getMessage());
        }
    }
}
