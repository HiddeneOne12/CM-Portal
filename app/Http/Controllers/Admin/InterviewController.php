<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interview;
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

class InterviewController extends Controller
{
    use ResponseTrait;

    private const PERMISSION_LISTING = 'admin/acl/interviews';
    private const PERMISSION_ADD = 'admin/acl/interviews/add';
    private const PERMISSION_EDIT = 'admin/acl/interviews/edit';
    private const PERMISSION_DELETE = 'admin/acl/interviews/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }
        $query = Interview::with('participant')->orderBy('id');
        if ($request->filled('search')) {
            $q = sanitizeInput((string) $request->input('search', ''), 'string');
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', '%' . $q . '%')
                    ->orWhere('video_link', 'like', '%' . $q . '%');
            });
        }
        $interviewItems = $query->paginate(10)->withQueryString();
        return view('admin.interviews.listing', [
            'pageTitle' => 'Interviews',
            'interviewItems' => $interviewItems,
        ]);
    }

    public function create(): JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        try {
            $participants = Participant::enabled()->orderBy('name')->get(['id', 'name', 'position']);
            $html = view('admin.interviews.add', ['pageTitle' => 'Interviews', 'participants' => $participants])->render();
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
            'participant_id' => 'nullable|integer|exists:participants,id',
            'video' => 'required_without:interview_image|nullable|file|mimes:mp4,mpeg,webm,quicktime|max:102400',
            'interview_image' => 'required_without:video|nullable|image|mimes:jpeg,jpg,png|max:5120',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'video.required_without' => 'Either Video or Interview image is required.',
            'interview_image.required_without' => 'Either Video or Interview image is required.',
            'video.mimes' => 'Only MP4, MPEG, WebM or MOV video files are allowed.',
            'video.max' => 'Video must not be greater than 100 MB.',
            'interview_image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.required' => 'Image is required.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
        ]);
        $data = $request->only(['title', 'description', 'participant_id']);
        $data['status'] = 1;
        $imageResult = uploadFile($request->file('image'), 'interviews', 'image', null, 5 * 1024 * 1024);
        if (isset($imageResult['error'])) {
            return $this->errorResponse($imageResult['error']);
        }
        $data['image'] = 'storage/' . $imageResult['filePath'];
        if ($request->hasFile('video')) {
            $videoResult = uploadFile($request->file('video'), 'interviews/videos', 'video', null, 100 * 1024 * 1024);
            if (isset($videoResult['error'])) {
                return $this->errorResponse($videoResult['error']);
            }
            $data['video'] = 'storage/' . $videoResult['filePath'];
            $data['interview_image'] = null;
        } else {
            $data['video'] = null;
            $imgResult = uploadFile($request->file('interview_image'), 'interviews/interview_images', 'image', null, 5 * 1024 * 1024);
            if (isset($imgResult['error'])) {
                return $this->errorResponse($imgResult['error']);
            }
            $data['interview_image'] = 'storage/' . $imgResult['filePath'];
        }
        Interview::create($data);
        return $this->successResponse('Interview has been added successfully.');
    }

    public function edit(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $row = Interview::find($id);
        if (!$row) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        try {
            $participants = Participant::enabled()->orderBy('name')->get(['id', 'name', 'position']);
            $html = view('admin.interviews.edit', ['pageTitle' => 'Interviews', 'row' => $row, 'participants' => $participants])->render();
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
        $interview = Interview::find($id);
        if (!$interview) {
            return $this->errorResponse('Record not found');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:65535',
            'participant_id' => 'nullable|integer|exists:participants,id',
            'video' => [
                Rule::requiredIf(!$interview->video && !$request->hasFile('interview_image') && !$interview->interview_image),
                'nullable',
                'file',
                'mimes:mp4,mpeg,webm,quicktime',
                'max:102400',
            ],
            'interview_image' => [
                Rule::requiredIf(!$interview->interview_image && !$request->hasFile('video') && !$interview->video),
                'nullable',
                'image',
                'mimes:jpeg,jpg,png',
                'max:5120',
            ],
            'image' => [Rule::requiredIf(!$interview->image), 'nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ], [
            'video.required' => 'Either Video or Interview image is required.',
            'interview_image.required' => 'Either Video or Interview image is required.',
            'video.mimes' => 'Only MP4, MPEG, WebM or MOV video files are allowed.',
            'video.max' => 'Video must not be greater than 100 MB.',
            'interview_image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.required' => 'Image is required when no image is set.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
        ]);
        $data = $request->only(['title', 'description', 'participant_id']);
        if ($request->hasFile('image')) {
            if ($interview->image && Storage::disk('public')->exists(str_replace('storage/', '', $interview->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $interview->image));
            }
            $imageResult = uploadFile($request->file('image'), 'interviews', 'image', null, 5 * 1024 * 1024);
            if (isset($imageResult['error'])) {
                return $this->errorResponse($imageResult['error']);
            }
            $data['image'] = 'storage/' . $imageResult['filePath'];
        }
        if ($request->hasFile('video')) {
            if ($interview->video && Storage::disk('public')->exists(str_replace('storage/', '', $interview->video))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $interview->video));
            }
            $videoResult = uploadFile($request->file('video'), 'interviews/videos', 'video', null, 100 * 1024 * 1024);
            if (isset($videoResult['error'])) {
                return $this->errorResponse($videoResult['error']);
            }
            $data['video'] = 'storage/' . $videoResult['filePath'];
        }
        if ($request->hasFile('interview_image')) {
            if ($interview->interview_image && Storage::disk('public')->exists(str_replace('storage/', '', $interview->interview_image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $interview->interview_image));
            }
            $imgResult = uploadFile($request->file('interview_image'), 'interviews/interview_images', 'image', null, 5 * 1024 * 1024);
            if (isset($imgResult['error'])) {
                return $this->errorResponse($imgResult['error']);
            }
            $data['interview_image'] = 'storage/' . $imgResult['filePath'];
        }
        $interview->update($data);
        return $this->successResponse('Interview has been updated successfully.');
    }

    public function toggleStatus(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $interview = Interview::find($id);
        if (!$interview) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        $interview->status = (int) $interview->status === 1 ? 0 : 1;
        $interview->save();
        return response()->json(['responseCode' => 1, 'status' => (int) $interview->status]);
    }

    public function destroy(string $token): string
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_DELETE)) {
            abort($id === null ? 404 : 403);
        }
        $interview = Interview::find($id);
        if ($interview) {
            if ($interview->image && Storage::disk('public')->exists(str_replace('storage/', '', $interview->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $interview->image));
            }
            if ($interview->video && Storage::disk('public')->exists(str_replace('storage/', '', $interview->video))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $interview->video));
            }
            if ($interview->interview_image && Storage::disk('public')->exists(str_replace('storage/', '', $interview->interview_image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $interview->interview_image));
            }
            $interview->delete();
        }
        return $this->successResponse('Interview has been deleted successfully.');
    }
}
