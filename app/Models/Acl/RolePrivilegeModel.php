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

    /**
     * Check if role has permission for the exact route/slug (for buttons and controller checks).
     * No prefix fallback: add/edit/delete require explicit module privileges.
     */
    public static function hasPermission(int $roleId, string $currentUri): mixed
    {
        return self::query()
            ->join('tbl_roles', 'tbl_role_privileges.role_ID', '=', 'tbl_roles.id')
            ->join('tbl_modules', 'tbl_role_privileges.module_ID', '=', 'tbl_modules.id')
            ->where('tbl_role_privileges.role_ID', (int) $roleId)
            ->where('tbl_modules.route', $currentUri)
            ->select('tbl_role_privileges.*')
            ->first();
    }

    /**
     * Check if role has permission for a request path (e.g. admin/acl/admin-users/edit/TOKEN).
     * Path is allowed if it equals a module route or starts with module_route + '/'.
     * Used by middleware so edit/123 matches module route "admin/acl/admin-users/edit".
     */
    public static function hasPermissionForPath(int $roleId, string $path): mixed
    {
        $privileges = self::query()
            ->join('tbl_modules', 'tbl_role_privileges.module_ID', '=', 'tbl_modules.id')
            ->where('tbl_role_privileges.role_ID', (int) $roleId)
            ->select('tbl_role_privileges.*', 'tbl_modules.route as module_route')
            ->get();
        foreach ($privileges as $row) {
            $route = $row->module_route ?? '';
            if ($path === $route || (strlen($route) > 0 && str_starts_with($path, $route . '/'))) {
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
