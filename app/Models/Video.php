<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'filename',
        'file_size',
        'status',
        'views',
        'folder_id', // Added folder_id
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
