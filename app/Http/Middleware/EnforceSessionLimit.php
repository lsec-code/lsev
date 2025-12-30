<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnforceSessionLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user needs security verification
            if (session('requires_security_verification')) {
                // Allow only verification routes
                if (!$request->routeIs('security.verify*')) {
                    return redirect()->route('security.verify.show');
                }
            } else {
                // Check session count
                $activeSessionCount = DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->count();
                
                // STRICT SECURITY CHECK
                // 1. Check if Device is Trusted (Exact Match of IP + UA in history > 1 min ago)
                //    - Same IP/Device = Trusted (Ignore)
                //    - Different IP = Untrusted (New Device)
                $userAgent = $request->userAgent();
                $ipAddress = $request->header('X-Real-IP') ?: $request->ip();

                $previousLogins = \App\Models\LoginActivity::where('user_id', $user->id)
                    ->where('ip_address', $ipAddress)
                    ->where('user_agent', $userAgent)
                    ->where('created_at', '<', now()->subMinutes(1)) 
                    ->exists();
                
                $isTrusted = $previousLogins;

                // TRIGGER VERIFICATION IF:
                // 1. Device is UNTRUSTED (New IP) AND
                // 2. User has > 3 Active Sessions.
                //
                // Result:
                // - User must verify code.
                // - On Success: SYSTEM WIPES ALL OTHER SESSIONS (Strict Mode).
                if (!$isTrusted && $activeSessionCount > 3) {
                     session(['requires_security_verification' => true]);
                     return redirect()->route('security.verify.show');
                }
            }
        }
        
        return $next($request);
    }
}
