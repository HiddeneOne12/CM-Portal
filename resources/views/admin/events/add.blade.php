<form method="post" action="{{ url('/cmcontrol/acl/events/add') }}" enctype="multipart/form-data">
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
        <label class="required fs-6 fw-semibold mb-2">Event Date &amp; Time</label>
        <div class="row g-3">
            <div class="col-md-4">
                <input type="date" name="event_date" class="form-control form-control-solid" placeholder="Event Date" required />
            </div>
            <div class="col-md-4">
                <input type="time" name="start_time" class="form-control form-control-solid" placeholder="Start" required />
            </div>
            <div class="col-md-4">
                <input type="time" name="end_time" class="form-control form-control-solid" placeholder="End" required />
            </div>
        </div>
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Location &amp; Company</label>
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="location" class="form-control form-control-solid" placeholder="Venue or address" required />
            </div>
            <div class="col-md-6">
                <select name="company_id" class="form-select form-select-solid" required>
                    <option value="">— Select company —</option>
                    @foreach($companies ?? [] as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Description</label>
        <textarea name="description" class="form-control form-control-solid" rows="3" placeholder="Description" required></textarea>
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Highlights</label>
        <textarea name="highlights" class="form-control form-control-solid" rows="4" placeholder="Highlights from the event" required></textarea>
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Event images (gallery)</label>
        <input type="file" name="event_images[]" class="form-control form-control-solid" accept=".jpg,.jpeg,.png" multiple />
        <span class="text-muted fs-7">You can select multiple images.</span>
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
