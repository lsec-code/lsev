<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\IpBan;
use App\Models\LoginActivity;
use App\Models\SecurityAlert;

class DetectMaliciousActivity
{
    /**
     * Malicious patterns to detect (improved accuracy)
     */
    private $patterns = [
        // SQL Injection (more specific patterns)
        'UNION SELECT' => 'SQL UNION attack',
        'SELECT * FROM' => 'SQL SELECT injection',
        'DROP TABLE' => 'SQL DROP command',
        'DROP DATABASE' => 'SQL DROP command',
        'INSERT INTO' => 'SQL INSERT injection',
        'UPDATE SET' => 'SQL UPDATE injection',
        'DELETE FROM' => 'SQL DELETE injection',
        'OR 1=1' => 'SQL boolean injection',
        'AND 1=1' => 'SQL boolean injection',
        "OR '1'='1" => 'SQL boolean injection',
        'EXEC(' => 'SQL EXEC command',
        'EXECUTE(' => 'SQL EXECUTE command',
        '; DROP' => 'SQL injection attempt',
        
        // XSS
        '<script' => 'XSS script injection',
        'javascript:' => 'XSS javascript protocol',
        'onerror=' => 'XSS event handler',
        'onload=' => 'XSS event handler',
        'onclick=' => 'XSS event handler',
        '<iframe' => 'XSS iframe injection',
        
        // Path Traversal
        '../' => 'Path traversal attempt',
        '..\\' => 'Path traversal attempt',
        '/etc/passwd' => 'System file access attempt',
        '/etc/shadow' => 'System file access attempt',
        'C:\\Windows' => 'System directory access',
        
        // Security Scanners
        'sqlmap' => 'SQL injection scanner detected',
        'nikto' => 'Web scanner detected',
        'nmap' => 'Port scanner detected',
        'acunetix' => 'Security scanner detected',
        'burp' => 'Burp Suite detected',
        'havij' => 'SQL injection tool detected',
    ];

    /**
     * Routes that should be whitelisted from detection
     */
    private $whitelistedRoutes = [
        'admin/*',
        'login',
        'register',
        'password/*',
        'security/*',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip detection for localhost/development
        $ipAddress = $request->header('X-Real-IP') ?: $request->ip();
        if (in_array($ipAddress, ['127.0.0.1', '::1']) || str_starts_with($ipAddress, '192.168.')) {
            return $next($request);
        }

        // Skip detection for whitelisted routes
        foreach ($this->whitelistedRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }
        
        $ipv6Address = null;
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ipv6Address = $ipAddress;
        }
        
        // Get device fingerprint from request
        $fingerprint = $request->input('device_fingerprint') ?: $request->session()->get('device_fingerprint');
        
        // Check if device fingerprint or IP is already banned
        $activeBan = IpBan::getActiveBan($ipAddress, $ipv6Address, $fingerprint);
        if ($activeBan) {
            return $this->showNoAccessPage($request, $activeBan);
        }
        
        // Check for malicious patterns
        $detectedPattern = $this->detectMaliciousPattern($request);
        
        if ($detectedPattern) {
            return $this->handleMaliciousActivity($request, $ipAddress, $ipv6Address, $fingerprint, $detectedPattern);
        }
        
        // Store fingerprint in session for future requests
        if ($fingerprint) {
            $request->session()->put('device_fingerprint', $fingerprint);
        }
        
