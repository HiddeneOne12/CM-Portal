<form method="post" action="{{ url('/admin/acl/companies/edit/' . $row->id) }}">
    @csrf
    <input type="hidden" name="eid" value="{{ $row->id }}">
    <input type="hidden" name="company_token" value="{{ encryptIdForUrl($row->id) }}">
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Name</label>
        <input type="text" name="name" class="form-control form-control-solid" placeholder="Company name" value="{{ old('name', $row->name) }}" required />
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Type</label>
        <input type="text" name="type" class="form-control form-control-solid" placeholder="Type" value="{{ old('type', $row->type) }}" />
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
