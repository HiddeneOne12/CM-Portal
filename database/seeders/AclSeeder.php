<?php

namespace Database\Seeders;

use App\Models\Acl\AdminUserModel;
use App\Models\Acl\AdminUserRoleModel;
use App\Models\Acl\ModuleCategoryModel;
use App\Models\Acl\ModuleModel;
use App\Models\Acl\RoleModel;
use App\Models\Acl\RolePrivilegeModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AclSeeder extends Seeder
{
    public function run(): void
    {
        $admin = AdminUserModel::firstOrCreate(
            ['user_name' => 'admin'],
            [
                'is_active' => true,
                'password' => Hash::make('password'),
                'user_type' => 'all',
                'is_deleted' => false,
            ]
        );

        $role = RoleModel::firstOrCreate(
            ['role_name' => 'Super Admin'],
            ['display_order' => 1]
        );

        AdminUserRoleModel::firstOrCreate(
            ['admin_ID' => $admin->id, 'role_ID' => $role->id],
            []
        );

        $catDashboard = ModuleCategoryModel::firstOrCreate(
            ['category_name' => 'Dashboard'],
            ['display_order' => 1]
        );
        $catAcl = ModuleCategoryModel::firstOrCreate(
            ['category_name' => 'ACL'],
            ['display_order' => 2]
        );

        $modDashboard = ModuleModel::firstOrCreate(
            ['route' => 'admin/dashboard'],
            [
                'module_category_ID' => $catDashboard->id,
                'module_name' => 'Dashboard',
                'show_in_menu' => true,
                'display_order' => 1,
            ]
        );
        $modCategories = ModuleModel::firstOrCreate(
            ['route' => 'admin/acl/module-categories'],
            [
                'module_category_ID' => $catAcl->id,
                'module_name' => 'Module Categories',
                'show_in_menu' => true,
                'display_order' => 1,
            ]
        );
        $modModules = ModuleModel::firstOrCreate(
            ['route' => 'admin/acl/module'],
            [
                'module_category_ID' => $catAcl->id,
                'module_name' => 'Modules',
                'show_in_menu' => true,
                'display_order' => 2,
            ]
        );

        RolePrivilegeModel::firstOrCreate(
            ['role_ID' => $role->id, 'module_ID' => $modDashboard->id],
            []
        );
        RolePrivilegeModel::firstOrCreate(
            ['role_ID' => $role->id, 'module_ID' => $modCategories->id],
            []
        );
        RolePrivilegeModel::firstOrCreate(
            ['role_ID' => $role->id, 'module_ID' => $modModules->id],
            []
        );
    }
}