        return $next($request);
    }
    
    /**
     * Detect malicious patterns in request
     */
    private function detectMaliciousPattern(Request $request)
    {
        // Check URL
        $fullUrl = $request->fullUrl();
        $decodedUrl = urldecode($fullUrl);
        
        foreach ($this->patterns as $pattern => $description) {
            if (stripos($decodedUrl, $pattern) !== false) {
                return $description;
            }
        }
        
        // Check query parameters
        foreach ($request->query() as $key => $value) {
            $checkString = $key . '=' . $value;
            foreach ($this->patterns as $pattern => $description) {
                if (stripos($checkString, $pattern) !== false) {
                    return $description;
                }
            }
        }
        
        // Check User-Agent
        $userAgent = $request->userAgent();
        foreach ($this->patterns as $pattern => $description) {
            if (stripos($userAgent, $pattern) !== false) {
                return $description;
            }
        }
        
        return null;
    }
    
    /**
     * Handle detected malicious activity
     */
    private function handleMaliciousActivity(Request $request, $ipAddress, $ipv6Address, $fingerprint, $pattern)
    {
        // Find or create ban record (prioritize fingerprint)
        $ipBan = null;
        
        if ($fingerprint) {
            $ipBan = IpBan::where('device_fingerprint', $fingerprint)->first();
        }
        
        if (!$ipBan) {
            $ipBan = IpBan::where('ip_address', $ipAddress)->first();
        }
        
        if (!$ipBan) {
            // First attempt: 30 minute cooldown
            $ipBan = IpBan::create([
                'ip_address' => $ipAddress,
                'ipv6_address' => $ipv6Address,
                'device_fingerprint' => $fingerprint,
                'attempt_count' => 1,
                'last_pattern' => $pattern,
                'violations' => [[
                    'pattern' => $pattern,
                    'url' => $request->fullUrl(),
                    'timestamp' => now()->toDateTimeString(),
                ]],
                'banned_at' => now(),
                'expires_at' => now()->addMinutes(30), // 30 min cooldown
            ]);
        } else {
            // Update fingerprint if not set
            if (!$ipBan->device_fingerprint && $fingerprint) {
                $ipBan->device_fingerprint = $fingerprint;
            }
            $ipBan->increment('attempt_count');
            $ipBan->addViolation($pattern, $request->fullUrl());
            
            // Progressive ban based on attempt count
            if ($ipBan->attempt_count < 3) {
                // Attempts 1-2: 30 minute cooldown
                $ipBan->banned_at = now();
                $ipBan->expires_at = now()->addMinutes(30);
                $ipBan->save();
            } elseif ($ipBan->attempt_count < 5) {
                // Attempts 3-4: 2 hour cooldown
                $ipBan->banned_at = now();
                $ipBan->expires_at = now()->addHours(2);
                $ipBan->save();
            } elseif ($ipBan->attempt_count >= 5) {
                // 5th attempt: PERMANENT BAN
                $ipBan->banned_at = now();
                $ipBan->expires_at = null; // Permanent
                $ipBan->save();
            }
        }
        
        
        // Get location and agent info for detailed reporting
        $agent = new \Jenssegers\Agent\Agent();
        $agent->setUserAgent($request->userAgent());
        
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
                // Fallback to unknown
            }
        }
        
        // Handle authenticated users: Progressive Account Suspension
        $userId = null;
        if (Auth::check()) {
            $user = Auth::user();
            $userId = $user->id;
            
            // Progressive account suspension based on attempt count
            if ($ipBan->attempt_count >= 5) {
                // 5th offense: PERMANENT ACCOUNT SUSPENSION
                $user->is_suspended = true;
                $user->suspension_reason = "Akun di-suspend PERMANEN setelah {$ipBan->attempt_count} percobaan aktivitas ilegal: {$pattern}";
                $user->suspended_at = now();
                $user->save();
            }
            // For attempt 1 & 2, just temporary ban (no account suspension)
            
            // Log the security event
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
                'status' => 'blocked',
                'reason' => $ipBan->attempt_count >= 5 
                    ? "Akun di-suspend PERMANEN karena aktivitas ilegal: {$pattern}" 
                    : "Login dicegah sementara (percobaan ke-{$ipBan->attempt_count}): {$pattern}",
                'login_at' => now(),
                'logout_at' => now(),
            ]);
            
            // Logout user immediately
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        
        // CREATE ADMIN SECURITY ALERT (for both guest and authenticated users)
        SecurityAlert::create([
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'ipv6_address' => $ipv6Address,
            'device_fingerprint' => $fingerprint,
            'alert_type' => 'malicious_activity',
            'severity' => 'critical',
            'pattern_detected' => $pattern,
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'location' => $location,
        ]);

        // Trigger Admin Notification
        \App\Models\Notification::notifyAdmins(
            'security',
            'Ancaman Keamanan Terdeteksi!',
            "Aktivitas mencurigakan ({$pattern}) dari IP: {$ipAddress}. Lokasi: {$location}.",
            route('admin.security_alerts') // Link to security log in admin panel
        );
        
        return $this->showNoAccessPage($request, $ipBan);
    }
    
    /**
     * Show NO ACCESS warning page
     */
    private function showNoAccessPage(Request $request, IpBan $ipBan)
    {
        return response()->view('errors.no-access', [
            'ipBan' => $ipBan,
            'violations' => $ipBan->violations ?? [],
        ], 403);
    }
}
