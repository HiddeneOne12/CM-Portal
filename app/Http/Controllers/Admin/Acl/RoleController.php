<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Acl\AdminUserRoleModel;
use App\Models\Acl\ModuleModel;
use App\Models\Acl\RoleModel;
use App\Models\Acl\RolePrivilegeModel;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    use ResponseTrait;

    private const PERMISSION_LISTING = 'admin/acl/roles';
    private const PERMISSION_ADD     = 'admin/acl/roles/add';
    private const PERMISSION_EDIT    = 'admin/acl/roles/edit';
    private const PERMISSION_DELETE  = 'admin/acl/roles/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }

        $query = RoleModel::query()->orderBy('display_order');
        if ($request->filled('search')) {
            $q = sanitizeInput((string) $request->input('search', ''), 'string');
            $query->where('role_name', 'like', '%' . $q . '%');
        }
        $roles = $query->paginate(20)->withQueryString();

        return view('admin.acl.roles.listing', [
            'pageTitle' => 'Roles',
            'roles'     => $roles,
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

        $modules = ModuleModel::with('category')->orderBy('module_name')->get();

        if ($request->ajax() || $request->wantsJson()) {
            try {
                $html = view('admin.acl.roles._form_drawer', ['role' => null, 'modules' => $modules])->render();
                return response()->json(['responseCode' => 1, 'html' => $html]);
            } catch (\Throwable $e) {
                return response()->json(['responseCode' => 0, 'msg' => 'Could not load form.']);
            }
        }

        return view('admin.acl.roles.add', [
            'pageTitle' => 'Add Role',
            'modules'   => $modules,
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
            'role_name'     => 'required|string|max:191',
            'modules'       => 'array',
            'modules.*'     => 'integer',
        ]);

        // New roles go after existing ones
        $nextOrder = (int) RoleModel::max('display_order') + 1;

        $role = RoleModel::create([
            'role_name'     => $data['role_name'],
            'display_order' => $nextOrder,
        ]);

        $this->syncPermissions((int) $role->getKey(), $data['modules'] ?? []);

        if ($request->ajax()) {
            return $this->successResponse('Role created.');
        }

        return redirect()
            ->route('admin.acl.roles.listing')
            ->with('success', 'Role created.');
    }

    public function edit(Request $request, string $token): View|JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_EDIT)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
            }
            abort(403);
        }

        $id   = decryptIdFromUrl($token);
        $role = RoleModel::findOrFail($id);
        $modules = ModuleModel::with('category')->orderBy('module_name')->get();

        if ($request->ajax() || $request->wantsJson()) {
            try {
                $html = view('admin.acl.roles._form_drawer', ['role' => $role, 'modules' => $modules])->render();
                return response()->json(['responseCode' => 1, 'html' => $html]);
            } catch (\Throwable $e) {
                return response()->json(['responseCode' => 0, 'msg' => 'Could not load form.']);
            }
        }

        $permissions = RolePrivilegeModel::where('role_ID', $role->getKey())->pluck('module_ID')->map(fn($v) => (int)$v)->toArray();

        return view('admin.acl.roles.edit', [
            'pageTitle'   => 'Edit Role',
            'role'        => $role,
            'modules'     => $modules,
            'permissions' => $permissions,
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

        $id   = decryptIdFromUrl($token);
        $role = RoleModel::findOrFail($id);

        $data = $request->validate([
            'role_name'     => 'required|string|max:191',
            'modules'       => 'array',
            'modules.*'     => 'integer',
        ]);

        $role->update([
            'role_name'     => $data['role_name'],
        ]);

        $roleId = (int) $role->getKey();

        if ($roleId !== 1) {
            $this->syncPermissions($roleId, $data['modules'] ?? []);
        }

        if ($request->ajax()) {
            return $this->successResponse('Role updated.');
        }

        return redirect()
            ->route('admin.acl.roles.listing')
            ->with('success', 'Role updated.');
    }

    public function destroy(Request $request, string $token): RedirectResponse|string
    {
        if (!validatePermissions(self::PERMISSION_DELETE)) {
            if ($request->ajax()) {
                return $this->errorResponse('Access denied');
            }
            abort(403);
        }

        $id     = decryptIdFromUrl($token);
        $role   = RoleModel::findOrFail($id);
        $roleId = (int) $role->getKey();

        if ($roleId === 1) {
            if ($request->ajax()) {
                return $this->errorResponse('Super Admin role cannot be deleted.');
            }
            return redirect()->back()->with('error', 'Super Admin role cannot be deleted.');
        }

        $assigned = AdminUserRoleModel::where('role_ID', $roleId)->exists();
        if ($assigned) {
            if ($request->ajax()) {
                return $this->errorResponse('Role is assigned to admin users and cannot be deleted.');
            }
            return redirect()->back()->with('error', 'Role is assigned to admin users and cannot be deleted.');
        }

        RolePrivilegeModel::where('role_ID', $roleId)->delete();
        $role->delete();

        if ($request->ajax()) {
            return $this->successResponse('Role deleted.');
        }

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