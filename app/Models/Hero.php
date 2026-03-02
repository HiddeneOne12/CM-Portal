<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $table = 'hero';

    protected $fillable = [
        'status',
        'image',
        'title',
        'description',
        'video_link',
        'video',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }
}
