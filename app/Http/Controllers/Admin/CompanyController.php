<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CompanyController extends Controller
{
    use ResponseTrait;

    private const PERMISSION_LISTING = 'admin/acl/companies';
    private const PERMISSION_ADD = 'admin/acl/companies/add';
    private const PERMISSION_EDIT = 'admin/acl/companies/edit';
    private const PERMISSION_DELETE = 'admin/acl/companies/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }
        $query = Company::orderBy('name');
        if ($request->filled('search')) {
            $q = sanitizeInput((string) $request->input('search', ''), 'string');
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', '%' . $q . '%')
                    ->orWhere('type', 'like', '%' . $q . '%');
            });
        }
        $items = $query->paginate(10)->withQueryString();
        return view('admin.companies.listing', [
            'pageTitle' => 'Companies',
            'items' => $items,
        ]);
    }

    public function create(): JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        try {
            $html = view('admin.companies.add', ['pageTitle' => 'Companies'])->render();
            return response()->json(['responseCode' => 1, 'html' => $html]);
        } catch (\Throwable $e) {
            return response()->json(['responseCode' => 0,   'msg' => 'Error loading form:  Try again later.' ]);
        }
    }

    public function store(Request $request): string
    {
        if (!$request->ajax() || !validatePermissions(self::PERMISSION_ADD)) {
            return $this->errorResponse('Access denied');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
        ]);
        Company::create($request->only(['name', 'type']));
        return $this->successResponse('Company has been added successfully.');
    }

    public function edit(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $row = Company::find($id);
        if (!$row) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        try {
            $html = view('admin.companies.edit', ['pageTitle' => 'Companies', 'row' => $row])->render();
            return response()->json(['responseCode' => 1, 'html' => $html]);
        } catch (\Throwable $e) {
            return response()->json(['responseCode' => 0,   'msg' => 'Error loading form:  Try again later.' ]);
        }
    }

    public function update(Request $request, string $token): string
    {
        $id = decryptIdFromUrl($token);
        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return $this->errorResponse('Access denied');
        }
        $row = Company::find($id);
        if (!$row) {
            return $this->errorResponse('Record not found');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
        ]);
        $row->update($request->only(['name', 'type']));
        return $this->successResponse('Company has been updated successfully.');
    }

    public function destroy(string $token): string
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_DELETE)) {
            abort($id === null ? 404 : 403);
        }
        $row = Company::find($id);
        if ($row) {
            $row->delete();
        }
        return $this->successResponse('Company has been deleted successfully.');
    }
}
