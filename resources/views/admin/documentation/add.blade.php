<form method="post" action="{{ url('/admin/acl/documentation/add') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="eid" value="">
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Image</label>
        <input type="file" name="image" class="form-control form-control-solid" accept=".jpg,.jpeg,.png" required />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Title</label>
        <input type="text" name="title" class="form-control form-control-solid" placeholder="Documentation title" required />
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Description</label>
        <textarea name="description" class="form-control form-control-solid" rows="4" placeholder="Brief description for the documentation card"></textarea>
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Published in date</label>
        <input type="date" name="published_in_date" class="form-control form-control-solid" required />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Report PDF</label>
        <input type="file" name="report_pdf" class="form-control form-control-solid" accept=".pdf" required />
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
