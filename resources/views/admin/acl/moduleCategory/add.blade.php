<form method="post" action="{{ url('/admin/acl/module-categories/add') }}">
    @csrf
    <input type="hidden" name="eid" value="">
    <div class="fv-row mb-8">
        <label class="required fs-6 fw-semibold mb-2">Category Name</label>
        <input type="text" name="category_name" class="form-control form-control-solid" placeholder="Category Name" id="category_name" required />
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
