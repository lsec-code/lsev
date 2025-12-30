<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\LoginActivity;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $activities = LoginActivity::where('user_id', Auth::id())
            ->orderBy('login_at', 'desc')
            ->paginate(6);

        $sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($session) {
                $agent = new \Jenssegers\Agent\Agent();
                $agent->setUserAgent($session->user_agent);
                
                return (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === request()->session()->getId(),
                    'last_activity' => $session->last_activity,
                    'browser' => $agent->browser(),
                    'platform' => $agent->platform(),
                    'device' => $agent->device(),
                ];
            });

        return view('profile.edit', [
            'user' => $request->user(),
            'activities' => $activities,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Logout from a specific session.
     */
    public function logoutSession(Request $request, $sessionId): RedirectResponse
    {
        DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('status', 'session-logged-out');
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $mime = $file->getMimeType();
            
            // STRICT SECURITY CHECK
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $fileSignature = $mime; // In real world inspect file header, getMimeType() does basic check

            if (!in_array($mime, $allowedMimes)) {
                // INCREMENT STRIKE (DB Based)
                $user->increment('upload_warnings');
                $strikes = $user->upload_warnings;

                // Log to DB SecurityAlert
                \App\Models\SecurityAlert::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'alert_type' => 'malicious_upload',
                    'severity' => 'medium',
                    'pattern_detected' => "Avatar Mime Manipulation: $mime",
                    'url' => $request->fullUrl(),
                    'user_agent' => $request->userAgent(),
                ]);

                // Notify Admin
                \App\Models\Notification::notifyAdmins(
                    'security',
                    'Percobaan Manipulasi File!',
                    "User {$user->username} mencoba mengupload avatar dengan tipe file ilegal ({$mime}). Strike {$strikes}/3.",
                    route('admin.bans')
                );

                // Log the incident to file
                \Illuminate\Support\Facades\Log::warning("Security Alert: Invalid File Upload (Strike $strikes/3)", [
                    'user_id' => $user->id,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_detected' => $mime,
                    'ip' => $request->ip()
                ]);

                // 3RD STRIKE = BAN
                if ($strikes >= 3) {
                    // 1. Suspend User
                    $user->is_suspended = true;
                    $user->suspended_at = now();
                    $user->suspension_reason = "Multiple attempts to upload malicious files (Detected: $mime). Security Violation.";
                    $user->save();

                    // 2. Ban IP Address
                    \App\Models\IpBan::create([
                        'ip_address' => $request->ip(),
                        'device_fingerprint' => md5($request->ip() . $request->userAgent()),
                        'attempt_count' => $strikes,
                        'last_pattern' => 'Malicious Avatar Upload (System Ban)',
                        'violations' => [['pattern' => "Mime: $mime", 'url' => $request->fullUrl(), 'timestamp' => now()->toDateTimeString()]],
                        'banned_at' => now(),
                        'expires_at' => now()->addDays(30), // 30 Days IP Ban
                    ]);
                    
                    // Notify Admin of BAN
                    \App\Models\Notification::notifyAdmins(
                        'security',
                        'User & IP Diberhentikan Otomatis',
                        "User {$user->username} dan IP {$request->ip()} telah di-ban otomatis karena 3x percobaan upload berbahaya.",
                        route('admin.bans')
                    );

                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return Redirect::route('account.suspended');
                }

                // Force abort and redirect to Alert
                return Redirect::route('security.alert');
            }
            
            // Clear strikes on success if wanted, or let it expire. We'll leave it to strictly enforce "3 mistakes in 24h"
            
            $request->validate([
                'avatar' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);

            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path('uploads/avatars/' . $user->avatar))) {
                @unlink(public_path('uploads/avatars/' . $user->avatar));
            }

            $avatarName = time() . '.' . $request->avatar->extension();
            $request->avatar->move(public_path('uploads/avatars'), $avatarName);
            $user->avatar = $avatarName;
        }

        // Payment Details - Validate all fields are filled
        if ($request->has('payment_method')) {
            // Validate that all payment fields are filled with correct format
            $request->validate([
                'payment_method' => 'required|string',
                'payment_number' => 'required|numeric',
                'payment_name' => [
                    'required',
                    'string',
                    'regex:/^[a-zA-Z\s]+$/',
                    'not_regex:/^\s+$/',
                ],
            ], [
                'payment_method.required' => 'Metode pembayaran harus dipilih',
                'payment_number.required' => 'Nomor rekening/HP harus diisi',
                'payment_number.numeric' => 'Nomor rekening/HP hanya boleh berisi angka',
                'payment_name.required' => 'Nama pemilik akun harus diisi',
                'payment_name.regex' => 'Nama pemilik akun hanya boleh berisi huruf dan spasi',
                'payment_name.not_regex' => 'Nama pemilik akun tidak boleh hanya berisi spasi',
            ]);
            
            $user->payment_method = $request->payment_method;
            $user->payment_number = $request->payment_number;
            $user->payment_name = trim($request->payment_name);
        }

        // Video Settings
        if ($request->has('video_settings_update')) {
            $user->allow_download = $request->boolean('allow_download');
        }
        
        // Custom Domain Settings
        if ($request->has('custom_domain_update')) {
            $domain = $request->custom_domain;
            
            if ($domain) {
                // Validate domain format
                $request->validate([
                    'custom_domain' => [
                        'required',
                        'string',
                        'regex:/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i',
                        'not_regex:/^https?:\/\//',
                    ],
                ], [
                    'custom_domain.required' => 'Domain harus diisi',
                    'custom_domain.regex' => 'Format domain tidak valid (contoh: myvideos.com)',
                    'custom_domain.not_regex' => 'Jangan sertakan http:// atau https://',
                ]);
                
                // Clean domain (remove www, http, https)
                $domain = strtolower($domain);
                $domain = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $domain);
                $domain = rtrim($domain, '/');
                
                $user->custom_domain = $domain;
                
                // Auto-verify domain (check if DNS points to server)
                $serverIp = gethostbyname($_SERVER['SERVER_NAME']);
                $domainIp = gethostbyname($domain);
                
                if ($domainIp === $serverIp) {
                    $user->domain_verified = true;
                    $user->domain_verified_at = now();
                } else {
                    $user->domain_verified = false;
                    $user->domain_verified_at = null;
                }
            } else {
                // Clear domain
                $user->custom_domain = null;
                $user->domain_verified = false;
                $user->domain_verified_at = null;
            }
        }


        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
            'security_answer' => ['required', 'string'],
        ]);

        if ($request->security_answer !== $request->user()->security_answer) {
            return back()->withErrors(['security_answer' => 'Kode Keamanan Salah!'], 'userDeletion');
        }

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
