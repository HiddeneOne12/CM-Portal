<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';

    protected $fillable = [
        'status',
        'image',
        'title',
        'description',
        'published_in_date',
        'report_pdf',
    ];

    protected $casts = [
        'status' => 'integer',
        'published_in_date' => 'date',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }
}
