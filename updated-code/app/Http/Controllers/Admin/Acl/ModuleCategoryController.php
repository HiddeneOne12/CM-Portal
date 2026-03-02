<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Acl\ModuleCategoryModel;
use App\Traits\HasPermissionsTrait;
use App\Traits\ModuleCategoryTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\StoreModuleCategoryRequest;
use App\Http\Requests\UpdateModuleCategoryRequest;
class ModuleCategoryController extends Controller
{
    use HasPermissionsTrait;
    use ModuleCategoryTrait;
    use ResponseTrait;

    public const PERMISSION_LISTING = 'admin/acl/module-categories';
    public const PERMISSION_ADD     = 'admin/acl/module-categories/add';
    public const PERMISSION_EDIT    = 'admin/acl/module-categories/edit';
    public const PERMISSION_DELETE  = 'admin/acl/module-categories/delete';

    public function index(): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }

        $result = ModuleCategoryModel::with('modules')
            ->orderBy('display_order')
            ->orderBy('category_name')
            ->get();

        return view('admin.acl.moduleCategory.listing', [
            'pageTitle' => 'Module Categories',
            'result' => $result,
        ]);
    }

    public function create(): JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }

        try {
            $html = view('admin.acl.moduleCategory.add', [
                'pageTitle' => 'Module Categories'
            ])->render();

            return response()->json(['responseCode' => 1, 'html' => $html]);
        } catch (\Throwable $e) {
            return response()->json([
                'responseCode' => 0,
                'msg' => 'Error loading form:  Try again later.' 
            ]);
        }
    }

    public function store(StoreModuleCategoryRequest $request): string
    {
        if (!$request->ajax()) {
            return $this->errorResponse('Access denied');
        }
    
        $data = $request->validated();
    
        ModuleCategoryModel::create([
            'category_name' => $data['category_name'],
            'display_order' => 0,
        ]);
    
        return $this->successResponse('Module category has been added successfully.');
    }

    public function show(Request $request, string $token): string
    {
        $id = decryptIdFromUrl($token);

        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_LISTING)) {
            return $this->errorResponse('Access denied');
        }

        $row = ModuleCategoryModel::find($id);

        if (!$row) {
            return json_encode(['responseCode' => 0, 'html' => '']);
        }

        $html = view('admin.acl.moduleCategory.inc_show', [
            'row' => $row
        ])->render();

        return json_encode(['responseCode' => 1, 'html' => $html]);
    }

    public function edit(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);

        if ($id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }

        $row = ModuleCategoryModel::find($id);

        if (!$row) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }

        try {
            $html = view('admin.acl.moduleCategory.edit', [
                'pageTitle' => 'Module Categories',
                'row' => $row
            ])->render();

            return response()->json(['responseCode' => 1, 'html' => $html]);
        } catch (\Throwable $e) {
            return response()->json([
                'responseCode' => 0,
                'msg' => 'Error loading form:  Try again later.' 
            ]);
        }
    }

   public function update(UpdateModuleCategoryRequest $request, string $token): string
{
    if (!$request->ajax()) {
        return $this->errorResponse('Access denied');
    }

    $id = decryptIdFromUrl($token);

    if ($id === null) {
        return $this->errorResponse('Invalid token');
    }

    $model = ModuleCategoryModel::find($id);

    if (!$model) {
        return $this->errorResponse('Record not found');
    }

    $data = $request->validated();

    $model->update([
        'category_name' => $data['category_name'],
    ]);

    return $this->successResponse('Module category has been updated successfully.');
}

    public function destroy(string $token): string
    {
        $id = decryptIdFromUrl($token);

        if ($id === null || !validatePermissions(self::PERMISSION_DELETE)) {
            abort($id === null ? 404 : 403);
        }

        ModuleCategoryModel::destroy($id);

        return $this->successResponse('Module category has been deleted successfully.');
    }

    public function updateDisplayOrder(Request $request, string $token, int $displayOrderValue): string
    {
        $id = decryptIdFromUrl($token);

        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return $this->errorResponse('Access denied');
        }

        $model = ModuleCategoryModel::find($id);

        if ($model) {
            $model->update(['display_order' => $displayOrderValue]);
        }

        return $this->successResponse('Display order updated successfully.');
    }

    public function searchModuleCategory(Request $request): string
    {
        if (!$request->ajax() || !validatePermissions(self::PERMISSION_LISTING)) {
            return $this->errorResponse('Access denied');
        }

        $searchWord = sanitizeInput(
            $request->input('word', ''),
            'alphanumeric'
        );

        $result = ModuleCategoryModel::with('modules')
            ->where('category_name', 'LIKE', '%' . $searchWord . '%')
            ->get();

        $html = view('admin.acl.moduleCategory.inc_module_category_cards', [
            'result' => $result
        ])->render();

        return json_encode([
            'responseCode' => 1,
            'html' => $html
        ]);
    }
}
