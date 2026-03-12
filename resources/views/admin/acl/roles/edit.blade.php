@extends('layouts.admin')
@push('title')
Edit Role - {{ config('global.SITE_NAME') }}
@endpush
@section('header')
@include('includes.admin_header_nav')
@stop
@section('toolbar')
@include('includes.toolbar')
@stop
@section('content')
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-fluid overflow-hidden">
    <div class="content flex-row-fluid w-100 min-w-0" id="kt_content">
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-12">
                <div class="card card-flush shadow-sm">
                    <div class="card-header pt-6 pb-4 border-bottom">
                        <div class="card-title d-flex align-items-center gap-3">
                            <span class="bg-warning bg-opacity-10 rounded p-2">
                                <i class="ki-duotone ki-pencil fs-2 text-warning"><span class="path1"></span><span class="path2"></span></i>
                            </span>
                            <div>
                                <h3 class="card-label fw-bold text-gray-900 mb-0">Edit Role</h3>
                                <span class="text-muted fs-7">Editing: <strong>{{ $role->role_name }}</strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-6">

                        @if ($errors->any())
                            <div class="alert alert-danger d-flex align-items-start mb-6">
                                <i class="ki-duotone ki-information-5 fs-2 me-3 mt-1 text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                <ul class="mb-0 ps-0 list-unstyled">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.acl.roles.edit', encryptIdForUrl($role->getKey())) }}">
                            @csrf

                            <div class="row mb-6">
                                <div class="col-12 col-md-6">
                                    <label class="form-label required fw-semibold text-gray-700">Role Name</label>
                                    <input type="text" name="role_name"
                                           class="form-control form-control-solid @error('role_name') is-invalid @enderror"
                                           value="{{ old('role_name', $role->role_name) }}"
                                           required>
                                    @error('role_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-semibold text-gray-700">Display Order</label>
                                    <input type="number" name="display_order"
                                           class="form-control form-control-solid"
                                           value="{{ old('display_order', $role->display_order) }}"
                                           min="0">
                                </div>
                            </div>

                            {{-- Permissions grouped by category --}}
                            <div class="mb-8">
                                @php
                                    $selectedModules = array_map('intval', old('modules', $permissions ?? []));
                                    $grouped = $modules->groupBy(fn($m) => optional($m->category)->category_name ?? 'Uncategorized');
                                    $isSuperAdmin = $role->getKey() == 1;
                                @endphp

                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <label class="form-label fw-semibold text-gray-700 mb-0">
                                        Permissions
                                        <span class="text-muted fw-normal fs-7 ms-2">(select modules this role can access)</span>
                                    </label>
                                    @if(!$isSuperAdmin)
                                    <div class="d-flex gap-3">
                                        <button type="button" class="btn btn-sm btn-light-primary" id="selectAll">Select All</button>
                                        <button type="button" class="btn btn-sm btn-light-danger" id="deselectAll">Deselect All</button>
                                    </div>
                                    @endif
                                </div>

                                @if($isSuperAdmin)
                                    <div class="alert alert-primary d-flex align-items-center mb-4">
                                        <i class="ki-duotone ki-shield-tick fs-2 me-3 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                        <div>Super Admin has access to <strong>all modules</strong> by default. Permissions cannot be modified.</div>
                                    </div>
                                @endif

                                @if($grouped->isEmpty())
                                    <div class="text-muted fst-italic fs-7 border rounded p-4 bg-light">No modules available.</div>
                                @else
                                <div class="row g-4">
                                    @foreach($grouped as $categoryName => $categoryModules)
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="border rounded p-4 h-100 bg-light-subtle">
                                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                                <span class="fw-bold text-gray-800 fs-7 text-uppercase ls-1">{{ $categoryName }}</span>
                                                @if(!$isSuperAdmin)
                                                <div class="form-check form-check-custom form-check-sm form-check-solid">
                                                    @php
                                                        $catIds = $categoryModules->pluck('ID')->map(fn($v) => (int)$v)->toArray();
                                                        $allChecked = count(array_intersect($catIds, $selectedModules)) === count($catIds);
                                                    @endphp
                                                    <input class="form-check-input category-toggle"
                                                           type="checkbox"
                                                           data-category="{{ Str::slug($categoryName) }}"
                                                           id="cat_{{ Str::slug($categoryName) }}"
                                                           {{ $allChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label text-muted fs-8" for="cat_{{ Str::slug($categoryName) }}">All</label>
                                                </div>
                                                @endif
                                            </div>
                                            @foreach($categoryModules as $module)
                                            <div class="form-check form-check-custom form-check-solid mb-2">
                                                <input class="form-check-input module-check cat-{{ Str::slug($categoryName) }}"
                                                       type="checkbox"
                                                       name="modules[]"
                                                       value="{{ $module->ID }}"
                                                       id="mod_{{ $module->ID }}"
                                                       {{ in_array((int)$module->ID, $selectedModules) ? 'checked' : '' }}
                                                       {{ $isSuperAdmin ? 'disabled checked' : '' }}>
                                                <label class="form-check-label text-gray-700 fw-semibold fs-7" for="mod_{{ $module->ID }}">
                                                    {{ $module->module_name }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary px-8" {{ $isSuperAdmin ? '' : '' }}>
                                    <i class="ki-duotone ki-check fs-4 me-1"></i> Update Role
                                </button>
                                <a href="{{ route('admin.acl.roles.listing') }}" class="btn btn-light">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.category-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const cat = this.dataset.category;
            document.querySelectorAll('.cat-' + cat).forEach(cb => cb.checked = toggle.checked);
        });
    });

    document.querySelectorAll('.module-check').forEach(function(cb) {
        cb.addEventListener('change', function() {
            this.classList.forEach(function(cls) {
                if (cls.startsWith('cat-')) {
                    const cat = cls.replace('cat-', '');
                    const all = document.querySelectorAll('.cat-' + cat);
                    const checked = document.querySelectorAll('.cat-' + cat + ':checked');
                    const toggle = document.querySelector('[data-category="' + cat + '"]');
                    if (toggle) toggle.checked = all.length === checked.length;
                }
            });
        });
    });

    const selectAll = document.getElementById('selectAll');
    const deselectAll = document.getElementById('deselectAll');
    if (selectAll) selectAll.addEventListener('click', () => {
        document.querySelectorAll('.module-check, .category-toggle').forEach(cb => cb.checked = true);
    });
    if (deselectAll) deselectAll.addEventListener('click', () => {
        document.querySelectorAll('.module-check, .category-toggle').forEach(cb => cb.checked = false);
    });
</script>
@endpush
@stop