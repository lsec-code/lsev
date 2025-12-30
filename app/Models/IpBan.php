<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpBan extends Model
{
    protected $fillable = [
        'ip_address',
        'ipv6_address',
        'device_fingerprint',
        'attempt_count',
        'last_pattern',
        'violations',
        'banned_at',
        'expires_at',
    ];

    protected $casts = [
        'banned_at' => 'datetime',
        'expires_at' => 'datetime',
        'violations' => 'array',
    ];

    /**
     * Check if IP is currently banned
     */
    public function isBanned(): bool
    {
        if (!$this->banned_at) {
            return false;
        }

        // Check if ban has expired
        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    /**
     * Get active ban for device fingerprint or IP
     * Priority: Fingerprint > IP
     */
    public static function getActiveBan($ipAddress, $ipv6Address = null, $fingerprint = null)
    {
        $query = self::where('banned_at', '!=', null)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });

        // Priority 1: Check fingerprint (most accurate)
        if ($fingerprint) {
            $ban = (clone $query)->where('device_fingerprint', $fingerprint)->first();
            if ($ban) {
                return $ban;
            }
        }

        // Priority 2: Check IP (fallback for shared networks)
        $ban = (clone $query)->where(function ($q) use ($ipAddress, $ipv6Address) {
            $q->where('ip_address', $ipAddress);
            if ($ipv6Address) {
                $q->orWhere('ipv6_address', $ipv6Address);
            }
        })->first();

        return $ban;
    }

    /**
     * Add violation to the record
     */
    public function addViolation($pattern, $url)
    {
        $violations = $this->violations ?? [];
        $violations[] = [
            'pattern' => $pattern,
            'url' => $url,
            'timestamp' => now()->toDateTimeString(),
        ];
        
        $this->violations = $violations;
        $this->last_pattern = $pattern;
        $this->save();
    }
}
