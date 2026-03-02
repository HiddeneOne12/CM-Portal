<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\PortalUser;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PortalUserController extends Controller
{
    use ResponseTrait;

    private const PERMISSION_LISTING = 'admin/acl/portal-users';
    private const PERMISSION_ADD     = 'admin/acl/portal-users/add';
    private const PERMISSION_EDIT    = 'admin/acl/portal-users/edit';
    private const PERMISSION_DELETE  = 'admin/acl/portal-users/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }

        $query = PortalUser::orderBy('first_name')->orderBy('last_name');

        if ($request->filled('search')) {
            $q = sanitizeInput((string) $request->input('search', ''), 'string');

            $query->where(function ($qry) use ($q) {
                $qry->where('first_name', 'like', "%$q%")
                    ->orWhere('last_name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('phone_number', 'like', "%$q%");
            });
        }

        $result = $query->paginate(10)->withQueryString();

        return view('admin.acl.portalUser.listing', [
            'pageTitle' => 'Portal Users',
            'result' => $result,
        ]);
    }

    public function create(): JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }

        try {
            $html = view('admin.acl.portalUser.add', [
                'pageTitle' => 'Portal Users'
            ])->render();

            return response()->json(['responseCode' => 1, 'html' => $html]);

        } catch (\Throwable $e) {
            return response()->json([
                'responseCode' => 0,
                'msg' => 'Error loading form:  Try again later.' 
            ]);
        }
    }

    public function store(Request $request): string
    {
        if (!$request->ajax() || !validatePermissions(self::PERMISSION_ADD)) {
            return $this->errorResponse('Access denied');
        }

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:portal_users,email',
            'phone_number' => 'nullable|string|max:30',
            'gender' => 'nullable|string|in:male,female,other',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'phone_number', 'gender']);
        $data['status'] = 1;

        if ($request->hasFile('image')) {
            $imageResult = uploadFile($request->file('image'), 'portal_users', 'image', null, 5 * 1024 * 1024);

            if (isset($imageResult['error'])) {
                return $this->errorResponse($imageResult['error']);
            }

            $data['image'] = 'storage/' . $imageResult['filePath'];
        }

        PortalUser::create($data);

        return $this->successResponse('User has been added successfully.');
    }

    public function show(Request $request, string $token): string
    {
        $id = decryptIdFromUrl($token);

        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_LISTING)) {
            return $this->errorResponse('Access denied');
        }

        $row = PortalUser::find($id);

        if (!$row) {
            return json_encode(['responseCode' => 0, 'html' => '']);
        }

        $html = view('admin.acl.portalUser.inc_show', ['row' => $row])->render();

        return json_encode(['responseCode' => 1, 'html' => $html]);
    }

    public function edit(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);

        if ($id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }

        $row = PortalUser::find($id);

        if (!$row) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }

        try {
            $html = view('admin.acl.portalUser.edit', [
                'pageTitle' => 'Portal Users',
                'row' => $row
            ])->render();

            return response()->json(['responseCode' => 1, 'html' => $html]);

        } catch (\Throwable $e) {
            return response()->json([
                'responseCode' => 0,
                'm  sg' => 'Error loading form: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, string $token): string
    {
        $id = decryptIdFromUrl($token);

        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return $this->errorResponse('Access denied');
        }

        $user = PortalUser::find($id);

        if (!$user) {
            return $this->errorResponse('Record not found');
        }

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:portal_users,email,' . $id,
            'phone_number' => 'nullable|string|max:30',
            'gender' => 'nullable|string|in:male,female,other',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'phone_number', 'gender']);

        if ($request->hasFile('image')) {

            if ($user->image && Storage::disk('public')->exists(str_replace('storage/', '', $user->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->image));
            }

            $imageResult = uploadFile($request->file('image'), 'portal_users', 'image', null, 5 * 1024 * 1024);

            if (isset($imageResult['error'])) {
                return $this->errorResponse($imageResult['error']);
            }

            $data['image'] = 'storage/' . $imageResult['filePath'];
        }

        $user->update($data);

        return $this->successResponse('User has been updated successfully.');
    }

    public function toggleStatus(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);

        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }

        $user = PortalUser::find($id);

        if (!$user) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }

        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();

        return response()->json([
            'responseCode' => 1,
            'status' => (int) $user->status
        ]);
    }

    public function destroy(string $token): string
    {
        $id = decryptIdFromUrl($token);

        if ($id === null || !validatePermissions(self::PERMISSION_DELETE)) {
            abort($id === null ? 404 : 403);
        }

        $user = PortalUser::find($id);

        if ($user) {
            if ($user->image && Storage::disk('public')->exists(str_replace('storage/', '', $user->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->image));
            }

            $user->delete();
        }

        return $this->successResponse('User has been deleted successfully.');
    }
}
