<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    protected $table = 'documentations';

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
