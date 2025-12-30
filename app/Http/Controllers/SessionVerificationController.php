<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionVerificationController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Check if user is in cooldown
        if ($user->security_cooldown_until && now()->lt($user->security_cooldown_until)) {
            $remainingTime = now()->diffInSeconds($user->security_cooldown_until);
            return view('auth.verify-security', [
                'cooldown' => true,
                'remainingSeconds' => $remainingTime,
                'attempts' => $user->security_verification_attempts
            ]);
        }
        
        return view('auth.verify-security', [
            'cooldown' => false,
            'attempts' => $user->security_verification_attempts
        ]);
    }
    
    public function verify(Request $request)
    {
        $user = Auth::user();
        
        // Check cooldown
        if ($user->security_cooldown_until && now()->lt($user->security_cooldown_until)) {
            return back()->withErrors(['security_code' => 'Akun Anda sedang dalam cooldown. Silakan tunggu.']);
        }
        
        $request->validate([
            'security_code' => 'required|string'
        ]);
        
        // Verify security answer
        if ($request->security_code !== $user->security_answer) {
            $user->increment('security_verification_attempts');
            
            // Log failed verification attempt
            $agent = new \Jenssegers\Agent\Agent();
            $agent->setUserAgent($request->userAgent());
            
            $ipAddress = $request->header('X-Real-IP') ?: $request->ip();
            
            \App\Models\LoginActivity::create([
                'user_id' => $user->id,
                'session_id' => $request->session()->getId(),
                'ip_address' => $ipAddress,
                'user_agent' => $request->userAgent(),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'device' => $agent->device(),
                'location' => 'N/A',
                'status' => 'failed',
                'reason' => 'Kode keamanan salah. Percobaan ke-' . $user->security_verification_attempts,
                'login_at' => now(),
            ]);
            
            // After 3 failed attempts, set cooldown
            if ($user->security_verification_attempts >= 3) {
                // Progressive cooldown: 1h, 2h, 3h, 4h...
                $cooldownHours = (int)($user->security_verification_attempts / 3);
                $user->security_cooldown_until = now()->addHours($cooldownHours);
                $user->save();
                
                return back()->withErrors(['security_code' => "Kode keamanan salah 3 kali. Akun dikunci selama {$cooldownHours} jam."]);
            }
            
            $remainingAttempts = 3 - ($user->security_verification_attempts % 3);
            return back()->withErrors(['security_code' => "Kode keamanan salah. Sisa percobaan: {$remainingAttempts}"]);
        }
        
        // Success - remove ALL other sessions (strict security)
        // User requested: "jika user berhasil login hapus semua sesi yg berjalan kecuali sesi yg berhasil login"
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();
        
        // Reset security tracking
        $user->login_logout_count = 0;
        $user->security_verification_attempts = 0;
        $user->security_cooldown_until = null;
        $user->last_login_logout_reset = now();
        $user->save();
        
        // Clear session flag
        session()->forget('requires_security_verification');
        
        return redirect()->route('dashboard')->with('status', 'security-verified');
    }
    
    public function cancel(Request $request)
    {
        $user = Auth::user();
        
        // Log the cancellation event
        $agent = new \Jenssegers\Agent\Agent();
        $agent->setUserAgent($request->userAgent());
        
        $ipAddress = $request->header('X-Real-IP') ?: $request->ip();
        $ipv6Address = null;
        
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            // IPv4 detected
        } elseif (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ipv6Address = $ipAddress;
        }
        
        $location = 'Unknown Location';
        if ($ipAddress === '127.0.0.1' || $ipAddress === '::1') {
            $location = 'Local Network';
        } else {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(3)->get("http://ip-api.com/json/{$ipAddress}");
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['status']) && $data['status'] === 'success') {
                        $location = ($data['city'] ?? 'Unknown City') . ', ' . ($data['country'] ?? 'Unknown Country');
                    }
                }
            } catch (\Exception $e) {
                // Fallback
            }
        }
        
        \App\Models\LoginActivity::create([
            'user_id' => $user->id,
            'session_id' => $request->session()->getId(),
            'ip_address' => $ipAddress ?: $ipv6Address,
            'ipv6_address' => $ipv6Address,
            'user_agent' => $request->userAgent(),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'device' => $agent->device(),
            'location' => $location,
            'status' => 'cancelled',
            'reason' => 'Verifikasi keamanan dibatalkan oleh user. Login dicegah karena perangkat tidak dikenal dan melebihi batas 3 sesi aktif.',
            'login_at' => now(),
            'logout_at' => now(),
        ]);
        
        // Logout user and redirect to login page
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('status', 'verification-dibatalkan');
    }
}
