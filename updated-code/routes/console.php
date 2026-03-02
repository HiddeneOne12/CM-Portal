<?php

use App\Models\Acl\ModuleModel;
use App\Models\Acl\RolePrivilegeModel;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('acl:assign-portal-users', function () {
    $roleId = 1;
    $routes = [
        'admin/acl/portal-users',
        'admin/acl/portal-users/add',
        'admin/acl/portal-users/edit',
        'admin/acl/portal-users/delete',
    ];
    $modules = ModuleModel::whereIn('route', $routes)->get();
    $inserted = 0;
    foreach ($modules as $module) {
        $moduleId = $module->getKey();
        if ($moduleId === null) {
            continue;
        }
        $exists = RolePrivilegeModel::where('role_ID', $roleId)->where('module_ID', $moduleId)->exists();
        if (!$exists) {
            RolePrivilegeModel::create([
                'role_ID' => $roleId,
                'module_ID' => $moduleId,
            ]);
            $inserted++;
            $this->line("Assigned role {$roleId} to module: {$module->module_name} ({$module->route})");
        }
    }
    if ($inserted === 0) {
        $this->info('Portal Users modules are already assigned to role 1. Nothing to do.');
    } else {
        $this->info("Done. Assigned {$inserted} module(s) to role 1. Refresh the admin panel to see 'Users' in the menu.");
    }
})->purpose('Assign role 1 (e.g. Super Admin) to Portal Users modules so they show in the admin menu');

Artisan::command('acl:assign-hero', function () {
    $roleId = 1;
    $routes = [
        'admin/acl/hero',
        'admin/acl/hero/add',
        'admin/acl/hero/edit',
        'admin/acl/hero/delete',
    ];
    $modules = ModuleModel::whereIn('route', $routes)->get();
    $inserted = 0;
    foreach ($modules as $module) {
        $moduleId = $module->getKey();
        if ($moduleId === null) {
            continue;
        }
        $exists = RolePrivilegeModel::where('role_ID', $roleId)->where('module_ID', $moduleId)->exists();
        if (!$exists) {
            RolePrivilegeModel::create([
                'role_ID' => $roleId,
                'module_ID' => $moduleId,
            ]);
            $inserted++;
            $this->line("Assigned role {$roleId} to module: {$module->module_name} ({$module->route})");
        }
    }
    if ($inserted === 0) {
        $this->info('Hero modules are already assigned to role 1. Nothing to do.');
    } else {
        $this->info("Done. Assigned {$inserted} Hero module(s) to role 1.");
    }
})->purpose('Assign role 1 to all Hero CRUD modules (listing, add, edit, delete)');

Artisan::command('acl:assign-interviews', function () {
    $roleId = 1;
    $routes = [
        'admin/acl/interviews',
        'admin/acl/interviews/add',
        'admin/acl/interviews/edit',
        'admin/acl/interviews/delete',
    ];
    $modules = ModuleModel::whereIn('route', $routes)->get();
    $inserted = 0;
    foreach ($modules as $module) {
        $moduleId = $module->getKey();
        if ($moduleId === null) {
            continue;
        }
        $exists = RolePrivilegeModel::where('role_ID', $roleId)->where('module_ID', $moduleId)->exists();
        if (!$exists) {
            RolePrivilegeModel::create([
                'role_ID' => $roleId,
                'module_ID' => $moduleId,
            ]);
            $inserted++;
            $this->line("Assigned role {$roleId} to module: {$module->module_name} ({$module->route})");
        }
    }
    if ($inserted === 0) {
        $this->info('Interviews modules are already assigned to role 1. Nothing to do.');
    } else {
        $this->info("Done. Assigned {$inserted} Interviews module(s) to role 1.");
    }
})->purpose('Assign role 1 to all Interviews CRUD modules (listing, add, edit, delete)');

Artisan::command('acl:assign-events', function () {
    $roleId = 1;
    $routes = [
        'admin/acl/events',
        'admin/acl/events/add',
        'admin/acl/events/edit',
        'admin/acl/events/delete',
    ];
    $modules = ModuleModel::whereIn('route', $routes)->get();
    $inserted = 0;
    foreach ($modules as $module) {
        $moduleId = $module->getKey();
        if ($moduleId === null) {
            continue;
        }
        $exists = RolePrivilegeModel::where('role_ID', $roleId)->where('module_ID', $moduleId)->exists();
        if (!$exists) {
            RolePrivilegeModel::create([
                'role_ID' => $roleId,
                'module_ID' => $moduleId,
            ]);
            $inserted++;
            $this->line("Assigned role {$roleId} to module: {$module->module_name} ({$module->route})");
        }
    }
    if ($inserted === 0) {
        $this->info('Events modules are already assigned to role 1. Nothing to do.');
    } else {
        $this->info("Done. Assigned {$inserted} Events module(s) to role 1.");
    }
})->purpose('Assign role 1 to all Events CRUD modules (listing, add, edit, delete)');
