<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class HandleCustomDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $mainDomain = parse_url(config('app.url'), PHP_URL_HOST);

        // Skip for main domain, localhost, or IP addresses
        if ($host === $mainDomain || $host === 'localhost' || $host === '127.0.0.1' || filter_var($host, FILTER_VALIDATE_IP)) {
            return $next($request);
        }

        // Try to find a user with this custom domain (must be verified)
        // We use a simple cache or singleton to store the mapped user for this request
        $mappedUser = User::where('custom_domain', $host)
            ->where('domain_verified', true)
            ->first();

        if ($mappedUser) {
            // Store the mapped user in the request attributes for easy access
            $request->attributes->set('mapped_user', $mappedUser);
            $request->attributes->set('mapped_user_id', $mappedUser->id);
        }

        return $next($request);
    }
}
