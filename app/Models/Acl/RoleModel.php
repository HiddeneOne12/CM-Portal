<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    protected $table = 'tbl_roles';

    // Database column name is "ID"
    protected $primaryKey = 'ID';

    // Allow mass assignment for these attributes
    protected $fillable = [
        'role_name',
        'display_order',
    ];

    public function permissions()
    {
        return $this->hasMany(RolePrivilegeModel::class, 'role_ID', 'ID');
    }

    public function modules()
    {
        return $this->hasMany(RolePrivilegeModel::class, 'role_ID', 'ID');
    }
}
