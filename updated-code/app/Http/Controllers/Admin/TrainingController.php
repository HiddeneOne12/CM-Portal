<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TrainingController extends Controller
{
    use ResponseTrait;

    private const PERMISSION_LISTING = 'admin/acl/training';
    private const PERMISSION_ADD = 'admin/acl/training/add';
    private const PERMISSION_EDIT = 'admin/acl/training/edit';
    private const PERMISSION_DELETE = 'admin/acl/training/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }
        $query = Training::orderBy('id', 'desc');
        if ($request->filled('search')) {
            $q = sanitizeInput((string) $request->input('search', ''), 'string');
            $query->where('title', 'like', '%' . $q . '%');
        }
        $items = $query->paginate(10)->withQueryString();
        return view('admin.training.listing', [
            'pageTitle' => 'Training',
            'items' => $items,
        ]);
    }

    public function create(): JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        try {
            $html = view('admin.training.add', ['pageTitle' => 'Training'])->render();
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
            'image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'video' => 'required_without:training_image|nullable|file|mimes:mp4,mpeg,webm,quicktime|max:102400',
            'training_image' => 'required_without:video|nullable|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'video.required_without' => 'Either Video or Training image is required.',
            'training_image.required_without' => 'Either Video or Training image is required.',
            'video.mimes' => 'Only MP4, WebM or MOV video files are allowed.',
            'video.max' => 'Video must not be greater than 100 MB.',
            'training_image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.required' => 'Image is required.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
        ]);
        $data = $request->only(['title', 'description']);
        $data['status'] = 1;
        $imageResult = uploadFile($request->file('image'), 'training', 'image', null, 5 * 1024 * 1024);
        if (isset($imageResult['error'])) {
            return $this->errorResponse($imageResult['error']);
        }
        $data['image'] = 'storage/' . $imageResult['filePath'];
        if ($request->hasFile('video')) {
            $videoResult = uploadFile($request->file('video'), 'training/videos', 'video', null, 100 * 1024 * 1024);
            if (isset($videoResult['error'])) {
                return $this->errorResponse($videoResult['error']);
            }
            $data['video'] = 'storage/' . $videoResult['filePath'];
            $data['training_image'] = null;
        } else {
            $data['video'] = null;
            $imgResult = uploadFile($request->file('training_image'), 'training/images', 'image', null, 5 * 1024 * 1024);
            if (isset($imgResult['error'])) {
                return $this->errorResponse($imgResult['error']);
            }
            $data['training_image'] = 'storage/' . $imgResult['filePath'];
        }
        Training::create($data);
        return $this->successResponse('Training has been added successfully.');
    }

    public function edit(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $row = Training::find($id);
        if (!$row) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        try {
            $html = view('admin.training.edit', ['pageTitle' => 'Training', 'row' => $row])->render();
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
        $training = Training::find($id);
        if (!$training) {
            return $this->errorResponse('Record not found');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:65535',
            'video' => [
                Rule::requiredIf(!$training->video && !$request->hasFile('training_image') && !$training->training_image),
                'nullable',
                'file',
                'mimes:mp4,mpeg,webm,quicktime',
                'max:102400',
            ],
            'training_image' => [
                Rule::requiredIf(!$training->training_image && !$request->hasFile('video') && !$training->video),
                'nullable',
                'image',
                'mimes:jpeg,jpg,png',
                'max:5120',
            ],
            'image' => [Rule::requiredIf(!$training->image), 'nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ], [
            'video.required' => 'Either Video or Training image is required.',
            'training_image.required' => 'Either Video or Training image is required.',
            'video.mimes' => 'Only MP4, WebM or MOV video files are allowed.',
            'video.max' => 'Video must not be greater than 100 MB.',
            'training_image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.required' => 'Image is required when no image is set.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
        ]);
        $data = $request->only(['title', 'description']);
        if ($request->hasFile('image')) {
            if ($training->image && Storage::disk('public')->exists(str_replace('storage/', '', $training->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $training->image));
            }
            $imageResult = uploadFile($request->file('image'), 'training', 'image', null, 5 * 1024 * 1024);
            if (isset($imageResult['error'])) {
                return $this->errorResponse($imageResult['error']);
            }
            $data['image'] = 'storage/' . $imageResult['filePath'];
        }
        if ($request->hasFile('video')) {
            if ($training->video && Storage::disk('public')->exists(str_replace('storage/', '', $training->video))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $training->video));
            }
            $videoResult = uploadFile($request->file('video'), 'training/videos', 'video', null, 100 * 1024 * 1024);
            if (isset($videoResult['error'])) {
                return $this->errorResponse($videoResult['error']);
            }
            $data['video'] = 'storage/' . $videoResult['filePath'];
            $data['training_image'] = null;
            if ($training->training_image && Storage::disk('public')->exists(str_replace('storage/', '', $training->training_image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $training->training_image));
            }
        }
        if ($request->hasFile('training_image')) {
            if ($training->training_image && Storage::disk('public')->exists(str_replace('storage/', '', $training->training_image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $training->training_image));
            }
            $imgResult = uploadFile($request->file('training_image'), 'training/images', 'image', null, 5 * 1024 * 1024);
            if (isset($imgResult['error'])) {
                return $this->errorResponse($imgResult['error']);
            }
            $data['training_image'] = 'storage/' . $imgResult['filePath'];
            $data['video'] = null;
            if ($training->video && Storage::disk('public')->exists(str_replace('storage/', '', $training->video))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $training->video));
            }
        }
        $training->update($data);
        return $this->successResponse('Training has been updated successfully.');
    }

    public function toggleStatus(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $training = Training::find($id);
        if (!$training) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        $training->status = (int) $training->status === 1 ? 0 : 1;
        $training->save();
        return response()->json(['responseCode' => 1, 'status' => (int) $training->status]);
    }

    public function destroy(string $token): string
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_DELETE)) {
            abort($id === null ? 404 : 403);
        }
        $training = Training::find($id);
        if ($training) {
            if ($training->image && Storage::disk('public')->exists(str_replace('storage/', '', $training->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $training->image));
            }
            if ($training->video && Storage::disk('public')->exists(str_replace('storage/', '', $training->video))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $training->video));
            }
            if ($training->training_image && Storage::disk('public')->exists(str_replace('storage/', '', $training->training_image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $training->training_image));
            }
            $training->delete();
        }
        return $this->successResponse('Training has been deleted successfully.');
    }
}
