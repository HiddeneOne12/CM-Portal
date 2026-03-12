<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $table = 'visitor_logs';

    protected $fillable = [
        'session_id',
        'visited_at',
        'time_spent_seconds',
        'source',
        'path',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];
}
