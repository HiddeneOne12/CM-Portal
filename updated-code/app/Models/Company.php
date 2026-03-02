<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'type',
    ];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
