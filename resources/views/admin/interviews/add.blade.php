<form method="post" action="{{ url('/admin/acl/interviews/add') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="eid" value="">
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Image</label>
        <input type="file" name="image" class="form-control form-control-solid" accept=".jpg,.jpeg,.png" required />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Title</label>
        <input type="text" name="title" class="form-control form-control-solid" placeholder="Title" required />
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Interview description</label>
        <textarea name="description" class="form-control form-control-solid" rows="4" placeholder="Description of the interview"></textarea>
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Interview of (participant)</label>
        <select name="participant_id" class="form-select form-select-solid">
            <option value="">— Select participant —</option>
            @foreach($participants ?? [] as $p)
            <option value="{{ $p->id }}">{{ $p->name }}@if($p->position) — {{ $p->position }}@endif</option>
            @endforeach
        </select>
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Video</label>
        <input type="file" name="video" class="form-control form-control-solid" accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov" />
        <span class="text-muted fs-7">MP4, WebM or MOV. Max 100 MB. One of Video or Interview image below is required.</span>
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Interview image</label>
        <input type="file" name="interview_image" class="form-control form-control-solid" accept=".jpg,.jpeg,.png" />
        <span class="text-muted fs-7">Alternative to video. One of Video or Interview image is required.</span>
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
