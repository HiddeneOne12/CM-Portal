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
                    <input type="text" name="search" value="{{ request('search') }}" id="companies-search-input" class="form-control form-control-solid w-250px ps-13" placeholder="Search Companies" autocomplete="off" />
                </div>
                <div class="card-toolbar">
                    @if (validatePermissions('admin/acl/companies/add'))
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-add-company"><i class="ki-duotone ki-plus fs-2"></i>Add Company</a>
                    @endif
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table id="companies-table" class="table table-hover table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-150px">Name</th>
                                <th class="min-w-120px">Type</th>
                                <th class="min-w-120px">Created At</th>
                                <th class="min-w-120px">Updated At</th>
                                <th class="min-w-100px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items ?? [] as $row)
                            <tr>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->type ?? '—' }}</td>
                                <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('M d, Y H:i') : '—' }}</td>
                                <td>{{ $row->updated_at ? \Carbon\Carbon::parse($row->updated_at)->format('M d, Y H:i') : '—' }}</td>
                                <td class="text-end">
                                    @if (validatePermissions('admin/acl/companies/edit'))
                                    <a href="javascript:void(0)" role="button" data-id="{{ $row->id }}" data-token="{{ encryptIdForUrl($row->id) }}" class="btn btn-light btn-sm btn-edit-company me-1">Edit</a>
                                    @endif
                                    @if (validatePermissions('admin/acl/companies/delete'))
                                    <a href="javascript:void(0)" data-id="{{ encryptIdForUrl($row->getKey()) }}"class="btn btn-sm btn-danger btn-delete"  data-confirm-delete="Delete this company?">Delete</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-10">No companies yet. Click "Add Company" to add one.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($items) && $items->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                    <div class="fs-7 text-gray-700">{{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} of {{ $items->total() }}</div>
                    <div>{{ $items->withQueryString()->links() }}</div>
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
            <h3 class="card-title drawer-title fw-bold text-gray-900">Companies</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activities_close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
        </div>
        <div class="drawer-body"></div>
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
            url: admin_url + "/acl/companies/delete/" + did,
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

    $(document).on('click', '.btn-add-company', function(e) {
        e.preventDefault();
        $.ajax($.extend({ url: baseUrl + '/acl/companies/add', type: 'GET' }, ajaxOpts))
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.html) showDrawer(d.html, 'Add Company');
                else if (d && d.msg) showAdminAlert('error', d.msg);
                else showAdminAlert('error', 'Could not load form');
            })
            .fail(function(xhr) {
                var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                showAdminAlert('error', d && d.msg ? d.msg : 'Request failed');
            });
    });

    $(document).on('click', '.btn-edit-company', function(e) {
        e.preventDefault();
        var token = $(this).data('token');
        if (!token) return;
        $.ajax($.extend({ url: baseUrl + '/acl/companies/edit/' + token, type: 'GET' }, ajaxOpts))
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.html) showDrawer(d.html, 'Edit Company');
                else if (d && d.msg) showAdminAlert('error', d.msg);
                else showAdminAlert('error', 'Could not load form');
            })
            .fail(function() { showAdminAlert('error', 'Request failed'); });
    });

    (function() {
        var searchEl = document.getElementById('companies-search-input');
        var searchTimer;
        if (searchEl) {
            searchEl.addEventListener('input', function() {
                clearTimeout(searchTimer);
                var q = searchEl.value.trim();
                searchTimer = setTimeout(function() {
                    var url = '{{ route("admin.acl.companies.listing") }}';
                    var params = new URLSearchParams(window.location.search);
                    if (q) params.set('search', q); else params.delete('search');
                    params.delete('page');
                    window.location.href = url + (params.toString() ? '?' + params.toString() : '');
                }, 400);
            });
        }
    })();

    $(document).on('click', '#kt_activities .drawer-body .drawer-close', function() { hideDrawer(); });

    $(document).on('submit', '#kt_activities .drawer-body form', function(e) {
        e.preventDefault();
        var form = $(this)[0];
        var companyToken = $(this).find('input[name="company_token"]').val();
        var url = baseUrl + '/acl/companies/add';
        if (companyToken) url = baseUrl + '/acl/companies/edit/' + companyToken;
        $.ajax({
            url: url,
            type: 'POST',
            data: $(form).serialize(),
            headers: ajaxOpts.headers,
            xhrFields: ajaxOpts.xhrFields
        })
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
});
</script>
@stop
