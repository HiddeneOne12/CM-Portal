<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'status',
        'title',
        'event_date',
        'description',
        'highlights',
        'image',
        'company_id',
        'start_time',
        'end_time',
        'location',
    ];

    protected $casts = [
        'status' => 'integer',
        'event_date' => 'date',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function eventImages(): HasMany
    {
        return $this->hasMany(EventImage::class)->orderBy('display_order')->orderBy('id');
    }

    public function eventParticipants(): HasMany
    {
        return $this->hasMany(EventParticipant::class)->orderBy('display_order')->orderBy('id');
    }
}
