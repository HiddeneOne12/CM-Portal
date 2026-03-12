<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Acl\AdminUserRoleModel;
use App\Models\Acl\ModuleModel;
use App\Models\Acl\RoleModel;
use App\Models\Acl\RolePrivilegeModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    private const PERMISSION_LISTING = 'admin/acl/roles';
    private const PERMISSION_ADD     = 'admin/acl/roles/add';
    private const PERMISSION_EDIT    = 'admin/acl/roles/edit';
    private const PERMISSION_DELETE  = 'admin/acl/roles/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }

        $roles = RoleModel::orderBy('display_order')->paginate(20)->withQueryString();

        return view('admin.acl.roles.listing', [
            'pageTitle' => 'Roles',
            'roles'     => $roles,
        ]);
    }

    public function create(): View
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            abort(403);
        }

        // Eager load category so blade can group by category_name
        $modules = ModuleModel::with('category')->orderBy('module_name')->get();

        return view('admin.acl.roles.add', [
            'pageTitle' => 'Add Role',
            'modules'   => $modules,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            abort(403);
        }

        $data = $request->validate([
            'role_name'     => 'required|string|max:191',
            'display_order' => 'nullable|integer|min:0',
            'modules'       => 'array',
            'modules.*'     => 'integer',
        ]);

        $role = RoleModel::create([
            'role_name'     => $data['role_name'],
            'display_order' => $data['display_order'] ?? 0,
        ]);

        $this->syncPermissions((int) $role->getKey(), $data['modules'] ?? []);

        return redirect()
            ->route('admin.acl.roles.listing')
            ->with('success', 'Role created.');
    }

    public function edit(string $token): View
    {
        if (!validatePermissions(self::PERMISSION_EDIT)) {
            abort(403);
        }

        $id   = decryptIdFromUrl($token);
        $role = RoleModel::findOrFail($id);

        // Eager load category so blade can group by category_name
        $modules     = ModuleModel::with('category')->orderBy('module_name')->get();
        $permissions = RolePrivilegeModel::where('role_ID', $role->getKey())->pluck('module_ID')->map(fn($v) => (int)$v)->toArray();

        return view('admin.acl.roles.edit', [
            'pageTitle'   => 'Edit Role',
            'role'        => $role,
            'modules'     => $modules,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, string $token): RedirectResponse
    {
        if (!validatePermissions(self::PERMISSION_EDIT)) {
            abort(403);
        }

        $id   = decryptIdFromUrl($token);
        $role = RoleModel::findOrFail($id);

        $data = $request->validate([
            'role_name'     => 'required|string|max:191',
            'display_order' => 'nullable|integer|min:0',
            'modules'       => 'array',
            'modules.*'     => 'integer',
        ]);

        $role->update([
            'role_name'     => $data['role_name'],
            'display_order' => $data['display_order'] ?? 0,
        ]);

        $roleId = (int) $role->getKey();

        // Super Admin (ID 1) always has all permissions; don't edit its privileges
        if ($roleId !== 1) {
            $this->syncPermissions($roleId, $data['modules'] ?? []);
        }

        return redirect()
            ->route('admin.acl.roles.listing')
            ->with('success', 'Role updated.');
    }

    public function destroy(string $token): RedirectResponse
    {
        if (!validatePermissions(self::PERMISSION_DELETE)) {
            abort(403);
        }

        $id   = decryptIdFromUrl($token);
        $role = RoleModel::findOrFail($id);
        $roleId = (int) $role->getKey();

        if ($roleId === 1) {
            return redirect()->back()->with('error', 'Super Admin role cannot be deleted.');
        }

        $assigned = AdminUserRoleModel::where('role_ID', $roleId)->exists();
        if ($assigned) {
            return redirect()->back()->with('error', 'Role is assigned to admin users and cannot be deleted.');
        }

        RolePrivilegeModel::where('role_ID', $roleId)->delete();
        $role->delete();

        return redirect()
            ->route('admin.acl.roles.listing')
            ->with('success', 'Role deleted.');
    }

    private function syncPermissions(int $roleId, array $moduleIds): void
    {
        RolePrivilegeModel::where('role_ID', $roleId)->delete();

        if (empty($moduleIds)) {
            return;
        }

        $rows = [];
        foreach ($moduleIds as $moduleId) {
            $rows[] = [
                'role_ID'   => $roleId,
                'module_ID' => (int) $moduleId,
            ];
        }

        RolePrivilegeModel::insert($rows);
    }
}