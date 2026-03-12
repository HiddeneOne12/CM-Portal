<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;

class AdminUserRoleModel extends Model
{
    protected $table = 'tbl_admin_user_roles';

    protected $primaryKey = 'id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function user()
    {
        return $this->belongsTo(AdminUserModel::class, 'admin_ID', 'id');
    }

    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'role_ID', 'ID'); // ← 'ID' not 'id'
    }
}