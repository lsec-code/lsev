<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginActivity;

class FirstLoginController extends Controller
{
    /**
     * Show first login security verification form
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        
        // If already completed first login, redirect to dashboard
        if ($user->has_logged_in) {
            return redirect()->route('dashboard');
        }
        
        // Check if account is locked
        if ($user->first_login_locked_until && now()->lt($user->first_login_locked_until)) {
            $remainingSeconds = now()->diffInSeconds($user->first_login_locked_until);
            return view('auth.first-login-verify', [
                'locked' => true,
                'remainingSeconds' => $remainingSeconds,
            ]);
        }
        
        // Reset attempts if lock expired
        if ($user->first_login_locked_until && now()->gte($user->first_login_locked_until)) {
            $user->first_login_attempts = 0;
            $user->first_login_locked_until = null;
            $user->save();
        }
        
        $remainingAttempts = 3 - $user->first_login_attempts;
        
        return view('auth.first-login-verify', [
            'locked' => false,
            'remainingAttempts' => $remainingAttempts,
        ]);
    }
    
    /**
     * Verify security code on first login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'security_code' => 'required|string',
        ]);
        
        $user = Auth::user();
        
        // Check if locked
        if ($user->first_login_locked_until && now()->lt($user->first_login_locked_until)) {
            return back()->withErrors(['security_code' => 'Akun terkunci. Silakan coba lagi nanti.']);
        }
        
        // Verify security code
        if ($request->security_code !== $user->security_answer) {
            $user->increment('first_login_attempts');
            
            // Lock after 3 failed attempts
            if ($user->first_login_attempts >= 3) {
                $user->first_login_locked_until = now()->addHour();
                $user->save();
                
                return back()->withErrors(['security_code' => 'Kode keamanan salah 3 kali. Akun dikunci selama 1 jam.']);
            }
            
            $remainingAttempts = 3 - $user->first_login_attempts;
            return back()->withErrors(['security_code' => "Kode keamanan salah. Sisa percobaan: {$remainingAttempts}"]);
        }
        
        // Success: Mark first login complete AND verify email
        $user->has_logged_in = true;
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        $user->first_login_at = now();
        $user->first_login_attempts = 0;
        $user->first_login_locked_until = null;
        $user->save();
        
        // Create first login activity record
        $agent = new \Jenssegers\Agent\Agent();
        $agent->setUserAgent($request->userAgent());
        
        $ipAddress = $request->header('X-Real-IP') ?: $request->ip();
        $ipv6Address = null;
        
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
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
        
        LoginActivity::create([
            'user_id' => $user->id,
            'session_id' => $request->session()->getId(),
            'ip_address' => $ipAddress,
            'ipv6_address' => $ipv6Address,
            'user_agent' => $request->userAgent(),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'device' => $agent->device(),
            'location' => $location,
            'status' => 'success',
            'reason' => 'First login verification completed successfully',
            'login_at' => now(),
        ]);
        
        return redirect()->route('dashboard')->with('status', 'first-login-success');
    }
}
