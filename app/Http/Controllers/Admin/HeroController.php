<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HeroController extends Controller
{
    use ResponseTrait;

    private const PERMISSION_LISTING = 'admin/acl/hero';
    private const PERMISSION_ADD = 'admin/acl/hero/add';
    private const PERMISSION_EDIT = 'admin/acl/hero/edit';
    private const PERMISSION_DELETE = 'admin/acl/hero/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }
        $query = Hero::orderBy('id');
        if ($request->filled('search')) {
            $q = sanitizeInput((string) $request->input('search', ''), 'string');
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%')
                    ->orWhere('video_link', 'like', '%' . $q . '%');
            });
        }
        $heroItems = $query->paginate(10)->withQueryString();
        return view('admin.hero.listing', [
            'pageTitle' => 'Hero',
            'heroItems' => $heroItems,
        ]);
    }

    public function create(): JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        try {
            $html = view('admin.hero.add', ['pageTitle' => 'Hero'])->render();
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
            'description' => 'required|string',
            'video' => 'required|file|mimes:mp4,mpeg,webm,quicktime|max:102400',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'video.required' => 'Video file is required.',
            'video.mimes' => 'Only MP4, MPEG, WebM or MOV video files are allowed.',
            'video.max' => 'Video must not be greater than 100 MB.',
            'description.required' => 'Description is required.',
            'image.required' => 'Image is required.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
        ]);
        $data = $request->only(['title', 'description']);
        $data['status'] = 1;
        $imageResult = uploadFile($request->file('image'), 'hero', 'image', null, 5 * 1024 * 1024);
        if (isset($imageResult['error'])) {
            return $this->errorResponse($imageResult['error']);
        }
        $data['image'] = 'storage/' . $imageResult['filePath'];
        $videoResult = uploadFile($request->file('video'), 'hero/videos', 'video', null, 100 * 1024 * 1024);
        if (isset($videoResult['error'])) {
            return $this->errorResponse($videoResult['error']);
        }
        $data['video'] = 'storage/' . $videoResult['filePath'];
        Hero::create($data);
        return $this->successResponse('Hero item has been added successfully.');
    }

    public function edit(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $row = Hero::find($id);
        if (!$row) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        try {
            $html = view('admin.hero.edit', ['pageTitle' => 'Hero', 'row' => $row])->render();
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
        $hero = Hero::find($id);
        if (!$hero) {
            return $this->errorResponse('Record not found');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'video' => [Rule::requiredIf(!$hero->video), 'nullable', 'file', 'mimes:mp4,mpeg,webm,quicktime', 'max:102400'],
            'image' => [Rule::requiredIf(!$hero->image), 'nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ], [
            'video.required' => 'Video file is required when no video is set.',
            'video.mimes' => 'Only MP4, MPEG, WebM or MOV video files are allowed.',
            'video.max' => 'Video must not be greater than 100 MB.',
            'description.required' => 'Description is required.',
            'image.required' => 'Image is required when no image is set.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
        ]);
        $data = $request->only(['title', 'description']);
        if ($request->hasFile('image')) {
            if ($hero->image && Storage::disk('public')->exists(str_replace('storage/', '', $hero->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $hero->image));
            }
            $imageResult = uploadFile($request->file('image'), 'hero', 'image', null, 5 * 1024 * 1024);
            if (isset($imageResult['error'])) {
                return $this->errorResponse($imageResult['error']);
            }
            $data['image'] = 'storage/' . $imageResult['filePath'];
        }
        if ($request->hasFile('video')) {
            if ($hero->video && Storage::disk('public')->exists(str_replace('storage/', '', $hero->video))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $hero->video));
            }
            $videoResult = uploadFile($request->file('video'), 'hero/videos', 'video', null, 100 * 1024 * 1024);
            if (isset($videoResult['error'])) {
                return $this->errorResponse($videoResult['error']);
            }
            $data['video'] = 'storage/' . $videoResult['filePath'];
        }
        $hero->update($data);
        return $this->successResponse('Hero item has been updated successfully.');
    }

    public function toggleStatus(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $hero = Hero::find($id);
        if (!$hero) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        $hero->status = (int) $hero->status === 1 ? 0 : 1;
        $hero->save();
        return response()->json(['responseCode' => 1, 'status' => (int) $hero->status]);
    }

    public function destroy(string $token): string
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_DELETE)) {
            abort($id === null ? 404 : 403);
        }
        $hero = Hero::find($id);
        if ($hero) {
            if ($hero->image && Storage::disk('public')->exists(str_replace('storage/', '', $hero->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $hero->image));
            }
            if ($hero->video && Storage::disk('public')->exists(str_replace('storage/', '', $hero->video))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $hero->video));
            }
            $hero->delete();
        }
        return $this->successResponse('Hero item has been deleted successfully.');
    }
}
