<form method="post" action="{{ url('/admin/acl/events/edit/' . $row->id) }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="eid" value="{{ $row->id }}">
    <input type="hidden" name="event_token" value="{{ encryptIdForUrl($row->id) }}">
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
        <label class="required fs-6 fw-semibold mb-2">Event Date &amp; Time</label>
        <div class="row g-3">
            <div class="col-md-4">
                <input type="date" name="event_date" class="form-control form-control-solid" value="{{ old('event_date', $row->event_date ? $row->event_date->format('Y-m-d') : '') }}" required />
            </div>
            <div class="col-md-4">
                <input type="time" name="start_time" class="form-control form-control-solid" value="{{ old('start_time', $row->start_time ? substr($row->start_time, 0, 5) : '') }}" required />
            </div>
            <div class="col-md-4">
                <input type="time" name="end_time" class="form-control form-control-solid" value="{{ old('end_time', $row->end_time ? substr($row->end_time, 0, 5) : '') }}" required />
            </div>
        </div>
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Location &amp; Company</label>
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="location" class="form-control form-control-solid" placeholder="Venue or address" value="{{ old('location', $row->location) }}" required />
            </div>
            <div class="col-md-6">
                <select name="company_id" class="form-select form-select-solid" required>
                    <option value="">— Select company —</option>
                    @foreach($companies ?? [] as $c)
                    <option value="{{ $c->id }}" {{ old('company_id', $row->company_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Description</label>
        <textarea name="description" class="form-control form-control-solid" rows="3" placeholder="Description" required>{{ old('description', $row->description) }}</textarea>
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Highlights</label>
        <textarea name="highlights" class="form-control form-control-solid" rows="4" placeholder="Highlights from the event" required>{{ old('highlights', $row->highlights) }}</textarea>
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Event images (gallery)</label>
        @if($row->eventImages && $row->eventImages->count() > 0)
        <div class="d-flex flex-wrap gap-3 mb-3">
            @foreach($row->eventImages as $img)
            <div class="d-flex flex-column align-items-center">
                <img src="{{ asset($img->image) }}" alt="" class="rounded" style="max-height: 80px; width: auto;">
                <label class="form-check form-check-custom form-check-solid mt-1">
                    <input type="checkbox" name="remove_event_image[]" value="{{ $img->id }}" class="form-check-input" />
                    <span class="form-check-label fs-7 text-danger">Remove</span>
                </label>
            </div>
            @endforeach
        </div>
        @endif
        <input type="file" name="event_images[]" class="form-control form-control-solid" accept=".jpg,.jpeg,.png" multiple />
        <span class="text-muted fs-7">Add more images; select multiple if needed.</span>
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
