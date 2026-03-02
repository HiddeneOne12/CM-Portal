<form method="post" action="{{ url('/admin/acl/module-categories/edit/' . $row->getKey()) }}">
    @csrf
    <input type="hidden" name="eid" value="{{ $row->getKey() }}">
    <input type="hidden" name="module_category_token" value="{{ encryptIdForUrl($row->getKey()) }}">
    <div class="fv-row mb-8">
        <label class="required fs-6 fw-semibold mb-2">Category Name</label>
        <input type="text" name="category_name" class="form-control form-control-solid" value="{{ $row->category_name }}" id="category_name" required />
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
