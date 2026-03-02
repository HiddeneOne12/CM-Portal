<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUserModel extends Authenticatable
{
    protected $table = 'tbl_admin';

    protected $primaryKey = 'id';

    protected $hidden = ['password', 'remember_token'];

    protected $fillable = [
        'user_name',
        'is_active',
        'password',
        'theme_color',
        'user_type',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function userRoles()
    {
        return $this->hasMany(AdminUserRoleModel::class, 'admin_ID', 'id');
    }
}
