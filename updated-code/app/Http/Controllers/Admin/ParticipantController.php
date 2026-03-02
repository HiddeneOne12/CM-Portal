<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Participant;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    use ResponseTrait;

    private const PERMISSION_LISTING = 'admin/acl/participants';
    private const PERMISSION_ADD = 'admin/acl/participants/add';
    private const PERMISSION_EDIT = 'admin/acl/participants/edit';
    private const PERMISSION_DELETE = 'admin/acl/participants/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }
        $query = Participant::with('company')->orderBy('display_order')->orderBy('name');
        if ($request->filled('search')) {
            $q = sanitizeInput((string) $request->input('search', ''), 'string');
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', '%' . $q . '%')
                    ->orWhere('position', 'like', '%' . $q . '%')
                    ->orWhereHas('company', fn ($c) => $c->where('name', 'like', '%' . $q . '%'));
            });
        }
        $items = $query->paginate(10)->withQueryString();
        return view('admin.participants.listing', [
            'pageTitle' => 'Participants',
            'items' => $items,
        ]);
    }

    public function create(): JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        try {
            $companies = Company::orderBy('name')->get();
            $html = view('admin.participants.add', ['pageTitle' => 'Participants', 'companies' => $companies])->render();
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
            'position' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'position.required' => 'Position is required.',
            'company_id.required' => 'Company is required.',
            'image.required' => 'Image is required.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
        ]);
        $data = $request->only(['name', 'position', 'company_id']);
        $data['status'] = 1;
        $data['display_order'] = 0;
        $imageResult = uploadFile($request->file('image'), 'participants', 'image', null, 5 * 1024 * 1024);
        if (isset($imageResult['error'])) {
            return $this->errorResponse($imageResult['error']);
        }
        $data['image'] = 'storage/' . $imageResult['filePath'];
        Participant::create($data);
        return $this->successResponse('Participant has been added successfully.');
    }

    public function edit(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $row = Participant::with('company')->find($id);
        if (!$row) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        try {
            $companies = Company::orderBy('name')->get();
            $html = view('admin.participants.edit', ['pageTitle' => 'Participants', 'row' => $row, 'companies' => $companies])->render();
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
        $row = Participant::find($id);
        if (!$row) {
            return $this->errorResponse('Record not found');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'image' => [Rule::requiredIf(!$row->image), 'nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ], [
            'position.required' => 'Position is required.',
            'company_id.required' => 'Company is required.',
            'image.required' => 'Image is required when no image is set.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
        ]);
        $data = $request->only(['name', 'position', 'company_id']);
        if ($request->hasFile('image')) {
            if ($row->image && Storage::disk('public')->exists(str_replace('storage/', '', $row->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $row->image));
            }
            $imageResult = uploadFile($request->file('image'), 'participants', 'image', null, 5 * 1024 * 1024);
            if (isset($imageResult['error'])) {
                return $this->errorResponse($imageResult['error']);
            }
            $data['image'] = 'storage/' . $imageResult['filePath'];
        }
        $row->update($data);
        return $this->successResponse('Participant has been updated successfully.');
    }

    public function toggleStatus(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $row = Participant::find($id);
        if (!$row) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        $row->status = (int) $row->status === 1 ? 0 : 1;
        $row->save();
        return response()->json(['responseCode' => 1, 'status' => (int) $row->status]);
    }

    public function destroy(string $token): string
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_DELETE)) {
            abort($id === null ? 404 : 403);
        }
        $row = Participant::find($id);
        if ($row) {
            if ($row->image && Storage::disk('public')->exists(str_replace('storage/', '', $row->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $row->image));
            }
            $row->delete();
        }
        return $this->successResponse('Participant has been deleted successfully.');
    }
}
