@extends('layouts.admin')
@push('title')
Roles - {{ config('global.SITE_NAME') }}
@endpush
@section('header')
@include('includes.admin_header_nav')
@stop
@section('toolbar')
@include('includes.toolbar')
@stop
@section('content')
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-fluid">
    <div class="content flex-row-fluid" id="kt_content">
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center mb-4"><i class="ki-duotone ki-shield-tick fs-2 me-3 text-success"></i><div>{{ session('success') }}</div></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center mb-4"><i class="ki-duotone ki-shield-cross fs-2 me-3 text-danger"></i><div>{{ session('error') }}</div></div>
        @endif
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <div class="d-flex align-items-center position-relative my-1 me-3">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" name="search" value="{{ request('search') }}" id="roles-search-input" class="form-control form-control-solid w-250px ps-13" placeholder="Search Roles" autocomplete="off" />
                </div>
                <div class="card-toolbar">
                    @if(validatePermissions('admin/acl/roles/add'))
                    <a href="{{ route('admin.acl.roles.add') }}" class="btn btn-sm btn-primary"><i class="ki-duotone ki-plus fs-2"></i>Add Role</a>
                    @endif
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table id="roles-table" class="table table-hover table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-200px">Name</th>
                                <th class="min-w-100px text-center">Display Order</th>
                                <th class="min-w-120px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="symbol symbol-35px symbol-circle">
                                            <span class="symbol-label bg-light-primary text-primary fw-bold fs-6">{{ strtoupper(substr($role->role_name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-800 fw-bold">{{ $role->role_name }}</span>
                                            @if($role->getKey() == 1)
                                                <span class="badge badge-light-success fw-semibold fs-8 ms-1">Super Admin</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light fw-bold">{{ $role->display_order }}</span>
                                </td>
                                <td class="text-end">
                                    @if(validatePermissions('admin/acl/roles/edit'))
                                    <a href="{{ route('admin.acl.roles.edit', encryptIdForUrl($role->getKey())) }}" role="button" class="btn btn-light btn-sm me-1">Edit</a>
                                    @endif
                                    @if($role->getKey() != 1)
                                        @if(validatePermissions('admin/acl/roles/delete'))
                                        <form action="{{ route('admin.acl.roles.delete', encryptIdForUrl($role->getKey())) }}"
                                              method="POST" class="d-inline role-delete-form">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-role"
                                                    data-role-name="{{ $role->role_name }}">Delete</button>
                                        </form>
                                        @endif
                                    @else
                                        <span class="btn btn-sm btn-light disabled" title="Cannot delete Super Admin">Delete</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-10">No roles found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($roles->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                    <div class="fs-7 text-gray-700">{{ $roles->firstItem() ?? 0 }} - {{ $roles->lastItem() ?? 0 }} of {{ $roles->total() }}</div>
                    <div>{{ $roles->withQueryString()->links() }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
@section('footer')
@include('includes.admin_footer')
@stop
@section('script')
@include('includes.admin_scripts')
<script src="{{ asset('assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchEl = document.getElementById('roles-search-input');
    if (searchEl) {
        var searchTimer;
        searchEl.addEventListener('input', function() {
            clearTimeout(searchTimer);
            var q = this.value.trim();
            searchTimer = setTimeout(function() {
                var url = '{{ route("admin.acl.roles.listing") }}';
                var params = new URLSearchParams(window.location.search);
                if (q) params.set('search', q); else params.delete('search');
                params.delete('page');
                window.location.href = url + (params.toString() ? '?' + params.toString() : '');
            }, 400);
        });
    }
    // SweetAlert delete confirmation
    document.querySelectorAll('.btn-delete-role').forEach(function(btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var form = this.closest('form.role-delete-form');
            var name = this.getAttribute('data-role-name') || 'this role';
            if (!form) return;
            if (typeof Swal === 'undefined') {
                if (confirm('Delete ' + name + '?')) form.submit();
                return;
            }
            Swal.fire({
                title: 'Delete role?',
                text: 'Are you sure you want to delete \"' + name + '\"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it',
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@stop
