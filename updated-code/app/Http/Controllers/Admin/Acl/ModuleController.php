<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Acl\ModuleCategoryModel;
use App\Models\Acl\ModuleModel;
use App\Traits\CategoryTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\StoreModuleRequest;
use App\Http\Requests\UpdateModuleRequest;
class ModuleController extends Controller
{
    use CategoryTrait;
    use ResponseTrait;

    public const PERMISSION_LISTING = 'admin/acl/module';
    public const PERMISSION_ADD     = 'admin/acl/module/add';
    public const PERMISSION_EDIT    = 'admin/acl/module/edit';
    public const PERMISSION_DELETE  = 'admin/acl/module/delete';

    public function index(): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }

        $result = ModuleModel::with(['category', 'roles.role'])
            ->orderBy('display_order')
            ->orderBy('module_category_ID')
            ->get();

        $catResult = ModuleCategoryModel::orderBy('category_name')->get();

        return view('admin.acl.module.listing', [
            'pageTitle' => 'ACL - Modules',
            'result' => $result,
            'catResult' => $catResult,
        ]);
    }

    public function create(): JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }

        try {
            $catResult = ModuleCategoryModel::orderBy('category_name')->get();

            $html = view('admin.acl.module.add', [
                'pageTitle' => 'ACL - Modules',
                'catResult' => $catResult
            ])->render();

            return response()->json(['responseCode' => 1, 'html' => $html]);

        } catch (\Throwable $e) {
            return response()->json([
                'responseCode' => 0,
                'msg' => 'Error loading form:  Try again later.' 
            ]);
        }
    }

    public function store(StoreModuleRequest $request): string
    {
        if (!$request->ajax()) {
            return $this->errorResponse('Access denied');
        }
    
        $data = $request->validated();
        $showInMenu = isset($data['show_in_menu']) ? 1 : 0;
        ModuleModel::create([
            'module_category_ID' => $data['module_category_id'],
            'module_name'        => $data['module_name'],
            'route'              => $data['route'],
            'show_in_menu'       => $showInMenu,
            'css_class'          => $data['css_class'] ?? null,
            'display_order'      => 0,
        ]);
    
        return $this->successResponse('Module has been added successfully.');
    }

    public function show(Request $request, string $token): string
    {
        $id = decryptIdFromUrl($token);

        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_LISTING)) {
            return $this->errorResponse('Access denied');
        }

        $row = ModuleModel::with('category')->find($id);

        if (!$row) {
            return json_encode(['responseCode' => 0, 'html' => '']);
        }

        $html = view('admin.acl.module.inc_show', ['row' => $row])->render();

        return json_encode(['responseCode' => 1, 'html' => $html]);
    }

    public function edit(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);

        if ($id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }

        $row = ModuleModel::find($id);

        if (!$row) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }

        try {
            $catResult = ModuleCategoryModel::orderBy('category_name')->get();

            $html = view('admin.acl.module.edit', [
                'pageTitle' => 'ACL - Module',
                'row' => $row,
                'catResult' => $catResult,
            ])->render();

            return response()->json(['responseCode' => 1, 'html' => $html]);

        } catch (\Throwable $e) {
            return response()->json([
                'responseCode' => 0,
                'msg' => 'Error loading form:  Try again later.' 
            ]);
        }
    }

    public function update(UpdateModuleRequest $request, string $token): string
    {
        if (!$request->ajax()) {
            return $this->errorResponse('Access denied');
        }
    
        $id = decryptIdFromUrl($token);
    
        if ($id === null) {
            return $this->errorResponse('Invalid token');
        }
    
        $model = ModuleModel::find($id);
    
        if (!$model) {
            return $this->errorResponse('Record not found');
        }
    
        $data = $request->validated();
        $showInMenu = isset($data['show_in_menu']) ? 1 : 0;
        $model->update([
            'module_category_ID' => $data['module_category_id'],
            'module_name'        => $data['module_name'],
            'route'              => $data['route'],
            'show_in_menu'       =>   $showInMenu,
            'css_class'          => $data['css_class'] ?? null,
        ]);
    
        return $this->successResponse('Module has been updated successfully.');
    }

    public function destroy(string $token): string
    {
        $id = decryptIdFromUrl($token);

        if ($id === null || !validatePermissions(self::PERMISSION_DELETE)) {
            abort($id === null ? 404 : 403);
        }

        ModuleModel::destroy($id);

        return $this->successResponse('Module has been deleted successfully.');
    }

    public function updateDisplayOrder(Request $request, string $token, int $displayOrderValue): string
    {
        $id = decryptIdFromUrl($token);

        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return $this->errorResponse('Access denied');
        }

        $model = ModuleModel::find($id);

        if ($model) {
            $model->update(['display_order' => $displayOrderValue]);
        }

        return $this->successResponse('Display order updated successfully.');
    }

    public function searchModule(Request $request): string
    {
        if (!$request->ajax() || !validatePermissions(self::PERMISSION_LISTING)) {
            return $this->errorResponse('Access denied');
        }

        $searchWord = sanitizeInput($request->input('word', ''), 'string');

        $result = ModuleModel::with('category', 'roles.role')
            ->where('module_name', 'LIKE', '%' . $searchWord . '%')
            ->orWhere('route', 'LIKE', '%' . $searchWord . '%')
            ->get();

        $html = view('admin.acl.module.inc_module_cards', [
            'result' => $result
        ])->render();

        return json_encode([
            'responseCode' => 1,
            'html' => $html
        ]);
    }
}
