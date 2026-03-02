<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    protected $table = 'tbl_roles';

    protected $primaryKey = 'id';

    public function permissions()
    {
        return $this->hasMany(RolePrivilegeModel::class, 'role_ID', 'id');
    }

    public function modules()
    {
        return $this->hasMany(RolePrivilegeModel::class, 'role_ID', 'id');
    }
}
