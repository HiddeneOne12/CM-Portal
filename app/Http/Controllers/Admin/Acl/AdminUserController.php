<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Acl\AdminUserModel;
use App\Models\Acl\AdminUserRoleModel;
use App\Models\Acl\RoleModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    private const PERMISSION_LISTING = 'admin/acl/admin-users';
    private const PERMISSION_ADD     = 'admin/acl/admin-users/add';
    private const PERMISSION_EDIT    = 'admin/acl/admin-users/edit';
    private const PERMISSION_DELETE  = 'admin/acl/admin-users/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }

        $admins = AdminUserModel::with('userRoles.role')
            ->orderBy('user_name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.acl.adminUser.listing', [
            'pageTitle' => 'Admin Users',
            'admins'    => $admins,
        ]);
    }

    public function create(): View
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            abort(403);
        }

        $roles = RoleModel::orderBy('display_order')->get();

        return view('admin.acl.adminUser.add', [
            'pageTitle' => 'Add Admin User',
            'roles'     => $roles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            abort(403);
        }
        $data = $request->validate([
            'user_name'  => 'required|string|max:191|unique:tbl_admin,user_name',
            'password'   => 'required|string|min:8|confirmed',
            'is_active'  => 'nullable|boolean',
            'roles'      => 'array',
            'roles.*'    => 'integer',
        ]);

        $admin = AdminUserModel::create([
            'user_name' => strtolower($data['user_name']),
            'password'  => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? 1,
            'user_type' => 'all',
        ]);

        $this->syncRoles($admin->id, $data['roles'] ?? []);

        return redirect()
            ->route('admin.acl.admin-users.listing')
            ->with('success', 'Admin user created.');
    }

    public function edit(string $token): View
    {
        if (!validatePermissions(self::PERMISSION_EDIT)) {
            abort(403);
        }

        $id    = decryptIdFromUrl($token);
        $admin = AdminUserModel::with('userRoles')->findOrFail($id);
        $roles = RoleModel::orderBy('display_order')->get();
        $currentRoleIds = $admin->userRoles->pluck('role_ID')->toArray();

        return view('admin.acl.adminUser.edit', [
            'pageTitle'      => 'Edit Admin User',
            'admin'          => $admin,
            'roles'          => $roles,
            'currentRoleIds' => $currentRoleIds,
        ]);
    }

    public function update(Request $request, string $token): RedirectResponse
    {
        if (!validatePermissions(self::PERMISSION_EDIT)) {
            abort(403);
        }

        $id    = decryptIdFromUrl($token);
        $admin = AdminUserModel::findOrFail($id);

        $data = $request->validate([
            'user_name'  => 'required|string|max:191|unique:tbl_admin,user_name,' . $admin->id . ',id',
            'password'   => 'nullable|string|min:8|confirmed',
            'is_active'  => 'nullable|boolean',
            'roles'      => 'array',
            'roles.*'    => 'integer',
        ]);

        $admin->user_name = strtolower($data['user_name']);
        $admin->is_active = $data['is_active'] ?? 1;

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        $this->syncRoles($admin->id, $data['roles'] ?? []);

        return redirect()
            ->route('admin.acl.admin-users.listing')
            ->with('success', 'Admin user updated.');
    }

    public function destroy(string $token): RedirectResponse
    {
        if (!validatePermissions(self::PERMISSION_DELETE)) {
            abort(403);
        }

        $id    = decryptIdFromUrl($token);
        $admin = AdminUserModel::findOrFail($id);

        if (auth('admin')->id() === $admin->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        AdminUserRoleModel::where('admin_ID', $admin->id)->delete();
        $admin->delete();

        return redirect()
            ->route('admin.acl.admin-users.listing')
            ->with('success', 'Admin user deleted.');
    }

    private function syncRoles(int $adminId, array $roleIds): void
    {
        AdminUserRoleModel::where('admin_ID', $adminId)->delete();

        if (empty($roleIds)) {
            return;
        }

        $rows = [];
        foreach ($roleIds as $roleId) {
            $rows[] = [
                'admin_ID' => $adminId,
                'role_ID'  => (int) $roleId,
            ];
        }

        AdminUserRoleModel::insert($rows);
    }
}