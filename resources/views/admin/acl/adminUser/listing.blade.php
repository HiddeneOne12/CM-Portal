@extends('layouts.admin')
@push('title')
Admin Users - {{ config('global.SITE_NAME') }}
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
                    <input type="text" name="search" value="{{ request('search') }}" id="admin-users-search-input" class="form-control form-control-solid w-250px ps-13" placeholder="Search Admin Users" autocomplete="off" />
                </div>
                <div class="card-toolbar">
                    @if(validatePermissions('admin/acl/admin-users/add'))
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-add-admin-user"><i class="ki-duotone ki-plus fs-2"></i>Add Admin User</a>
                    @endif
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table id="admin-users-table" class="table table-hover table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-150px">Username</th>
                                <th class="min-w-200px">Roles</th>
                                <th class="min-w-80px">Status</th>
                                <th class="min-w-100px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $admin)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="symbol symbol-35px symbol-circle">
                                            <span class="symbol-label bg-light-primary text-primary fw-bold fs-6">{{ strtoupper(substr($admin->user_name, 0, 1)) }}</span>
                                        </div>
                                        <span class="text-gray-800 fw-bold">{{ $admin->user_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php $roleNames = $admin->userRoles->pluck('role.role_name')->filter(); @endphp
                                    @if($roleNames->isEmpty())
                                        <span class="text-muted fst-italic">No roles</span>
                                    @else
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($roleNames as $roleName)
                                                <span class="badge badge-light-primary fw-semibold fs-8">{{ $roleName }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($admin->is_active)
                                        <span class="badge badge-light-success fw-bold">Active</span>
                                    @else
                                        <span class="badge badge-light-danger fw-bold">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if(validatePermissions('admin/acl/admin-users/edit'))
                                    <a href="javascript:void(0)" role="button" data-id="{{ $admin->id }}" data-token="{{ encryptIdForUrl($admin->id) }}" class="btn btn-light btn-sm btn-edit-admin-user me-1">Edit</a>
                                    @endif
                                    @if(auth('admin')->id() !== $admin->id)
                                        @if(validatePermissions('admin/acl/admin-users/delete'))
                                        <a href="javascript:void(0)" data-id="{{ encryptIdForUrl($admin->id) }}" class="btn btn-sm btn-danger btn-delete-admin-user" data-confirm-delete="Delete {{ $admin->user_name }}?">Delete</a>
                                        @endif
                                    @else
                                        <span class="btn btn-sm btn-light disabled" title="Cannot delete your own account">Delete</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-10">No admin users found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($admins->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                    <div class="fs-7 text-gray-700">{{ $admins->firstItem() ?? 0 }} - {{ $admins->lastItem() ?? 0 }} of {{ $admins->total() }}</div>
                    <div>{{ $admins->withQueryString()->links() }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
@section('models')
<div id="kt_activities" class="bg-body" data-kt-drawer-permanent="true" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'300px', 'lg': '500px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activities_toggle" data-kt-drawer-close="#kt_activities_close">
    <div class="card shadow-none border-0 rounded-0 w-100">
        <div class="card-header" id="kt_activities_header">
            <h3 class="card-title drawer-title fw-bold text-gray-900">Admin User</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activities_close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
        </div>
        <div class="drawer-body"></div>
    </div>
</div>
@endsection
@section('footer')
@include('includes.admin_footer')
@stop
@section('script')
@include('includes.admin_scripts')
<script src="{{ asset('assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
<script>
(function(){
    var token = $('meta[name="csrf-token"]').attr('content');
    $(document).on('click', '.btn-delete-admin-user', function(e) {
        e.preventDefault();
        var did = $(this).data('id');
        var msg = $(this).data('confirm-delete') || 'Are you sure?';
        Swal.fire({ title: 'Are you sure?', text: msg, icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes, delete it!' })
            .then(function(result) {
                if (result.value) {
                    $.ajax({ url: (typeof admin_url !== 'undefined' ? admin_url : (window.location.origin + '/cmcontrol')) + '/acl/admin-users/delete/' + did, method: 'POST', data: { _token: token } })
                        .done(function(data) {
                            var obj = typeof data === 'string' ? JSON.parse(data) : data;
                            if (obj.responseCode == 1) { Swal.fire('Success', obj.msg, 'success'); setTimeout(function() { location.reload(); }, 1500); }
                            else if (obj.msg) Swal.fire('Error', obj.msg, 'error');
                        });
                }
            });
    });
})();
document.addEventListener('DOMContentLoaded', function() {
    var baseUrl = typeof admin_url !== 'undefined' ? admin_url : (window.location.origin + '/cmcontrol');
    var ajaxHeaders = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' };
    var ajaxOpts = { headers: ajaxHeaders, xhrFields: { withCredentials: true } };
    var drawerEl = document.getElementById('kt_activities');

    function showDrawer(html, title) {
        var body = document.querySelector('#kt_activities .drawer-body');
        var titleEl = document.querySelector('#kt_activities .drawer-title');
        if (body) body.innerHTML = html;
        if (titleEl && title) titleEl.textContent = title;
        if (drawerEl && typeof KTDrawer !== 'undefined') KTDrawer.getInstance(drawerEl).show();
    }

    function hideDrawer() {
        if (drawerEl && typeof KTDrawer !== 'undefined') KTDrawer.getInstance(drawerEl).hide();
    }

    function parseJsonRes(res) {
        if (typeof res === 'string') {
            if (res.trim().startsWith('<')) return null;
            try { return JSON.parse(res); } catch (e) { return null; }
        }
        return res;
    }

    $(document).on('click', '.btn-add-admin-user', function(e) {
        e.preventDefault();
        $.ajax($.extend({ url: baseUrl + '/acl/admin-users/add', type: 'GET' }, ajaxOpts))
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.html) showDrawer(d.html, 'Add Admin User');
                else if (d && d.msg) showAdminAlert('error', d.msg);
                else showAdminAlert('error', 'Could not load form');
            })
            .fail(function(xhr) {
                var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                showAdminAlert('error', d && d.msg ? d.msg : 'Request failed');
            });
    });

    $(document).on('click', '.btn-edit-admin-user', function(e) {
        e.preventDefault();
        var token = $(this).data('token');
        if (!token) return;
        $.ajax($.extend({ url: baseUrl + '/acl/admin-users/edit/' + token, type: 'GET' }, ajaxOpts))
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.html) showDrawer(d.html, 'Edit Admin User');
                else if (d && d.msg) showAdminAlert('error', d.msg);
                else showAdminAlert('error', 'Could not load form');
            })
            .fail(function() { showAdminAlert('error', 'Request failed'); });
    });

    $(document).on('click', '#kt_activities .drawer-body .drawer-close', function() { hideDrawer(); });

    $(document).on('submit', '#kt_activities .drawer-body .admin-user-drawer-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var adminToken = form.find('input[name="admin_user_token"]').val();
        var url = baseUrl + '/acl/admin-users/add';
        if (adminToken) url = baseUrl + '/acl/admin-users/edit/' + adminToken;
        $.ajax($.extend({ url: url, type: 'POST', data: form.serialize() }, ajaxOpts))
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1) { hideDrawer(); showAdminAlert('success', d.msg); window.location.reload(); }
                else showAdminAlert('error', d && d.msg ? d.msg : 'Error');
            })
            .fail(function(xhr) {
                var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                showAdminAlert('error', d && d.msg ? d.msg : 'Request failed');
            });
    });

    var searchEl = document.getElementById('admin-users-search-input');
    if (searchEl) {
        var searchTimer;
        searchEl.addEventListener('input', function() {
            clearTimeout(searchTimer);
            var q = this.value.trim();
            searchTimer = setTimeout(function() {
                var url = '{{ route("admin.acl.admin-users.listing") }}';
                var params = new URLSearchParams(window.location.search);
                if (q) params.set('search', q); else params.delete('search');
                params.delete('page');
                window.location.href = url + (params.toString() ? '?' + params.toString() : '');
            }, 400);
        });
    }
});
</script>
@stop
