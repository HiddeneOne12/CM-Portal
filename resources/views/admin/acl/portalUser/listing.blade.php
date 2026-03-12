@extends('layouts.admin')
@push('title')
{{ $pageTitle }} - {{ config('global.SITE_NAME') }}
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
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <div class="d-flex align-items-center position-relative my-1 me-3">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" name="search" value="{{ request('search') }}" id="portal-user-search-input" class="form-control form-control-solid w-250px ps-13" placeholder="Search User (real-time)" autocomplete="off" />
                </div>
                <div class="card-toolbar">
                    @if(validatePermissions('admin/acl/portal-users/add'))
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-add"><i class="ki-duotone ki-plus fs-2"></i>Add User</a>
                    @endif
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table id="portal-users-table" class="table table-hover table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-80px">Image</th>
                                <th class="min-w-120px">First Name</th>
                                <th class="min-w-120px">Last Name</th>
                                <th class="min-w-100px">Status</th>
                                <th class="min-w-150px">Email</th>
                                <th class="min-w-100px">Phone</th>
                                <th class="min-w-80px">Gender</th>
                                <th class="min-w-120px">Created At</th>
                                <th class="min-w-120px">Updated At</th>
                                <th class="min-w-100px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($result ?? [] as $row)
                            <tr>
                                <td>
                                    @if($row->image)
                                    <img src="{{ asset($row->image) }}" alt="" class="rounded" style="width: 60px; height: 40px; object-fit: cover;">
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $row->first_name }}</td>
                                <td>{{ $row->last_name }}</td>
                                <td>
                                    @if(validatePermissions('admin/acl/portal-users/edit'))
                                    <a href="javascript:void(0)" class="status-toggle text-decoration-none" data-id="{{ $row->id }}" data-url="{{ route('admin.acl.portal-users.toggle-status', encryptIdForUrl($row->id)) }}" data-status="{{ (int) $row->status }}" title="Click to toggle">
                                        @if((int) $row->status === 1)
                                        <span class="badge badge-light-success">Enabled</span>
                                        @else
                                        <span class="badge badge-light-danger">Disabled</span>
                                        @endif
                                    </a>
                                    @else
                                    @if((int) $row->status === 1)
                                    <span class="badge badge-light-success">Enabled</span>
                                    @else
                                    <span class="badge badge-light-danger">Disabled</span>
                                    @endif
                                    @endif
                                </td>
                                <td>{{ $row->email }}</td>
                                <td>{{ $row->phone_number ?? '—' }}</td>
                                <td>{{ $row->gender ? ucfirst($row->gender) : '—' }}</td>
                                <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('M d, Y H:i') : '—' }}</td>
                                <td>{{ $row->updated_at ? \Carbon\Carbon::parse($row->updated_at)->format('M d, Y H:i') : '—' }}</td>
                                <td class="text-end">
                                    @if(validatePermissions('admin/acl/portal-users/edit'))
                                    <a href="javascript:void(0)" role="button" data-id="{{ $row->id }}" data-token="{{ encryptIdForUrl($row->id) }}" class="btn btn-light btn-sm btn-edit-form me-1">Edit</a>
                                    @endif
                                    @if(validatePermissions('admin/acl/portal-users/delete'))
                                    <a href="javascript:void(0)" data-id="{{ encryptIdForUrl($row->getKey()) }}" class="btn btn-sm btn-danger btn-delete" data-confirm-delete="Delete this user?">Delete</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-10">No users found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($result) && $result->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                    <div class="fs-7 text-gray-700">{{ $result->firstItem() ?? 0 }} - {{ $result->lastItem() ?? 0 }} of {{ $result->total() }}</div>
                    <div>{{ $result->withQueryString()->links() }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
@section('models')
<div id="kt_activities" class="bg-body" data-kt-drawer-permanent="true" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'300px', 'lg': '900px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activities_toggle" data-kt-drawer-close="#kt_activities_close">
    <div class="card shadow-none border-0 rounded-0 w-100">
        <div class="card-header" id="kt_activities_header">
            <h3 class="card-title drawer-title fw-bold text-gray-900">Portal User</h3>
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
     $(document).on("click", ".btn-delete", function (e) {

var did = $(this).data("id");
var currentRow = $(this).closest("tr");
var token = $('meta[name="csrf-token"]').attr('content');

Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    animation: !1,
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
}).then((result) => {

    if (result.value) {

        $.ajax({
            url: admin_url + "/acl/portal-users/delete/" + did,
            method: "POST",
            data: {
                _token: token
            },
            success: function (data) {

                var obj = JSON.parse(data);

                if (obj.responseCode == 1) {
    Swal.fire("Success", obj.msg, "success");

    setTimeout(function () {
        location.reload();
    }, 2000); // 2 second delay
}
            }
        });

    }
});
});
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

    (function() {
        var userSearch = document.getElementById('portal-user-search-input');
        var userSearchTimer;
        if (userSearch) {
            userSearch.addEventListener('input', function() {
                clearTimeout(userSearchTimer);
                var q = userSearch.value.trim();
                userSearchTimer = setTimeout(function() {
                    var url = '{{ route("admin.acl.portal-users.listing") }}';
                    var params = new URLSearchParams(window.location.search);
                    if (q) params.set('search', q); else params.delete('search');
                    params.delete('page');
                    window.location.href = url + (params.toString() ? '?' + params.toString() : '');
                }, 400);
            });
        }
    })();

    $(document).on('click', '.btn-add', function(e) {
        e.preventDefault();
        $.ajax($.extend({ url: baseUrl + '/acl/portal-users/add', type: 'GET' }, ajaxOpts))
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.html) showDrawer(d.html, 'Add User');
                else if (d && d.msg) showAdminAlert('error', d.msg);
                else showAdminAlert('error', 'Could not load form');
            })
            .fail(function(xhr) {
                var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                showAdminAlert('error', d && d.msg ? d.msg : 'Request failed');
            });
    });

    $(document).on('click', '.btn-edit-form', function(e) {
        e.preventDefault();
        var token = $(this).data('token');
        if (!token) return;
        $.ajax($.extend({ url: baseUrl + '/acl/portal-users/edit/' + token, type: 'GET' }, ajaxOpts))
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.html) showDrawer(d.html, 'Edit User');
                else if (d && d.msg) showAdminAlert('error', d.msg);
                else showAdminAlert('error', 'Could not load form');
            })
            .fail(function() { showAdminAlert('error', 'Request failed'); });
    });

    $(document).on('click', '#kt_activities .drawer-body .drawer-close', function() { hideDrawer(); });

    $(document).on('click', '.status-toggle', function(e) {
        e.preventDefault();
        var btn = $(this);
        var url = btn.data('url');
        if (!url) return;
        btn.css('pointer-events', 'none');
        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            headers: ajaxOpts.headers,
            xhrFields: ajaxOpts.xhrFields
        })
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1) {
                    var isEnabled = d.status === 1;
                    btn.data('status', d.status);
                    btn.find('.badge').remove();
                    if (isEnabled) {
                        btn.append('<span class="badge badge-light-success">Enabled</span>');
                    } else {
                        btn.append('<span class="badge badge-light-danger">Disabled</span>');
                    }
                    if (typeof showAdminAlert === 'function') showAdminAlert('success', 'Status updated.');
                } else if (d && d.msg) showAdminAlert('error', d.msg);
            })
            .fail(function() { showAdminAlert('error', 'Request failed'); })
            .always(function() { btn.css('pointer-events', 'auto'); });
    });

    $(document).on('submit', '#kt_activities .drawer-body form', function(e) {
        e.preventDefault();
        var form = $(this)[0];
        var portalUserToken = $(this).find('input[name="portal_user_token"]').val();
        var url = baseUrl + '/acl/portal-users/add';
        if (portalUserToken) url = baseUrl + '/acl/portal-users/edit/' + portalUserToken;
        var formData = new FormData(form);
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: ajaxOpts.headers,
            xhrFields: ajaxOpts.xhrFields
        })
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1) { hideDrawer(); showAdminAlert('success', d.msg); }
                else showAdminAlert('error', d && d.msg ? d.msg : 'Error');
            })
            .fail(function(xhr) {
                var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                showAdminAlert('error', d && d.msg ? d.msg : 'Request failed');
            });
    });
});
</script>
@stop
