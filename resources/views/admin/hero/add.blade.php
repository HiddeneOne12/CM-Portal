<form method="post" action="{{ url('/cmcontrol/acl/hero/add') }}" enctype="multipart/form-data">
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
        <label class="required fs-6 fw-semibold mb-2">Description</label>
        <textarea name="description" class="form-control form-control-solid" rows="3" placeholder="Description" required></textarea>
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Video</label>
        <input type="file" name="video" class="form-control form-control-solid" accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov" required />
        <span class="text-muted fs-7">MP4, WebM or MOV. Max 100 MB.</span>
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
