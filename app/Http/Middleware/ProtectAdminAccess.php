<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Closure;

class ProtectAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // 0. Check if IP is already banned
        $ipBan = \App\Models\IpBan::where('ip_address', $ip)->first();
        if ($ipBan) {
             return response()->view('errors.no-access', ['ipBan' => $ipBan], 403);
        }

        $user = Auth::user();

        // 1. GUEST ACCESSING ADMIN -> BAN IP IMMEDIATELY
        if (!$user) {
            $ipBan = \App\Models\IpBan::firstOrCreate(
                ['ip_address' => $ip],
                [
                    'reason' => 'Unauthorized Admin Access (Guest)', 
                    'expires_at' => null,
                    'banned_at' => now(),
                    'last_pattern' => 'Unauthorized Admin Access',
                    'attempt_count' => 1
                ]
            );
            
            \App\Models\SecurityAlert::create([
                'user_id' => null,
                'alert_type' => 'UNAUTHORIZED_ADMIN_ACCESS',
                'severity' => 'critical',
                'pattern_detected' => "Guest IP $ip banned for attempting admin access.",
                'url' => $request->fullUrl(),
                'ip_address' => $ip,
                'user_agent' => $request->userAgent()
            ]);

            return response()->view('errors.no-access', ['ipBan' => $ipBan], 403);
        }

        // 2. REGULAR USER ACCESSING ADMIN -> WARNING 5x THEN BAN ACCOUNT
        if (!$user->is_admin) {
            $key = 'illegal_admin_access_' . $user->id;
            $attempts = \Illuminate\Support\Facades\Cache::get($key, 0) + 1;
            \Illuminate\Support\Facades\Cache::put($key, $attempts, 3600); // 1 hour memory

            if ($attempts >= 5) {
                // Ban User
                $user->update([
                    'is_suspended' => true,
                    'suspension_reason' => 'Security: Forced Admin Access Attempts',
                    'suspended_at' => now()
                ]);
                
                \App\Models\SecurityAlert::create([
                    'user_id' => $user->id,
                    'alert_type' => 'FORCED_ADMIN_ACCESS',
                    'severity' => 'critical',
                    'pattern_detected' => "User {$user->username} suspended for 5x Admin Access Attempts.",
                    'url' => $request->fullUrl(),
                    'ip_address' => $ip,
                    'user_agent' => $request->userAgent()
                ]);

                Auth::logout();
                abort(403, 'ACCOUNT SUSPENDED PERMANENTLY DUE TO SECURITY VIOLATIONS.');
            }

            // Warning
            return redirect('/')->with('error', "SECURITY WARNING! Attempt $attempts/5. Do not access unauthorized areas or your account will be banned.");
        }

        // 3. ADMIN ACCESS -> REQUIRE DOUBLE AUTH (SECURITY CODE)
        // Skip check if we are already on the verification page
        if ($request->routeIs('admin.verify.security') || $request->routeIs('admin.verify.security.check')) {
            return $next($request);
        }

        if (!session('admin_verified')) {
             return redirect()->route('admin.verify.security');
        }

        return $next($request);
    }
}
