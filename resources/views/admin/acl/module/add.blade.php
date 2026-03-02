<form method="post" action="{{ url('/admin/acl/module/add') }}">
    @csrf
    <input type="hidden" name="eid" value="">
    <input type="hidden" name="show_in_menu" id="show_in_menu" value="0">
    <div class="fv-row mb-8">
        <label class="required fs-6 fw-semibold mb-2">Module Category</label>
        <select name="module_category_id" class="form-select form-select-solid" required>
            <option value="">Choose Category</option>
            @if (isset($catResult))
            @foreach ($catResult as $rowCat)
            <option value="{{ $rowCat->getKey() }}">{{ $rowCat->category_name }}</option>
            @endforeach
            @endif
        </select>
    </div>
    <div class="fv-row mb-8">
        <label class="required fs-6 fw-semibold mb-2">Module Name</label>
        <input type="text" name="module_name" class="form-control form-control-solid" placeholder="Module Name" required />
    </div>
    <div class="fv-row mb-8">
        <label class="required fs-6 fw-semibold mb-2">Route (Slug)</label>
        <input type="text" name="route" class="form-control form-control-solid" placeholder="e.g. admin/acl/module" required />
    </div>
    <div class="fv-row mb-8">
        <label class="fs-6 fw-semibold mb-2">CSS Class</label>
        <input type="text" name="css_class" class="form-control form-control-solid" placeholder="Optional" />
    </div>
    <div class="fv-row mb-8">
        <label class="fs-6 fw-semibold mb-2">Show in menu</label>
        <input type="checkbox" name="show_in_menu_checkbox" class="form-check-input" onchange="document.getElementById('show_in_menu').value = this.checked ? '1' : '0'" />
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
