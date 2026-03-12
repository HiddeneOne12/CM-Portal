<?php

namespace App\Traits;

use App\Models\Acl\AdminUserRoleModel;
use App\Models\Acl\RolePrivilegeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

trait HasPermissionsTrait
{
    /**
     * Check permission using current route URI (for backward compatibility).
     */
    public function getModulesPremissions(): mixed
    {
        $currentUri = Route::getFacadeRoot()->current()?->uri() ?? '';
        return $this->getModulesPremissionsByPath($currentUri);
    }

    /**
     * Server-side access check by request path.
     * Path is normalized to match tbl_modules.route (e.g. "admin/acl/events").
     */
    public function getModulesPremissionsByPath(string $path): mixed
    {
        $adminUser = Auth::guard('admin')->user();
        if (!$adminUser) {
            return false;
        }
        $path = ltrim($path, '/');
        // URL prefix is cmcontrol; permission routes in DB use admin/ for matching
        if (str_starts_with($path, 'cmcontrol/')) {
            $path = 'admin/' . substr($path, strlen('cmcontrol/'));
        } elseif (!str_starts_with($path, 'admin/')) {
            $path = 'admin/' . $path;
        }

        $adminRoles = AdminUserRoleModel::where('admin_ID', $adminUser->id)->get();
        foreach ($adminRoles as $rowAdminRole) {
            // For middleware/path checks, allow URLs like admin/acl/admin-users/edit/TOKEN
            $result = RolePrivilegeModel::hasPermissionForPath($rowAdminRole->role_ID, $path);
            if ($result) {
                return $result;
            }
        }
        return null;
    }

    public static function getModulesPremissionsBySlug(string $slug): bool
    {
        $adminUser = Auth::guard('admin')->user();
        if (!$adminUser) {
            return false;
        }
        $adminUserId = $adminUser->id;
        $adminRoles = AdminUserRoleModel::where('admin_ID', $adminUserId)->get();
        foreach ($adminRoles as $rowAdminRole) {
            $result = RolePrivilegeModel::hasPermission($rowAdminRole->role_ID, $slug);
            if ($result) {
                return true;
            }
        }
        return false;
    }
}
