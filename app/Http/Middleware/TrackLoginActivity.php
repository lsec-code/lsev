<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\LoginActivity;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;

class TrackLoginActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = $request->session()->getId();
            
            // Get the primary IP address
            $primaryIp = $request->header('X-Real-IP') ?: $request->ip();
            
            // Determine if primary IP is IPv4 or IPv6 and separate them
            $ipAddress = null;
            $ipv6Address = null;
            
            if (filter_var($primaryIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ipAddress = $primaryIp;
            } elseif (filter_var($primaryIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $ipv6Address = $primaryIp;
            }
            
            // Try to find the other IP type from X-Forwarded-For
            if ($request->header('X-Forwarded-For')) {
                $forwardedIps = explode(',', $request->header('X-Forwarded-For'));
                foreach ($forwardedIps as $ip) {
                    $ip = trim($ip);
                    // If we don't have IPv4 yet, look for it
                    if (!$ipAddress && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        $ipAddress = $ip;
                    }
                    // If we don't have IPv6 yet, look for it
                    if (!$ipv6Address && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                        $ipv6Address = $ip;
                    }
                    // Break if we have both
                    if ($ipAddress && $ipv6Address) {
                        break;
                    }
                }
            }
            
            // Fallback: if we only have IPv6, also store it in ip_address for compatibility
            if (!$ipAddress && $ipv6Address) {
                $ipAddress = $ipv6Address;
            }
            
            $userAgent = $request->userAgent();

            $activity = LoginActivity::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->first();

            // If no activity OR if previous IP was localhost but now we have a real IP
            if (!$activity || (($activity->ip_address === '127.0.0.1' || $activity->ip_address === '::1') && $ipAddress !== '127.0.0.1' && $ipAddress !== '::1')) {
                $agent = new Agent();
                $agent->setUserAgent($userAgent);

                $location = 'Unknown Location';
                
                // Handle Localhost/Private IPs
                if ($ipAddress === '127.0.0.1' || $ipAddress === '::1') {
                    $location = 'Local Network';
                } else {
                    try {
                        $response = Http::timeout(3)->get("http://ip-api.com/json/{$ipAddress}");
                        if ($response->successful()) {
                            $data = $response->json();
                            if (isset($data['status']) && $data['status'] === 'success') {
                                $location = ($data['city'] ?? 'Unknown City') . ', ' . ($data['country'] ?? 'Unknown Country');
                            }
                        }
                    } catch (\Exception $e) {
                        // Fallback remains Unknown Location
                    }
                }

                if ($activity) {
                    $activity->update([
                        'ip_address' => $ipAddress,
                        'ipv6_address' => $ipv6Address,
                        'location' => $location,
                        'browser' => $agent->browser(),
                        'platform' => $agent->platform(),
                        'device' => $agent->device(),
                    ]);
                } else {
                    LoginActivity::create([
                        'user_id' => $user->id,
                        'session_id' => $sessionId,
                        'ip_address' => $ipAddress,
                        'ipv6_address' => $ipv6Address,
                        'user_agent' => $userAgent,
                        'browser' => $agent->browser(),
                        'platform' => $agent->platform(),
                        'device' => $agent->device(),
                        'location' => $location,
                        'login_at' => now(),
                    ]);
                }
            }
        }

        return $next($request);
    }
}
