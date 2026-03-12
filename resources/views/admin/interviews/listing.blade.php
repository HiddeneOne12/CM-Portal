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
                    <input type="text" name="search" value="{{ request('search') }}" id="interviews-search-input" class="form-control form-control-solid w-250px ps-13" placeholder="Search Interviews (real-time)" autocomplete="off" />
                </div>
                <div class="card-toolbar">
                    @if (validatePermissions('admin/acl/interviews/add'))
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-add-interview"><i class="ki-duotone ki-plus fs-2"></i>Add Interview</a>
                    @endif
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table id="interviews-table" class="table table-hover table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-80px">Image</th>
                                <th class="min-w-150px">Title</th>
                                <th class="min-w-200px">Description</th>
                                <th class="min-w-100px">Status</th>
                                <th class="min-w-120px">Interview of</th>
                                <th class="min-w-120px">Video</th>
                                <th class="min-w-80px">Duration</th>
                                <th class="min-w-120px">Created At</th>
                                <th class="min-w-120px">Updated At</th>
                                <th class="min-w-100px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($interviewItems ?? [] as $row)
                            <tr>
                                <td>
                                    @if($row->image)
                                    <img src="{{ asset($row->image) }}" alt="" class="rounded" style="width: 60px; height: 40px; object-fit: cover;">
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($row->title, 40) }}</td>
                                <td>{{ Str::limit($row->description, 50) ?: '—' }}</td>
                                <td>
                                    @if (validatePermissions('admin/acl/interviews/edit'))
                                    <a href="javascript:void(0)" class="status-toggle text-decoration-none" data-id="{{ $row->id }}" data-url="{{ route('admin.acl.interviews.toggle-status', encryptIdForUrl($row->id)) }}" data-status="{{ (int) $row->status }}" title="Click to toggle">
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
                                <td>{{ $row->participant ? $row->participant->name : '—' }}</td>
                                <td>
                                    @if($row->video)
                                        <a href="javascript:void(0)" class="text-success video-preview" data-type="file" data-src="{{ asset($row->video) }}">Uploaded</a>
                                    @elseif($row->video_link)
                                        <a href="javascript:void(0)" class="text-primary text-hover-primary video-preview" data-type="link" data-src="{{ $row->video_link }}">{{ Str::limit($row->video_link, 30) }}</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $row->duration ?? '—' }}</td>
                                <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('M d, Y H:i') : '—' }}</td>
                                <td>{{ $row->updated_at ? \Carbon\Carbon::parse($row->updated_at)->format('M d, Y H:i') : '—' }}</td>
                                <td class="text-end">
                                    @if (validatePermissions('admin/acl/interviews/edit'))
                                    <a href="javascript:void(0)" role="button" data-id="{{ $row->id }}" data-token="{{ encryptIdForUrl($row->id) }}" class="btn btn-light btn-sm btn-edit-interview me-1">Edit</a>
                                    @endif
                                    @if (validatePermissions('admin/acl/interviews/delete'))
                                    <a href="javascript:void(0)" data-id="{{ encryptIdForUrl($row->getKey()) }}" class="btn btn-sm btn-danger btn-delete"  data-confirm-delete="Delete this interview?">Delete</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-10">No interviews yet. Click "Add Interview" to add one.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($interviewItems) && $interviewItems->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                    <div class="fs-7 text-gray-700">{{ $interviewItems->firstItem() ?? 0 }} - {{ $interviewItems->lastItem() ?? 0 }} of {{ $interviewItems->total() }}</div>
                    <div>{{ $interviewItems->withQueryString()->links() }}</div>
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
            <h3 class="card-title drawer-title fw-bold text-gray-900">Interviews</h3>
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
            url: admin_url + "/acl/interviews/delete/" + did,
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

    $(document).on('click', '.btn-add-interview', function(e) {
        e.preventDefault();
        $.ajax($.extend({ url: baseUrl + '/acl/interviews/add', type: 'GET' }, ajaxOpts))
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.html) showDrawer(d.html, 'Add Interview');
                else if (d && d.msg) showAdminAlert('error', d.msg);
                else showAdminAlert('error', 'Could not load form');
            })
            .fail(function(xhr) {
                var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                showAdminAlert('error', d && d.msg ? d.msg : 'Request failed');
            });
    });

    $(document).on('click', '.btn-edit-interview', function(e) {
        e.preventDefault();
        var token = $(this).data('token');
        if (!token) return;
        $.ajax($.extend({ url: baseUrl + '/acl/interviews/edit/' + token, type: 'GET' }, ajaxOpts))
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.html) showDrawer(d.html, 'Edit Interview');
                else if (d && d.msg) showAdminAlert('error', d.msg);
                else showAdminAlert('error', 'Could not load form');
            })
            .fail(function() { showAdminAlert('error', 'Request failed'); });
    });

    (function() {
        var interviewsSearch = document.getElementById('interviews-search-input');
        var interviewsSearchTimer;
        if (interviewsSearch) {
            interviewsSearch.addEventListener('input', function() {
                clearTimeout(interviewsSearchTimer);
                var q = interviewsSearch.value.trim();
                interviewsSearchTimer = setTimeout(function() {
                    var url = '{{ route("admin.acl.interviews.listing") }}';
                    var params = new URLSearchParams(window.location.search);
                    if (q) params.set('search', q); else params.delete('search');
                    params.delete('page');
                    window.location.href = url + (params.toString() ? '?' + params.toString() : '');
                }, 400);
            });
        }
    })();

    function buildVideoHtml(type, src) {
        if (!src) {
            return '<p class="text-muted mb-0">No video available.</p>';
        }

        if (type === 'link') {
            var ytMatch = src.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
            if (ytMatch && ytMatch[1]) {
                var embed = 'https://www.youtube.com/embed/' + ytMatch[1];
                return '' +
                    '<div class="ratio ratio-16x9">' +
                        '<iframe src="' + embed + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' +
                    '</div>';
            }

            // Fallback: treat as direct video URL
            return '' +
                '<video controls style="width:100%;max-height:480px;">' +
                    '<source src="' + src + '">' +
                    'Your browser does not support the video tag.' +
                '</video>';
        }

        // Uploaded file
        return '' +
            '<video controls style="width:100%;max-height:480px;">' +
                '<source src="' + src + '">' +
                'Your browser does not support the video tag.' +
            '</video>';
    }

    $(document).on('click', '.video-preview', function(e) {
        e.preventDefault();
        var type = $(this).data('type') || 'file';
        var src = $(this).data('src') || '';
        var html = '' +
            '<div class="p-5">' +
                '<div class="mb-5">' + buildVideoHtml(type, src) + '</div>' +
                '<div class="text-end">' +
                    '<button type="button" class="btn btn-sm btn-light drawer-close">Close</button>' +
                '</div>' +
            '</div>';
        showDrawer(html, 'Preview Interview Video');
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
        var interviewToken = $(this).find('input[name="interview_token"]').val();
        var url = baseUrl + '/acl/interviews/add';
        if (interviewToken) url = baseUrl + '/acl/interviews/edit/' + interviewToken;
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
