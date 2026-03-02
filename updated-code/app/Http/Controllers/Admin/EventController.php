<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\EventParticipant;
use App\Models\EventParticipantDocument;
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

class EventController extends Controller
{
    use ResponseTrait;

    private const PERMISSION_LISTING = 'admin/acl/events';
    private const PERMISSION_ADD = 'admin/acl/events/add';
    private const PERMISSION_EDIT = 'admin/acl/events/edit';
    private const PERMISSION_DELETE = 'admin/acl/events/delete';

    public function index(Request $request): View
    {
        if (!validatePermissions(self::PERMISSION_LISTING)) {
            abort(403);
        }
        $query = Event::with('company')->orderBy('event_date', 'desc')->orderBy('id');
        if ($request->filled('search')) {
            $q = sanitizeInput((string) $request->input('search', ''), 'string');
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%');
            });
        }
        $eventItems = $query->paginate(10)->withQueryString();
        return view('admin.events.listing', [
            'pageTitle' => 'Events',
            'eventItems' => $eventItems,
        ]);
    }

    public function show(string $token): View|RedirectResponse
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_LISTING)) {
            abort($id === null ? 404 : 403);
        }
        $event = Event::with([
            'company',
            'eventImages',
            'eventParticipants.participant.company',
            'eventParticipants.eventParticipantDocuments',
        ])->find($id);
        if (!$event) {
            return Redirect::route('admin.acl.events.listing')->with('error', 'Event not found.');
        }
        $participants = Participant::enabled()->with('company')->orderBy('display_order')->orderBy('name')->get();
        return view('admin.events.show', [
            'pageTitle' => 'Event Details',
            'event' => $event,
            'participants' => $participants,
        ]);
    }

    public function storeParticipant(Request $request, string $token): JsonResponse
    {
        $eventId = decryptIdFromUrl($token);
        if (!$request->ajax() || $eventId === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $event = Event::find($eventId);
        if (!$event) {
            return response()->json(['responseCode' => 0, 'msg' => 'Event not found']);
        }
        $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'topic' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:65535',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'video' => 'nullable|file|mimes:mp4,mpeg,webm,quicktime|max:102400',
        ]);
        if (EventParticipant::where('event_id', $eventId)->where('participant_id', $request->participant_id)->exists()) {
            return response()->json(['responseCode' => 0, 'msg' => 'This participant is already added to the event.']);
        }
        $startTime = $request->start_time ? substr($request->start_time, 0, 5) : null;
        $endTime = $request->end_time ? substr($request->end_time, 0, 5) : null;
        if ($startTime && $endTime && $startTime >= $endTime) {
            return response()->json(['responseCode' => 0, 'msg' => 'Start time must be before end time.']);
        }
        $eventStart = $event->start_time ? substr($event->start_time, 0, 5) : null;
        $eventEnd = $event->end_time ? substr($event->end_time, 0, 5) : null;
        if ($startTime && $eventStart && $startTime < $eventStart) {
            return response()->json(['responseCode' => 0, 'msg' => 'Participant start time cannot be before the event start time.']);
        }
        if ($endTime && $eventEnd && $endTime > $eventEnd) {
            return response()->json(['responseCode' => 0, 'msg' => 'Participant end time cannot be after the event end time.']);
        }
        if ($startTime && $endTime && $this->eventParticipantTimeOverlaps($eventId, $startTime, $endTime, null)) {
            return response()->json(['responseCode' => 0, 'msg' => 'This time slot overlaps with another participant. Please choose a different time.']);
        }
        $maxOrder = (int) EventParticipant::where('event_id', $eventId)->max('display_order');
        $data = [
            'event_id' => $eventId,
            'participant_id' => $request->participant_id,
            'topic' => $request->topic,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'display_order' => $maxOrder + 1,
        ];
        if ($request->hasFile('video')) {
            $videoResult = uploadFile($request->file('video'), 'event_participants/videos', 'video', null, 100 * 1024 * 1024);
            if (isset($videoResult['error'])) {
                return response()->json(['responseCode' => 0, 'msg' => $videoResult['error']]);
            }
            $data['video'] = 'storage/' . $videoResult['filePath'];
            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            $imageResult = uploadFile($request->file('image'), 'event_participants', 'image', null, 5 * 1024 * 1024);
            if (isset($imageResult['error'])) {
                return response()->json(['responseCode' => 0, 'msg' => $imageResult['error']]);
            }
            $data['image'] = 'storage/' . $imageResult['filePath'];
            $data['video'] = null;
        }
        $ep = EventParticipant::create($data);
        $ep->load('participant.company');
        $p = $ep->participant;
        return response()->json([
            'responseCode' => 1,
            'msg' => 'Participant added.',
            'ep_id' => $ep->id,
            'participant' => [
                'id' => $p->id,
                'name' => $p->name,
                'position' => $p->position ?? '',
                'company_name' => $p->company ? $p->company->name : '',
                'image' => $p->image ?? '',
                'ep_image' => $ep->image ?? '',
                'ep_video' => $ep->video ?? '',
                'topic' => $ep->topic ?? '',
                'description' => $ep->description ?? '',
                'start_time' => $ep->start_time ? substr($ep->start_time, 0, 5) : '',
                'end_time' => $ep->end_time ? substr($ep->end_time, 0, 5) : '',
            ],
        ]);
    }

    public function updateParticipant(Request $request, $id): JsonResponse
    {
        $id = sanitizeInput((string) $id, 'int');
        if (!$request->ajax() || !validatePermissions(self::PERMISSION_EDIT) || !isInteger($id)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $ep = EventParticipant::find($id);
        if (!$ep) {
            return response()->json(['responseCode' => 0, 'msg' => 'Participant record not found']);
        }
        $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'topic' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:65535',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'video' => 'nullable|file|mimes:mp4,mpeg,webm,quicktime|max:102400',
        ]);
        $otherWithSameParticipant = EventParticipant::where('event_id', $ep->event_id)
            ->where('participant_id', $request->participant_id)
            ->where('id', '!=', $ep->id)
            ->exists();
        if ($otherWithSameParticipant) {
            return response()->json(['responseCode' => 0, 'msg' => 'This participant is already added to the event.']);
        }
        $startTime = $request->start_time ? substr($request->start_time, 0, 5) : null;
        $endTime = $request->end_time ? substr($request->end_time, 0, 5) : null;
        if ($startTime && $endTime && $startTime >= $endTime) {
            return response()->json(['responseCode' => 0, 'msg' => 'Start time must be before end time.']);
        }
        $event = $ep->event;
        $eventStart = $event && $event->start_time ? substr($event->start_time, 0, 5) : null;
        $eventEnd = $event && $event->end_time ? substr($event->end_time, 0, 5) : null;
        if ($startTime && $eventStart && $startTime < $eventStart) {
            return response()->json(['responseCode' => 0, 'msg' => 'Participant start time cannot be before the event start time.']);
        }
        if ($endTime && $eventEnd && $endTime > $eventEnd) {
            return response()->json(['responseCode' => 0, 'msg' => 'Participant end time cannot be after the event end time.']);
        }
        if ($startTime && $endTime && $this->eventParticipantTimeOverlaps($ep->event_id, $startTime, $endTime, $ep->id)) {
            return response()->json(['responseCode' => 0, 'msg' => 'This time slot overlaps with another participant. Please choose a different time.']);
        }
        $data = $request->only(['participant_id', 'topic', 'description', 'start_time', 'end_time']);
        if ($request->hasFile('video')) {
            if ($ep->video && Storage::disk('public')->exists(str_replace('storage/', '', $ep->video))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $ep->video));
            }
            if ($ep->image && Storage::disk('public')->exists(str_replace('storage/', '', $ep->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $ep->image));
            }
            $videoResult = uploadFile($request->file('video'), 'event_participants/videos', 'video', null, 100 * 1024 * 1024);
            if (isset($videoResult['error'])) {
                return response()->json(['responseCode' => 0, 'msg' => $videoResult['error']]);
            }
            $data['video'] = 'storage/' . $videoResult['filePath'];
            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($ep->image && Storage::disk('public')->exists(str_replace('storage/', '', $ep->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $ep->image));
            }
            if ($ep->video && Storage::disk('public')->exists(str_replace('storage/', '', $ep->video))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $ep->video));
            }
            $imageResult = uploadFile($request->file('image'), 'event_participants', 'image', null, 5 * 1024 * 1024);
            if (isset($imageResult['error'])) {
                return response()->json(['responseCode' => 0, 'msg' => $imageResult['error']]);
            }
            $data['image'] = 'storage/' . $imageResult['filePath'];
            $data['video'] = null;
        }
        $ep->update($data);
        $ep->load('participant.company');
        $p = $ep->participant;
        return response()->json([
            'responseCode' => 1,
            'msg' => 'Participant updated.',
            'participant' => [
                'id' => $p->id,
                'name' => $p->name,
                'position' => $p->position ?? '',
                'company_name' => $p->company ? $p->company->name : '',
                'image' => $p->image ?? '',
                'ep_image' => $ep->image ?? '',
                'ep_video' => $ep->video ?? '',
                'topic' => $ep->topic ?? '',
                'description' => $ep->description ?? '',
                'start_time' => $ep->start_time ? substr($ep->start_time, 0, 5) : '',
                'end_time' => $ep->end_time ? substr($ep->end_time, 0, 5) : '',
            ],
        ]);
    }

    public function destroyParticipant($id): JsonResponse
    {
        $id = sanitizeInput((string) $id, 'int');
        if (!request()->ajax() || !validatePermissions(self::PERMISSION_EDIT) || !isInteger($id)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $ep = EventParticipant::find($id);
        if (!$ep) {
            return response()->json(['responseCode' => 0, 'msg' => 'Participant record not found']);
        }
        if ($ep->image && Storage::disk('public')->exists(str_replace('storage/', '', $ep->image))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $ep->image));
        }
        if ($ep->video && Storage::disk('public')->exists(str_replace('storage/', '', $ep->video))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $ep->video));
        }
        foreach ($ep->eventParticipantDocuments as $doc) {
            if ($doc->file_path && Storage::disk('public')->exists(str_replace('storage/', '', $doc->file_path))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $doc->file_path));
            }
        }
        $ep->delete();
        return response()->json(['responseCode' => 1, 'msg' => 'Participant removed from event.']);
    }

    public function storeParticipantDocument(Request $request, $epId): JsonResponse
    {
        $epId = sanitizeInput((string) $epId, 'int');
        if (!$request->ajax() || !validatePermissions(self::PERMISSION_EDIT) || !isInteger($epId)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $ep = EventParticipant::find($epId);
        if (!$ep) {
            return response()->json(['responseCode' => 0, 'msg' => 'Participant record not found']);
        }
        $request->validate([
            'title' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:pdf|mimetypes:application/pdf|max:10240',
        ], ['file.required' => 'Please select a PDF file.', 'file.mimes' => 'Only PDF files are allowed.']);
        $file = $request->file('file');
        $uploadResult = uploadFile($file, 'event_participant_docs', 'pdf', null, 10 * 1024 * 1024);
        if (isset($uploadResult['error'])) {
            return response()->json(['responseCode' => 0, 'msg' => $uploadResult['error']]);
        }
        $doc = EventParticipantDocument::create([
            'event_participant_id' => $epId,
            'title' => trim($request->input('title', '') ?: 'Presentation'),
            'file_path' => 'storage/' . $uploadResult['filePath'],
            'file_size' => $file->getSize(),
        ]);
        return response()->json([
            'responseCode' => 1,
            'msg' => 'Document added.',
            'document' => ['id' => $doc->id, 'title' => $doc->title, 'file_path' => $doc->file_path],
        ]);
    }

    public function updateParticipantDocument(Request $request, $docId): JsonResponse
    {
        $docId = sanitizeInput((string) $docId, 'int');
        if (!$request->ajax() || !validatePermissions(self::PERMISSION_EDIT) || !isInteger($docId)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $doc = EventParticipantDocument::find($docId);
        if (!$doc) {
            return response()->json(['responseCode' => 0, 'msg' => 'Document not found']);
        }
        $request->validate([
            'title' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:10240',
        ], ['file.mimes' => 'Only PDF files are allowed.']);
        if ($request->filled('title')) {
            $doc->title = $request->input('title');
        }
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            if ($doc->file_path && Storage::disk('public')->exists(str_replace('storage/', '', $doc->file_path))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $doc->file_path));
            }
            $uploadResult = uploadFile($request->file('file'), 'event_participant_docs', 'pdf', null, 10 * 1024 * 1024);
            if (isset($uploadResult['error'])) {
                return response()->json(['responseCode' => 0, 'msg' => $uploadResult['error']]);
            }
            $doc->file_path = 'storage/' . $uploadResult['filePath'];
            $doc->file_size = $request->file('file')->getSize();
        }
        $doc->save();
        return response()->json([
            'responseCode' => 1,
            'msg' => 'Document updated.',
            'document' => ['id' => $doc->id, 'title' => $doc->title, 'file_path' => $doc->file_path],
        ]);
    }

    public function destroyParticipantDocument($docId): JsonResponse
    {
        $docId = sanitizeInput((string) $docId, 'int');
        if (!request()->ajax() || !validatePermissions(self::PERMISSION_EDIT) || !isInteger($docId)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $doc = EventParticipantDocument::find($docId);
        if (!$doc) {
            return response()->json(['responseCode' => 0, 'msg' => 'Document not found']);
        }
        if ($doc->file_path && Storage::disk('public')->exists(str_replace('storage/', '', $doc->file_path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $doc->file_path));
        }
        $doc->delete();
        return response()->json(['responseCode' => 1, 'msg' => 'Document removed.']);
    }

    public function create(): JsonResponse
    {
        if (!validatePermissions(self::PERMISSION_ADD)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        try {
            $companies = Company::orderBy('name')->get();
            $html = view('admin.events.add', ['pageTitle' => 'Events', 'companies' => $companies])->render();
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
            'event_date' => ['required', 'date', 
            //'after_or_equal:today'
            ],
            'description' => 'required|string',
            'highlights' => 'required|string',
            'company_id' => 'required|exists:companies,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'location' => 'required|string|max:500',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'event_images.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'event_date.required' => 'Event date is required.',
            'event_date.after_or_equal' => 'Event date cannot be in the past.',
            'description.required' => 'Description is required.',
            'highlights.required' => 'Highlights is required.',
            'company_id.required' => 'Company is required.',
            'start_time.required' => 'Start time is required.',
            'end_time.required' => 'End time is required.',
            'location.required' => 'Location is required.',
            'image.required' => 'Image is required.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
            'event_images.*.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'event_images.*.max' => 'Each image must not be greater than 5 MB.',
        ]);
        $data = $request->only(['title', 'event_date', 'description', 'highlights', 'company_id', 'start_time', 'end_time', 'location']);
        $data['status'] = 1;
        $imageResult = uploadFile($request->file('image'), 'events', 'image', null, 5 * 1024 * 1024);
        if (isset($imageResult['error'])) {
            return $this->errorResponse($imageResult['error']);
        }
        $data['image'] = 'storage/' . $imageResult['filePath'];
        $event = Event::create($data);
        if ($request->hasFile('event_images')) {
            $order = 0;
            foreach ($request->file('event_images') as $file) {
                if ($file->isValid() && in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png'])) {
                    $imgResult = uploadFile($file, 'events/gallery', 'image', null, 5 * 1024 * 1024);
                    if (!isset($imgResult['error'])) {
                        EventImage::create([
                            'event_id' => $event->id,
                            'image' => 'storage/' . $imgResult['filePath'],
                            'display_order' => $order++,
                        ]);
                    }
                }
            }
        }
        return $this->successResponse('Event has been added successfully.');
    }

    public function edit(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $row = Event::find($id);
        if (!$row) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        try {
            $row->load(['eventImages']);
            $companies = Company::orderBy('name')->get();
            $html = view('admin.events.edit', ['pageTitle' => 'Events', 'row' => $row, 'companies' => $companies])->render();
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
        $event = Event::find($id);
        if (!$event) {
            return $this->errorResponse('Record not found');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => ['required', 'date', 
            //'after_or_equal:today'
        ],
            'description' => 'required|string',
            'highlights' => 'required|string',
            'company_id' => 'required|exists:companies,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'location' => 'required|string|max:500',
            'image' => [Rule::requiredIf(!$event->image), 'nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'event_images.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'event_date.required' => 'Event date is required.',
            'event_date.after_or_equal' => 'Event date cannot be in the past.',
            'description.required' => 'Description is required.',
            'highlights.required' => 'Highlights is required.',
            'company_id.required' => 'Company is required.',
            'start_time.required' => 'Start time is required.',
            'end_time.required' => 'End time is required.',
            'location.required' => 'Location is required.',
            'image.required' => 'Image is required when no image is set.',
            'image.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'image.max' => 'Image must not be greater than 5 MB.',
            'event_images.*.mimes' => 'Only JPG, JPEG and PNG images are allowed.',
            'event_images.*.max' => 'Each image must not be greater than 5 MB.',
        ]);
        $data = $request->only(['title', 'event_date', 'description', 'highlights', 'company_id', 'start_time', 'end_time', 'location']);
        if ($request->hasFile('image')) {
            if ($event->image && Storage::disk('public')->exists(str_replace('storage/', '', $event->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $event->image));
            }
            $imageResult = uploadFile($request->file('image'), 'events', 'image', null, 5 * 1024 * 1024);
            if (isset($imageResult['error'])) {
                return $this->errorResponse($imageResult['error']);
            }
            $data['image'] = 'storage/' . $imageResult['filePath'];
        }
        $event->update($data);
        $removeIds = $request->input('remove_event_image', []);
        if (is_array($removeIds)) {
            $toRemove = EventImage::where('event_id', $event->id)->whereIn('id', $removeIds)->get();
            foreach ($toRemove as $ei) {
                if ($ei->image && Storage::disk('public')->exists(str_replace('storage/', '', $ei->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $ei->image));
                }
                $ei->delete();
            }
        }
        if ($request->hasFile('event_images')) {
            $maxOrder = (int) $event->eventImages()->max('display_order');
            $order = $maxOrder + 1;
            foreach ($request->file('event_images') as $file) {
                if ($file->isValid() && in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png'])) {
                    $imgResult = uploadFile($file, 'events/gallery', 'image', null, 5 * 1024 * 1024);
                    if (!isset($imgResult['error'])) {
                        EventImage::create([
                            'event_id' => $event->id,
                            'image' => 'storage/' . $imgResult['filePath'],
                            'display_order' => $order++,
                        ]);
                    }
                }
            }
        }
        return $this->successResponse('Event has been updated successfully.');
    }

    public function toggleStatus(Request $request, string $token): JsonResponse
    {
        $id = decryptIdFromUrl($token);
        if (!$request->ajax() || $id === null || !validatePermissions(self::PERMISSION_EDIT)) {
            return response()->json(['responseCode' => 0, 'msg' => 'Access denied']);
        }
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['responseCode' => 0, 'msg' => 'Record not found']);
        }
        $event->status = (int) $event->status === 1 ? 0 : 1;
        $event->save();
        return response()->json(['responseCode' => 1, 'status' => (int) $event->status]);
    }

    public function destroy(string $token): string
    {
        $id = decryptIdFromUrl($token);
        if ($id === null || !validatePermissions(self::PERMISSION_DELETE)) {
            abort($id === null ? 404 : 403);
        }
        $event = Event::find($id);
        if ($event) {
            foreach ($event->eventImages as $ei) {
                if ($ei->image && Storage::disk('public')->exists(str_replace('storage/', '', $ei->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $ei->image));
                }
            }
            if ($event->image && Storage::disk('public')->exists(str_replace('storage/', '', $event->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $event->image));
            }
            $event->delete();
        }
        return $this->successResponse('Event has been deleted successfully.');
    }

    /** Check if given time range overlaps any other participant for the event. Exclude event_participant $excludeEpId if set. */
    private function eventParticipantTimeOverlaps(int $eventId, string $startTime, string $endTime, ?int $excludeEpId): bool
    {
        $others = EventParticipant::where('event_id', $eventId)
            ->whereNotNull('start_time')
            ->whereNotNull('end_time');
        if ($excludeEpId !== null) {
            $others->where('id', '!=', $excludeEpId);
        }
        foreach ($others->get() as $other) {
            $s = substr($other->start_time, 0, 5);
            $e = substr($other->end_time, 0, 5);
            if ($startTime < $e && $endTime > $s) {
                return true;
            }
        }
        return false;
    }

    /** @return array<int, int> Ordered event_participant ids (index = participant row index) */
    private function syncEventParticipants(Event $event, Request $request): array
    {
        $ids = $request->input('ep_id', []);
        $participantIds = $request->input('ep_participant_id', []);
        $topics = $request->input('ep_topic', []);
        $descriptions = $request->input('ep_description', []);
        $startTimes = $request->input('ep_start_time', []);
        $endTimes = $request->input('ep_end_time', []);
        $orders = $request->input('ep_display_order', []);

        $existingIds = [];
        if (!is_array($participantIds)) {
            return $existingIds;
        }

        foreach ($participantIds as $i => $participantId) {
            $participantId = (int) $participantId;
            if ($participantId <= 0) {
                continue;
            }
            $epId = isset($ids[$i]) ? (int) $ids[$i] : 0;
            $topic = isset($topics[$i]) ? $topics[$i] : null;
            $description = isset($descriptions[$i]) ? $descriptions[$i] : null;
            $startTime = !empty($startTimes[$i]) ? $startTimes[$i] : null;
            $endTime = !empty($endTimes[$i]) ? $endTimes[$i] : null;
            $order = isset($orders[$i]) ? (int) $orders[$i] : $i;

            if ($epId > 0) {
                $ep = EventParticipant::where('event_id', $event->id)->where('id', $epId)->first();
                if ($ep) {
                    $ep->update([
                        'participant_id' => $participantId,
                        'topic' => $topic,
                        'description' => $description,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'display_order' => $order,
                    ]);
                    $existingIds[] = $ep->id;
                    continue;
                }
            }
            $ep = EventParticipant::create([
                'event_id' => $event->id,
                'participant_id' => $participantId,
                'topic' => $topic,
                'description' => $description,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'display_order' => $order,
            ]);
            $existingIds[] = $ep->id;
        }

        EventParticipant::where('event_id', $event->id)->whereNotIn('id', $existingIds)->delete();
        return $existingIds;
    }

    private function syncEventParticipantDocuments(Event $event, Request $request, array $orderedEpIds): void
    {
        $docTitlesByRow = $request->input('ep_doc_titles', []);
        $docIdsByRow = $request->input('ep_doc_ids', []);
        $removeIds = (array) $request->input('ep_doc_remove', []);
        if (!is_array($docTitlesByRow)) {
            return;
        }
        $keptDocIds = [];
        foreach ($docTitlesByRow as $rowIndex => $titles) {
            $epId = $orderedEpIds[$rowIndex] ?? null;
            if (!$epId || !is_array($titles)) {
                continue;
            }
            $ids = $docIdsByRow[$rowIndex] ?? [];
            if (!is_array($ids)) {
                $ids = [];
            }
            foreach ($titles as $docIndex => $title) {
                $title = trim($title ?? '');
                $docId = isset($ids[$docIndex]) ? (int) $ids[$docIndex] : 0;
                $file = $request->file('ep_doc_files.' . $rowIndex . '.' . $docIndex);
                if ($docId > 0) {
                    $doc = EventParticipantDocument::where('event_participant_id', $epId)->where('id', $docId)->first();
                    if ($doc) {
                        if (in_array((string) $docId, $removeIds, true)) {
                            if ($doc->file_path && Storage::disk('public')->exists(str_replace('storage/', '', $doc->file_path))) {
                                Storage::disk('public')->delete(str_replace('storage/', '', $doc->file_path));
                            }
                            $doc->delete();
                            continue;
                        }
                        $doc->title = $title ?: $doc->title;
                        if ($file && $file->isValid() && strtolower($file->getClientOriginalExtension()) === 'pdf') {
                            if ($doc->file_path && Storage::disk('public')->exists(str_replace('storage/', '', $doc->file_path))) {
                                Storage::disk('public')->delete(str_replace('storage/', '', $doc->file_path));
                            }
                            $uploadResult = uploadFile($file, 'event_participant_docs', 'pdf', null, 10 * 1024 * 1024);
                            if (!isset($uploadResult['error'])) {
                                $doc->file_path = 'storage/' . $uploadResult['filePath'];
                                $doc->file_size = $file->getSize();
                            }
                        }
                        $doc->save();
                        $keptDocIds[] = $doc->id;
                    }
                    continue;
                }
                if (($title || $file) && $file && $file->isValid() && strtolower($file->getClientOriginalExtension()) === 'pdf') {
                    $uploadResult = uploadFile($file, 'event_participant_docs', 'pdf', null, 10 * 1024 * 1024);
                    if (!isset($uploadResult['error'])) {
                        EventParticipantDocument::create([
                            'event_participant_id' => $epId,
                            'title' => $title ?: 'Presentation',
                            'file_path' => 'storage/' . $uploadResult['filePath'],
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            }
        }
    }
}
