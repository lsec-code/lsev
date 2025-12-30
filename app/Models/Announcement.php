<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'content',
        'color',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'speed' => 'integer'
    ];
}
