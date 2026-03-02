<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalUser extends Model
{
    protected $table = 'portal_users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'status',
        'first_name',
        'last_name',
        'image',
        'email',
        'phone_number',
        'gender',
    ];

    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
