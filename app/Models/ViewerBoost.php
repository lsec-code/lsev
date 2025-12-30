<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ViewerBoost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_id',
        'views_per_minute',
        'max_views_per_minute',
        'duration_minutes',
        'started_at',
        'expires_at',
        'status',
        'views_added',
        'earnings_added'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
