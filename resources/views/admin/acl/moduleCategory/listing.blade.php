@extends('layouts.admin')
@push('title')
{{ $pageTitle }} - {{ config('global.SITE_NAME') }}
@endpush
@push('toolbar_actions')
@if (validatePermissions('admin/acl/module-categories'))
<div class="d-flex align-items-center gap-3 ms-auto">
    @if(validatePermissions('admin/acl/module-categories/add'))
    <a href="javascript:void(0)" class="btn btn-primary btn-sm btn-add border-anchor">
        <i class="ki-duotone ki-plus fs-2"></i>Add Module Category
    </a>
    @endif
    <div class="d-flex align-items-center position-relative">
        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        <input type="text" data-kt-vendor-table-filter="search" class="form-control form-control-solid w-100 min-w-200px ps-13" placeholder="Search Module Category">
    </div>
</div>
@endif
@endpush
@section('header')
@include('includes.admin_header_nav')
@stop
@section('toolbar')
@include('includes.toolbar')
@stop
@section('content')
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-fluid">
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-5 g-xl-9 container-fluid" id="cards-container">
        @if (isset($result) && $result->count())
        @foreach ($result as $row)
        <div class="col-md-3">
            <div class="card card-flush h-md-100">
                <div class="card-header">
                    <div class="card-title"><h2>{{ $row->category_name }}</h2></div>
                </div>
                <div class="card-body pt-1">
                    <div class="fw-bolder text-gray-600 mb-5">Total Modules in this Category: {{ $row->modules->count() }}</div>
                    <div class="d-flex flex-column text-gray-600">
                        @if ($row->modules->count())
                        @foreach ($row->modules->take(4) as $modules)
                        <div class="d-flex align-items-center py-2">
                            <span class="bullet bg-primary me-3"></span>{{ $modules->module_name }}
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="card-footer flex-wrap pt-0">
                    @if(validatePermissions('admin/acl/module-categories/edit'))
                    <a href="javascript:void(0)" role="button" data-id="{{ $row->getKey() }}" data-token="{{ encryptIdForUrl($row->getKey()) }}" class="btn btn-light btn-active-light-primary btn-edit my-1">Edit</a>
                    @endif
                    @if(validatePermissions('admin/acl/module-categories/delete'))
                    <a href="javascript:void(0)" data-id="{{ encryptIdForUrl($row->getKey()) }}" class="btn btn-light btn-active-light-primary my-1 btn-delete"  data-confirm-delete="Delete this category?">Delete</a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>
@stop
@section('models')
<div id="kt_drawer_chat" class="bg-body" data-kt-drawer-permanent="true" data-kt-drawer="true" data-kt-drawer-name="chat" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'300px', 'md': '500px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_drawer_chat_toggle" data-kt-drawer-close="#kt_drawer_chat_close">
    <div class="card w-100 border-0 rounded-0" id="kt_drawer_chat_messenger">
        <div class="card-header pe-5" id="kt_drawer_chat_messenger_header">
            <div class="card-title">
                <div class="d-flex justify-content-center flex-column me-3">
                    <span class="fs-4 fw-bold drawer-title text-gray-900 me-1 mb-2 lh-1">Module Category</span>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="kt_drawer_chat_close">
                    <i class="ki-duotone ki-cross-square fs-2"><span class="path1"></span><span class="path2"></span></i>
                </div>
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
            url: admin_url + "/acl/module-categories/delete/" + did,
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

    var drawerEl = document.getElementById('kt_drawer_chat');
    function showDrawer(html) {
        var body = document.querySelector('#kt_drawer_chat .drawer-body');
        if (body) body.innerHTML = html;
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

    var ajaxOpts = { headers: ajaxHeaders, xhrFields: { withCredentials: true } };

    $(document).on('click', '.btn-add', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $.ajax($.extend({ url: baseUrl + '/acl/module-categories/add', type: 'GET' }, ajaxOpts))
            .done(function(res, textStatus, xhr) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.html) showDrawer(d.html);
                else if (d && d.msg) showAdminAlert('error', d.msg);
                else if (res && (res + '').trim().indexOf('<') === 0) showAdminAlert('error', 'Server returned a page instead of data. Open: ' + baseUrl + '/acl/module-categories/add in a new tab to check.');
                else showAdminAlert('error', 'Could not load form');
            })
            .fail(function(xhr) {
                var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                if (d && d.msg) showAdminAlert('error', d.msg);
                else if (xhr.status === 0) showAdminAlert('error', 'Network error. Check the console.');
                else showAdminAlert('error', 'Request failed: ' + (xhr.status || xhr.statusText || 'Unknown'));
            });
    });

    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var token = $(this).data('token');
        if (!token) return;
        $.ajax($.extend({ url: baseUrl + '/acl/module-categories/edit/' + token, type: 'GET' }, ajaxOpts))
            .done(function(res, textStatus, xhr) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.html) showDrawer(d.html);
                else if (d && d.msg) showAdminAlert('error', d.msg);
                else if (res && (res + '').trim().indexOf('<') === 0) showAdminAlert('error', 'Server returned a page instead of data. Check the edit URL in a new tab.');
                else showAdminAlert('error', 'Could not load form');
            })
            .fail(function(xhr) {
                var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                if (d && d.msg) showAdminAlert('error', d.msg);
                else if (xhr.status === 0) showAdminAlert('error', 'Network error. Check the console.');
                else showAdminAlert('error', 'Request failed: ' + (xhr.status || xhr.statusText || 'Unknown'));
            });
    });

    $(document).on('click', '#kt_drawer_chat .drawer-body .drawer-close', function() { hideDrawer(); });

    $(document).on('submit', '#kt_drawer_chat .drawer-body form', function(e) {
        e.preventDefault();
        var form = $(this), moduleCategoryToken = form.find('input[name="module_category_token"]').val(), url = baseUrl + '/acl/module-categories/add';
        if (moduleCategoryToken) url = baseUrl + '/acl/module-categories/edit/' + moduleCategoryToken;
        $.ajax($.extend({ url: url, type: 'POST', data: form.serialize() }, ajaxOpts))
            .done(function(res) {
                try {
                    var d = typeof res === 'string' ? JSON.parse(res) : res;
                    if (d && d.responseCode === 1) { hideDrawer(); showAdminAlert('success', d.msg); }
                    else showAdminAlert('error', d && d.msg ? d.msg : 'Error');
                } catch (err) { showAdminAlert('success', 'Saved'); }
            })
            .fail(function(xhr) { showAdminAlert('error', 'Request failed: ' + (xhr.statusText || xhr.status)); });
    });

    $('input[data-kt-vendor-table-filter="search"]').on('input', function() {
        var q = $(this).val();
        $.get(baseUrl + '/acl/module-categories/search', { word: q }, function(res) {
            try {
                var d = typeof res === 'string' ? JSON.parse(res) : res;
                if (d && d.responseCode === 1 && d.html) $('#cards-container').html(d.html);
            } catch (err) {}
        });
    });
});
</script>
@stop
