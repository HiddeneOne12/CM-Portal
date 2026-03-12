@extends('layouts.admin')
@push('title')
Add Role - {{ config('global.SITE_NAME') }}
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
                            
                            <div>
                                <h3 class="card-label fw-bold text-gray-900 mb-0">Add Role</h3>
                                <span class="text-muted fs-7">Define a role and assign module permissions</span>
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

                        <form method="POST" action="{{ route('admin.acl.roles.add') }}">
                            @csrf

                            <div class="row mb-6">
                                <div class="col-12 col-md-6">
                                    <label class="form-label required fw-semibold text-gray-700">Role Name</label>
                                    <input type="text" name="role_name"
                                           class="form-control form-control-solid @error('role_name') is-invalid @enderror"
                                           value="{{ old('role_name') }}"
                                           placeholder="e.g. Editor, Manager"
                                           required>
                                    @error('role_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-8">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <label class="form-label fw-semibold text-gray-700 mb-0">
                                        Permissions
                                        <span class="text-muted fw-normal fs-7 ms-2">(select modules this role can access)</span>
                                    </label>
                                    <div class="d-flex gap-3">
                                        <button type="button" class="btn btn-sm btn-light-primary" id="selectAll">Select All</button>
                                        <button type="button" class="btn btn-sm btn-light-danger" id="deselectAll">Deselect All</button>
                                    </div>
                                </div>

                                @php
                                    $grouped = $modules->groupBy(fn($m) => optional($m->category)->category_name ?? 'Uncategorized');
                                @endphp

                                @if($grouped->isEmpty())
                                    <div class="text-muted fst-italic fs-7 border rounded p-4 bg-light">
                                        No modules available.
                                    </div>
                                @else
                                <div class="row g-4">
                                    @foreach($grouped as $categoryName => $categoryModules)
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="border rounded p-4 h-100 bg-light-subtle">
                                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                                <span class="fw-bold text-gray-800 fs-7 text-uppercase ls-1">{{ $categoryName }}</span>
                                                <div class="form-check form-check-custom form-check-sm form-check-solid">
                                                    <input class="form-check-input category-toggle" type="checkbox"
                                                           data-category="{{ \Illuminate\Support\Str::slug($categoryName) }}"
                                                           id="cat_{{ \Illuminate\Support\Str::slug($categoryName) }}"
                                                           title="Toggle all in {{ $categoryName }}">
                                                    <label class="form-check-label text-muted fs-8" for="cat_{{ \Illuminate\Support\Str::slug($categoryName) }}">All</label>
                                                </div>
                                            </div>
                                            @foreach($categoryModules as $module)
                                            <div class="form-check form-check-custom form-check-solid mb-2">
                                                <input class="form-check-input module-check cat-{{ \Illuminate\Support\Str::slug($categoryName) }}"
                                                       type="checkbox"
                                                       name="modules[]"
                                                       value="{{ $module->getKey() }}"
                                                       id="mod_{{ $module->getKey() }}"
                                                       {{ in_array($module->getKey(), old('modules', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label text-gray-700 fw-semibold fs-7" for="mod_{{ $module->getKey() }}">
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
                                <button type="submit" class="btn btn-primary px-8">
                                    <i class="ki-duotone ki-check fs-4 me-1"></i> Save Role
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
@stop

@push('scripts')
<script>
document.querySelectorAll('.category-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        const cat = this.dataset.category;
        document.querySelectorAll('.cat-' + cat).forEach(function(cb) { cb.checked = toggle.checked; });
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
var selectAll = document.getElementById('selectAll');
var deselectAll = document.getElementById('deselectAll');
if (selectAll) selectAll.addEventListener('click', function() {
    document.querySelectorAll('.module-check, .category-toggle').forEach(function(cb) { cb.checked = true; });
});
if (deselectAll) deselectAll.addEventListener('click', function() {
    document.querySelectorAll('.module-check, .category-toggle').forEach(function(cb) { cb.checked = false; });
});
</script>
@endpush
