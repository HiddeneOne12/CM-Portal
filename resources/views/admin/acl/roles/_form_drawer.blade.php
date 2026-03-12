@php
    $isEdit = isset($role) && $role;
    $permissions = $isEdit ? (\App\Models\Acl\RolePrivilegeModel::where('role_ID', $role->getKey())->pluck('module_ID')->map(fn($v) => (int)$v)->toArray()) : [];
    $selectedModules = array_map('intval', old('modules', $permissions));
    $grouped = ($modules ?? collect())->groupBy(fn($m) => optional($m->category)->category_name ?? 'Uncategorized');
    $isSuperAdmin = $isEdit && $role->getKey() == 1;
@endphp
<form method="POST" action="" class="role-drawer-form">
    @csrf
    @if($isEdit)
    <input type="hidden" name="role_token" value="{{ encryptIdForUrl($role->getKey()) }}">
    @endif
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Role Name</label>
        <input type="text" name="role_name" class="form-control form-control-solid" placeholder="e.g. Manager"
               value="{{ old('role_name', $role->role_name ?? '') }}" required />
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2 d-block">Permissions</label>
        @if($isSuperAdmin)
            <p class="text-muted fs-7">Super Admin has access to all modules.</p>
        @elseif($grouped->isEmpty())
            <p class="text-muted fs-7">No modules available.</p>
        @else
            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-sm btn-light-primary btn-select-all-roles">Select All</button>
                <button type="button" class="btn btn-sm btn-light-danger btn-deselect-all-roles">Deselect All</button>
            </div>
            <div class="border rounded p-4 bg-light-subtle" style="max-height: 320px; overflow-y: auto;">
                <div class="row g-4">
                    @foreach($grouped as $categoryName => $categoryModules)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="mb-2 pb-1 d-flex align-items-center justify-content-between border-bottom">
                            <span class="fw-bold text-gray-800 fs-7 text-uppercase">{{ $categoryName }}</span>
                            <div class="form-check form-check-sm">
                                <input class="form-check-input category-toggle-roles" type="checkbox" data-cat="{{ \Illuminate\Support\Str::slug($categoryName) }}">
                                <label class="form-check-label text-muted fs-8">All</label>
                            </div>
                        </div>
                        @foreach($categoryModules as $module)
                        <div class="form-check form-check-custom form-check-solid mb-1">
                            <input class="form-check-input module-check-roles cat-{{ \Illuminate\Support\Str::slug($categoryName) }}" type="checkbox"
                                   name="modules[]" value="{{ $module->getKey() }}" id="mod_{{ $module->getKey() }}"
                                   {{ in_array($module->getKey(), $selectedModules) ? 'checked' : '' }}>
                            <label class="form-check-label text-gray-700 fs-7" for="mod_{{ $module->getKey() }}">{{ $module->module_name }}</label>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Save' }}</button>
    </div>
</form>
<script>
(function(){
    document.querySelectorAll('.category-toggle-roles').forEach(function(t) {
        t.addEventListener('change', function() {
            var cat = this.getAttribute('data-cat');
            document.querySelectorAll('.cat-' + cat).forEach(function(cb) { cb.checked = t.checked; });
        });
    });
    document.querySelectorAll('.btn-select-all-roles').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.module-check-roles, .category-toggle-roles').forEach(function(c) { c.checked = true; });
        });
    });
    document.querySelectorAll('.btn-deselect-all-roles').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.module-check-roles, .category-toggle-roles').forEach(function(c) { c.checked = false; });
        });
    });
})();
</script>
