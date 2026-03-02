<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $table = 'trainings';

    protected $fillable = [
        'status',
        'image',
        'title',
        'description',
        'video',
        'training_image',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }
}
