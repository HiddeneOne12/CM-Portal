<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RolePrivilegeModel extends Model
{
    protected $table = 'tbl_role_privileges';

    protected $primaryKey = 'id';

    protected $fillable = ['role_ID', 'module_ID'];

    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'role_ID', 'id');
    }

    public function module()
    {
        return $this->hasOne(ModuleModel::class, 'id', 'module_ID');
    }

    public static function hasPermission(int $roleId, string $currentUri): mixed
    {
        $exact = self::query()
            ->join('tbl_roles', 'tbl_role_privileges.role_ID', '=', 'tbl_roles.id')
            ->join('tbl_modules', 'tbl_role_privileges.module_ID', '=', 'tbl_modules.id')
            ->where('tbl_role_privileges.role_ID', (int) $roleId)
            ->where('tbl_modules.route', $currentUri)
            ->select('tbl_role_privileges.*')
            ->first();
        if ($exact) {
            return $exact;
        }
        // Allow add/edit/search etc. when user has permission for the module base route (e.g. admin/acl/module-categories/add allowed when user has admin/acl/module-categories)
        $privileges = self::query()
            ->join('tbl_modules', 'tbl_role_privileges.module_ID', '=', 'tbl_modules.id')
            ->where('tbl_role_privileges.role_ID', (int) $roleId)
            ->select('tbl_role_privileges.*', 'tbl_modules.route as module_route')
            ->get();
        foreach ($privileges as $row) {
            $route = $row->module_route ?? '';
            if ($currentUri === $route || str_starts_with($currentUri, $route . '/')) {
                return $row;
            }
        }
        return null;
    }

    public static function drawLeftMenu(?int $moduleCatId): \Illuminate\Database\Eloquent\Collection
    {
        if ($moduleCatId === null) {
            return collect();
        }
        $adminUser = Auth::guard('admin')->user();
        if (!$adminUser) {
            return collect();
        }
        $roleIds = AdminUserRoleModel::where('admin_ID', $adminUser->id)->pluck('role_ID')->toArray();
        if (empty($roleIds)) {
            return collect();
        }
        return self::query()
            ->join('tbl_roles', 'tbl_role_privileges.role_ID', '=', 'tbl_roles.id')
            ->join('tbl_modules', 'tbl_role_privileges.module_ID', '=', 'tbl_modules.id')
            ->whereIn('tbl_role_privileges.role_ID', $roleIds)
            ->where('tbl_modules.module_category_ID', $moduleCatId)
            ->where('tbl_modules.show_in_menu', 1)
            ->orderBy('tbl_modules.display_order')
            ->select('tbl_modules.*')
            ->distinct()
            ->get();
    }
}
