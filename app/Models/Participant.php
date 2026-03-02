<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    protected $table = 'participants';

    protected $fillable = [
        'status',
        'name',
        'position',
        'company_id',
        'image',
        'display_order',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function eventParticipants(): HasMany
    {
        return $this->hasMany(EventParticipant::class)->orderBy('display_order')->orderBy('id');
    }
}
