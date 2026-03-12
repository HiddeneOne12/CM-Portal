@extends('layouts.admin')
@push('title')
Add Admin User - {{ config('global.SITE_NAME') }}
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
                            <span class="bg-primary bg-opacity-10 rounded p-2">
                                <i class="ki-duotone ki-profile-add fs-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </span>
                            <div>
                                <h3 class="card-label fw-bold text-gray-900 mb-0">Add Admin User</h3>
                                <span class="text-muted fs-7">Create a new administrator account</span>
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

                        <form method="POST" action="{{ route('admin.acl.admin-users.store') }}">
                            @csrf

                            {{-- Username --}}
                            <div class="mb-6">
                                <label class="form-label required fw-semibold text-gray-700">Username</label>
                                <input type="text" name="user_name"
                                       class="form-control form-control-solid @error('user_name') is-invalid @enderror"
                                       value="{{ old('user_name') }}"
                                       placeholder="e.g. john.doe"
                                       required autocomplete="off">
                                @error('user_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="row mb-6">
                                <div class="col-6">
                                    <label class="form-label required fw-semibold text-gray-700">Password</label>
                                    <input type="password" name="password"
                                           class="form-control form-control-solid @error('password') is-invalid @enderror"
                                           placeholder="Min. 8 characters"
                                           required autocomplete="new-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label required fw-semibold text-gray-700">Confirm Password</label>
                                    <input type="password" name="password_confirmation"
                                           class="form-control form-control-solid"
                                           placeholder="Repeat password"
                                           required autocomplete="new-password">
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="mb-6">
                                <label class="form-label fw-semibold text-gray-700 d-block">Status</label>
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                                    <label class="form-check-label fw-semibold text-gray-600" for="is_active">Active</label>
                                </div>
                            </div>

                            {{-- Roles --}}
                            <div class="mb-8">
                                <label class="form-label fw-semibold text-gray-700 d-block mb-2">
                                    Assign Role
                                    <span class="text-muted fw-normal fs-7 ms-2">(optional)</span>
                                </label>
                                @if($roles->isEmpty())
                                    <div class="text-muted fst-italic fs-7 border rounded p-4 bg-light">
                                        No roles available. <a href="{{ route('admin.acl.roles.add') }}">Create a role first.</a>
                                    </div>
                                @else
                                <div class="d-flex flex-wrap gap-3">
                                    <label class="role-option d-flex align-items-center gap-2 border rounded px-4 py-3">
                                        <input type="radio" name="roles[]" value="" class="d-none role-radio"
                                               {{ empty(old('roles', [])) ? 'checked' : '' }}>
                                        <span class="role-indicator border border-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                              style="width:18px;height:18px;">
                                            <span class="role-dot rounded-circle" style="width:8px;height:8px;display:none;"></span>
                                        </span>
                                        <span class="fw-semibold text-gray-700 fs-7">None</span>
                                    </label>
                                    @foreach($roles as $role)
                                    @php $isSelected = in_array($role->ID, array_map('intval', old('roles', []))); @endphp
                                    <label class="role-option d-flex align-items-center gap-2 border rounded px-4 py-3 {{ $isSelected ? 'active' : '' }}">
                                        <input type="radio" name="roles[]" value="{{ $role->ID }}" class="d-none role-radio" {{ $isSelected ? 'checked' : '' }}>
                                        <span class="role-indicator border border-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                              style="width:18px;height:18px;">
                                            <span class="role-dot rounded-circle" style="width:8px;height:8px;{{ $isSelected ? '' : 'display:none;' }}"></span>
                                        </span>
                                        <span class="fw-semibold text-gray-700 fs-7">{{ $role->role_name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary px-8">
                                    <i class="ki-duotone ki-check fs-4 me-1"></i> Save User
                                </button>
                                <a href="{{ route('admin.acl.admin-users.listing') }}" class="btn btn-light">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('styles')
<style>
.role-option {
    cursor: pointer;
    transition: all 0.15s ease;
    border-color: #e4e6ef !important;
    background: #fff;
    user-select: none;
    min-width: 140px;
}
.role-option:hover { border-color: #6d28d9 !important; background: #f5f3ff; }
.role-option.active { border-color: #6d28d9 !important; background: #f5f3ff; }
.role-option.active .role-indicator { border-color: #6d28d9 !important; background: #6d28d9; }
.role-option.active .role-dot { display: block !important; background: #fff; }
.role-option .role-indicator { border-color: #b5b5c3; }
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.role-option').forEach(function(label) {
    label.addEventListener('click', function() {
        document.querySelectorAll('.role-option').forEach(function(l) {
            l.classList.remove('active');
            l.querySelector('.role-indicator').style.background = '';
            l.querySelector('.role-indicator').style.borderColor = '';
            l.querySelector('.role-dot').style.display = 'none';
        });
        this.classList.add('active');
        this.querySelector('.role-indicator').style.background = '#6d28d9';
        this.querySelector('.role-indicator').style.borderColor = '#6d28d9';
        this.querySelector('.role-dot').style.display = 'block';
        this.querySelector('.role-radio').checked = true;
    });
});
document.querySelectorAll('.role-radio:checked').forEach(function(radio) {
    const label = radio.closest('.role-option');
    if (label) {
        label.classList.add('active');
        label.querySelector('.role-indicator').style.background = '#6d28d9';
        label.querySelector('.role-indicator').style.borderColor = '#6d28d9';
        label.querySelector('.role-dot').style.display = 'block';
    }
});
</script>
@endpush