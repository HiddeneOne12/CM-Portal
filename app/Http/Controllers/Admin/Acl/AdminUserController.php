<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Acl\AdminUserModel;
use App\Models\Acl\AdminUserRoleModel;
use App\Models\Acl\RoleModel;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    use ResponseTrait;
    private const PERMISSION_LISTING = 'admin/acl/admin-users';
    private const PERMISSION_ADD     = 'admin/acl/admin-users/add';
    private const PERMISSION_EDIT    = 'admin/acl/admin-users/edit';
    private const PERMISSION_DELETE  = 'admin/acl/admin-users/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }

        $query = AdminUserModel::with('userRoles.role')->orderBy('user_name');
        if ($request->filled('search')) {
            $q = sanitizeInput((string) $request->input('search', ''), 'string');
            $query->where('user_name', 'like', '%' . $q . '%');
        }
        $admins = $query->paginate(20)->withQueryString();

        return view('admin.acl.adminUser.listing', [
            'pageTitle' => 'Admin Users',
            'admins'    => $admins,
        ]);
    }

    public function create(Request $request): View|JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
            }
            abort(403);
        }

        $roles = RoleModel::orderBy('display_order')->get();

        if ($request->ajax() || $request->wantsJson()) {
            try {
                $html = view('admin.acl.adminUser._form_drawer', ['admin' => null, 'roles' => $roles])->render();
                return response()->json(['responseCode' => 1, 'html' => $html]);
            } catch (\Throwable $e) {
                return response()->json(['responseCode' => 0, 'msg' => 'Could not load form.']);
            }
        }

        return view('admin.acl.adminUser.add', [
            'pageTitle' => 'Add Admin User',
            'roles'     => $roles,
        ]);
    }

    public function store(Request $request): RedirectResponse|string
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            if ($request->ajax()) {
                return $this->errorResponse('Access denied');
            }
            abort(403);
        }
        $data = $request->validate([
            'user_name'  => 'required|string|max:191|unique:tbl_admin,user_name',
            'password'   => 'required|string|min:8|confirmed',
            'is_active'  => 'nullable|boolean',
            'roles'      => 'array',
            'roles.*'    => 'nullable|integer',
        ]);

        $roleIds = array_filter(array_map('intval', $data['roles'] ?? []), fn($id) => $id > 0);

        $admin = AdminUserModel::create([
            'user_name' => strtolower($data['user_name']),
            'password'  => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? 1,
            'user_type' => 'all',
        ]);

        $this->syncRoles($admin->id, $roleIds);

        if ($request->ajax()) {
            return $this->successResponse('Admin user created.');
        }
        return redirect()
            ->route('admin.acl.admin-users.listing')
            ->with('success', 'Admin user created.');
    }

    public function edit(Request $request, string $token): View|JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_EDIT)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
            }
            abort(403);
        }

        $id    = decryptIdFromUrl($token);
        $admin = AdminUserModel::with('userRoles')->findOrFail($id);
        $roles = RoleModel::orderBy('display_order')->get();

        if ($request->ajax() || $request->wantsJson()) {
            try {
                $html = view('admin.acl.adminUser._form_drawer', ['admin' => $admin, 'roles' => $roles])->render();
                return response()->json(['responseCode' => 1, 'html' => $html]);
            } catch (\Throwable $e) {
                return response()->json(['responseCode' => 0, 'msg' => 'Could not load form.']);
            }
        }

        return view('admin.acl.adminUser.edit', [
            'pageTitle'      => 'Edit Admin User',
            'admin'          => $admin,
            'roles'          => $roles,
            'currentRoleIds' => $admin->userRoles->pluck('role_ID')->toArray(),
        ]);
    }

    public function update(Request $request, string $token): RedirectResponse|string
    {
        if (!validatePermissions(self::PERMISSION_EDIT)) {
            if ($request->ajax()) {
                return $this->errorResponse('Access denied');
            }
            abort(403);
        }

        $id    = decryptIdFromUrl($token);
        $admin = AdminUserModel::findOrFail($id);

        $data = $request->validate([
            'user_name'  => 'required|string|max:191|unique:tbl_admin,user_name,' . $admin->id . ',id',
            'password'   => 'nullable|string|min:8|confirmed',
            'is_active'  => 'nullable|boolean',
            'roles'      => 'array',
            'roles.*'    => 'nullable|integer',
        ]);

        $roleIds = array_filter(array_map('intval', $data['roles'] ?? []), fn($id) => $id > 0);

        $admin->user_name = strtolower($data['user_name']);
        $admin->is_active = $data['is_active'] ?? 1;

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        $this->syncRoles($admin->id, $roleIds);

        if ($request->ajax()) {
            return $this->successResponse('Admin user updated.');
        }
        return redirect()
            ->route('admin.acl.admin-users.listing')
            ->with('success', 'Admin user updated.');
    }

    public function destroy(Request $request, string $token): RedirectResponse|string
    {
        if (!validatePermissions(self::PERMISSION_DELETE)) {
            if ($request->ajax()) {
                return $this->errorResponse('Access denied');
            }
            abort(403);
        }

        $id    = decryptIdFromUrl($token);
        $admin = AdminUserModel::findOrFail($id);

        if (auth('admin')->id() === $admin->id) {
            if ($request->ajax()) {
                return $this->errorResponse('You cannot delete your own account.');
            }
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        AdminUserRoleModel::where('admin_ID', $admin->id)->delete();
        $admin->delete();

        if ($request->ajax()) {
            return $this->successResponse('Admin user deleted.');
        }
        return redirect()
            ->route('admin.acl.admin-users.listing')
            ->with('success', 'Admin user deleted.');
    }

    private function syncRoles(int $adminId, array $roleIds): void
    {
        AdminUserRoleModel::where('admin_ID', $adminId)->delete();

        $roleIds = array_filter(array_map('intval', $roleIds), fn($id) => $id > 0);
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