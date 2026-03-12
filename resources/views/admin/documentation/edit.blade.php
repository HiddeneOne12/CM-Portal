<form method="post" action="{{ url('/cmcontrol/acl/documentation/edit/' . $row->id) }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="eid" value="{{ $row->id }}">
    <input type="hidden" name="documentation_token" value="{{ encryptIdForUrl($row->id) }}">
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
        <input type="text" name="title" class="form-control form-control-solid" placeholder="Documentation title" value="{{ old('title', $row->title) }}" required />
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Description</label>
        <textarea name="description" class="form-control form-control-solid" rows="4" placeholder="Brief description for the documentation card">{{ old('description', $row->description) }}</textarea>
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Published in date</label>
        <input type="date" name="published_in_date" class="form-control form-control-solid" value="{{ old('published_in_date', $row->published_in_date ? $row->published_in_date->format('Y-m-d') : '') }}" required />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Report PDF</label>
        @if($row->report_pdf)
        <div class="mb-2">
            <a href="{{ asset($row->report_pdf) }}" target="_blank" rel="noopener noreferrer" class="text-primary">Current PDF</a>
        </div>
        <span class="text-muted fs-7">Leave empty to keep current file.</span>
        @else
        <span class="text-muted fs-7">Report PDF is required.</span>
        @endif
        <input type="file" name="report_pdf" class="form-control form-control-solid" accept=".pdf" @if(!$row->report_pdf) required @endif />
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
