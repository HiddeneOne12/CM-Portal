<form method="post" action="{{ url('/cmcontrol/acl/interviews/edit/' . $row->id) }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="eid" value="{{ $row->id }}">
    <input type="hidden" name="interview_token" value="{{ encryptIdForUrl($row->id) }}">
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Image</label>
        @if($row->image)
        <div class="mb-2"><img src="{{ asset($row->image) }}" alt="" class="rounded" style="max-height: 60px;"></div>
        <span class="text-muted fs-7">Leave empty to keep current image.</span>
        @else
        <span class="text-muted fs-7">Image is required.</span>
        @endif
        <input type="file" name="image" class="form-control form-control-solid" accept=".jpg,.jpeg,.png" @if(!$row->image) required @endif />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Title</label>
        <input type="text" name="title" class="form-control form-control-solid" placeholder="Title" value="{{ old('title', $row->title) }}" required />
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Interview description</label>
        <textarea name="description" class="form-control form-control-solid" rows="4" placeholder="Description of the interview">{{ old('description', $row->description) }}</textarea>
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Interview of (participant)</label>
        <select name="participant_id" class="form-select form-select-solid">
            <option value="">— Select participant —</option>
            @foreach($participants ?? [] as $p)
            <option value="{{ $p->id }}" @if((int) old('participant_id', $row->participant_id) === (int) $p->id) selected @endif>{{ $p->name }}@if($p->position) — {{ $p->position }}@endif</option>
            @endforeach
        </select>
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Video</label>
        @if($row->video)
        <div class="mb-2">
            <span class="text-muted fs-7 d-block mb-1">Current video uploaded. Leave empty to keep it.</span>
            <div class="rounded border bg-light p-2" style="max-width: 320px;">
                <video src="{{ asset($row->video) }}" controls preload="metadata" class="w-100 rounded" style="max-height: 180px;"></video>
                <div class="mt-1 text-muted fs-8">{{ basename($row->video) }}</div>
            </div>
        </div>
        @else
        <span class="text-muted fs-7">One of Video or Interview image is required.</span>
        @endif
        <input type="file" name="video" class="form-control form-control-solid" accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov" />
        <span class="text-muted fs-7">MP4, WebM or MOV. Max 100 MB.</span>
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Interview image</label>
        @if($row->interview_image)
        <div class="mb-2"><img src="{{ asset($row->interview_image) }}" alt="" class="rounded" style="max-height: 80px;"></div>
        <span class="text-muted fs-7">Leave empty to keep current. Alternative to video.</span>
        @else
        <span class="text-muted fs-7">Alternative to video. One of Video or Interview image is required.</span>
        @endif
        <input type="file" name="interview_image" class="form-control form-control-solid" accept=".jpg,.jpeg,.png" />
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
