<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentationController extends Controller
{
    use ResponseTrait;

    private const PERMISSION_LISTING = 'admin/acl/documentation';
    private const PERMISSION_ADD = 'admin/acl/documentation/add';
    private const PERMISSION_EDIT = 'admin/acl/documentation/edit';
    private const PERMISSION_DELETE = 'admin/acl/documentation/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }
        $query = Documentation::orderBy('id', 'desc');
        if ($request->filled('search')) {
            $q = sanitizeInput((string) $request->input('search', ''), 'string');
            $query->where('title', 'like', '%' . $q . '%');
        }
        $items = $query->paginate(10)->withQueryString();
        return view('admin.documentation.listing', [
            'pageTitle' => 'Documentation',
            'items' => $items,
        ]);
    }

    public function create(): JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        try {
            $html = view('admin.documentation.add', ['pageTitle' => 'Documentation'])->render();
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:65535',
            'published_in_date' => ['required', 'date', 'before_or_equal:today'],
            'image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'report_pdf' => 'required|file|mimes:pdf|mimetypes:application/pdf|max:51200',
        ], [
            'published_in_date.required' => 'Published in date is required.',
            'published_in_date.before_or_equal' => 'Published date cannot be in the future.',
            'image.required' => 'Image is required.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
            'report_pdf.required' => 'Report PDF is required.',
            'report_pdf.mimes' => 'Only PDF files are allowed.',
        ]);
        $data = $request->only(['title', 'description', 'published_in_date']);
        $data['status'] = 1;
        $imageResult = uploadFile($request->file('image'), 'documentation', 'image', null, 5 * 1024 * 1024);
        if (isset($imageResult['error'])) {
            return $this->errorResponse($imageResult['error']);
        }
        $data['image'] = 'storage/' . $imageResult['filePath'];
        $pdfResult = uploadFile($request->file('report_pdf'), 'documentation', 'pdf', null, 50 * 1024 * 1024);
        if (isset($pdfResult['error'])) {
            return $this->errorResponse($pdfResult['error']);
        }
        $data['report_pdf'] = 'storage/' . $pdfResult['filePath'];
        Documentation::create($data);
        return $this->successResponse('Documentation has been added successfully.');
    }

    public function edit(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $row = Documentation::find($id);
        if (!$row) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        try {
            $html = view('admin.documentation.edit', ['pageTitle' => 'Documentation', 'row' => $row])->render();
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
        $doc = Documentation::find($id);
        if (!$doc) {
            return $this->errorResponse('Record not found');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:65535',
            'published_in_date' => ['required', 'date', 'before_or_equal:today'],
            'image' => [Rule::requiredIf(!$doc->image), 'nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'report_pdf' => [Rule::requiredIf(!$doc->report_pdf), 'nullable', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:51200'],
        ], [
            'published_in_date.required' => 'Published in date is required.',
            'published_in_date.before_or_equal' => 'Published date cannot be in the future.',
            'image.required' => 'Image is required when no image is set.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
            'report_pdf.required' => 'Report PDF is required when no file is set.',
            'report_pdf.mimes' => 'Only PDF files are allowed.',
        ]);
        $data = $request->only(['title', 'description', 'published_in_date']);
        if ($request->hasFile('image')) {
            if ($doc->image && Storage::disk('public')->exists(str_replace('storage/', '', $doc->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $doc->image));
            }
            $imageResult = uploadFile($request->file('image'), 'documentation', 'image', null, 5 * 1024 * 1024);
            if (isset($imageResult['error'])) {
                return $this->errorResponse($imageResult['error']);
            }
            $data['image'] = 'storage/' . $imageResult['filePath'];
        }
        if ($request->hasFile('report_pdf')) {
            if ($doc->report_pdf && Storage::disk('public')->exists(str_replace('storage/', '', $doc->report_pdf))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $doc->report_pdf));
            }
            $pdfResult = uploadFile($request->file('report_pdf'), 'documentation','pdf', null, 50 * 1024 * 1024);
            if (isset($pdfResult['error'])) {
                return $this->errorResponse($pdfResult['error']);
            }
            $data['report_pdf'] = 'storage/' . $pdfResult['filePath'];
        }
        $doc->update($data);
        return $this->successResponse('Documentation has been updated successfully.');
    }

    public function toggleStatus(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $doc = Documentation::find($id);
        if (!$doc) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        $doc->status = (int) $doc->status === 1 ? 0 : 1;
        $doc->save();
        return response()->json(['responseCode' => 1, 'status' => (int) $doc->status]);
    }

    public function destroy(string $token): string
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_DELETE)) {
            abort($id === null ? 404 : 403);
        }
        $doc = Documentation::find($id);
        if ($doc) {
            if ($doc->image && Storage::disk('public')->exists(str_replace('storage/', '', $doc->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $doc->image));
            }
            if ($doc->report_pdf && Storage::disk('public')->exists(str_replace('storage/', '', $doc->report_pdf))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $doc->report_pdf));
            }
            $doc->delete();
        }
        return $this->successResponse('Documentation has been deleted successfully.');
    }
}
