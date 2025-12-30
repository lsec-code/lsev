<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityAlert extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'ipv6_address',
        'device_fingerprint',
        'alert_type',
        'severity',
        'pattern_detected',
        'url',
        'user_agent',
        'browser',
        'platform',
        'location',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user associated with this alert
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get unread alerts count
     */
    public static function getUnreadCount()
    {
        return self::where('is_read', false)->count();
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }
}
