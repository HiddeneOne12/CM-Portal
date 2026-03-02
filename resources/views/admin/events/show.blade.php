@extends('layouts.admin')
@push('title')
{{ $pageTitle }} - {{ config('global.SITE_NAME') }}
@endpush
@push('css')
<style>.first-participant-row .btn-remove-ep { display: none !important; }</style>
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
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Event Details</span>
                </h3>
                <div class="card-toolbar">
                    <a href="{{ route('admin.acl.events.listing') }}" class="btn btn-sm btn-light me-2">Back to Events</a>
                    @if (validatePermissions('admin/acl/events/edit'))
                    <a href="javascript:void(0)" role="button" data-id="{{ $event->id }}" data-token="{{ encryptIdForUrl($event->id) }}" class="btn btn-sm btn-primary btn-edit-event me-2">Edit</a>
                    @endif
                    @if (validatePermissions('admin/acl/events/delete'))
                    <a href="{{ route('admin.acl.events.delete', encryptIdForUrl($event->id)) }}" class="btn btn-sm btn-danger" data-confirm-delete="Delete this event?">Delete</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row g-5">
                    <div class="col-12">
                        @if($event->image)
                        <div class="mb-5">
                            <img src="{{ asset($event->image) }}" alt="{{ $event->title }}" class="rounded w-100" style="max-height: 400px; object-fit: cover;">
                        </div>
                        @endif
                        <h1 class="fw-bold mb-3">{{ $event->title }}</h1>
                        <dl class="row mb-5">
                            <dt class="col-sm-3 text-muted">Event Date</dt>
                            <dd class="col-sm-9">{{ $event->event_date ? $event->event_date->format('F j, Y') : '—' }}</dd>
                            <dt class="col-sm-3 text-muted">Start – End time</dt>
                            <dd class="col-sm-9">{{ $event->start_time ? substr($event->start_time, 0, 5) : '—' }} – {{ $event->end_time ? substr($event->end_time, 0, 5) : '—' }}</dd>
                            <dt class="col-sm-3 text-muted">Location</dt>
                            <dd class="col-sm-9">{{ $event->location ?: '—' }}</dd>
                            <dt class="col-sm-3 text-muted">Company</dt>
                            <dd class="col-sm-9">{{ $event->company ? $event->company->name : '—' }}</dd>
                        </dl>
                        @if($event->description)
                        <h5 class="fw-semibold mb-2">Description</h5>
                        <p class="text-gray-700 mb-5">{{ nl2br(e($event->description)) }}</p>
                        @endif
                        @if($event->highlights)
                        <h5 class="fw-semibold mb-2">Highlights</h5>
                        <p class="text-gray-700 mb-5">{{nl2br(e($event->highlights))}}</p>
                        @endif
                    </div>

                    @if($event->eventImages && $event->eventImages->count() > 0)
                    <div class="col-12">
                        <h5 class="fw-semibold mb-3">Event Images (Gallery)</h5>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach($event->eventImages as $img)
                            <div>
                                <img src="{{ asset($img->image) }}" alt="" class="rounded" style="height: 180px; width: auto; max-width: 280px; object-fit: cover;">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="col-12">
                        <h5 class="fw-semibold mb-3">Participants</h5>
                        @if (validatePermissions('admin/acl/events/edit'))
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-primary btn-ep-add-participant">Add Participant</button>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-3" id="event-participants-table" data-event-id="{{ $event->id }}" data-event-token="{{ encryptIdForUrl($event->id) }}" data-event-start="{{ $event->start_time ? substr($event->start_time, 0, 5) : '' }}" data-event-end="{{ $event->end_time ? substr($event->end_time, 0, 5) : '' }}">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th>Participant</th>
                                        <th>Image</th>
                                        <th>Topic</th>
                                        <th>Description</th>
                                        <th>Time</th>
                                        <th>Documents</th>
                                        @if (validatePermissions('admin/acl/events/edit'))
                                        <th class="text-end" style="width: 100px;">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($event->eventParticipants && $event->eventParticipants->count() > 0)
                                    @foreach($event->eventParticipants as $ep)
                                    @php $p = $ep->participant; @endphp
                                    <tr class="ep-view-row" data-ep-id="{{ $ep->id }}" data-participant-id="{{ $p->id }}" data-ep-image="{{ $ep->image ? asset($ep->image) : '' }}" data-ep-video="{{ $ep->video ? '1' : '' }}" data-description="{{ e($ep->description ?? '') }}" data-topic="{{ e($ep->topic) }}" data-start-time="{{ $ep->start_time ? substr($ep->start_time, 0, 5) : '' }}" data-end-time="{{ $ep->end_time ? substr($ep->end_time, 0, 5) : '' }}">
                                        <td class="ep-cell-participant">
                                            <div class="d-flex align-items-center">
                                                @if($p->image)
                                                <img src="{{ asset($p->image) }}" alt="" class="rounded me-3 ep-participant-img" style="width: 48px; height: 48px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <span class="fw-semibold ep-participant-name">{{ $p->name }}</span>
                                                    @if($p->position || $p->company)
                                                    <br><span class="text-muted small ep-participant-meta">{{ $p->position }}{{ $p->position && $p->company ? ', ' : '' }}{{ $p->company ? $p->company->name : '' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="ep-cell-image">
                                            @if($ep->video)
                                            <span class="badge badge-light-info">Video</span>
                                            @elseif($ep->image)
                                            <img src="{{ asset($ep->image) }}" alt="" class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                            @else
                                            —
                                            @endif
                                        </td>
                                        <td class="ep-cell-topic">{{ $ep->topic ?: '—' }}</td>
                                        <td class="ep-cell-description">{{ Str::limit($ep->description, 40) ?: '—' }}</td>
                                        <td class="ep-cell-time">{{ $ep->start_time ? substr($ep->start_time, 0, 5) : '—' }} – {{ $ep->end_time ? substr($ep->end_time, 0, 5) : '—' }}</td>
                                        <td class="ep-cell-docs">
                                            <div class="ep-docs-list">
                                                @if($ep->eventParticipantDocuments && $ep->eventParticipantDocuments->count() > 0)
                                                @foreach($ep->eventParticipantDocuments as $doc)
                                                <div class="ep-doc-item d-flex align-items-center gap-1 mb-1" data-doc-id="{{ $doc->id }}">
                                                    <a href="{{ asset($doc->file_path) }}" target="_blank" rel="noopener" class="text-primary small ep-doc-link">{{ $doc->title }}</a>
                                                    @if (validatePermissions('admin/acl/events/edit'))
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-warning btn-ep-doc-edit" title="Edit title"><i class="ki-duotone ki-pencil fs-6"><span class="path1"></span><span class="path2"></span></i></button>
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-ep-doc-remove" title="Remove"><i class="ki-duotone ki-trash fs-6"><span class="path1"></span><span class="path2"></span></i></button>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif
                                            @if (validatePermissions('admin/acl/events/edit'))
                                            <div class="ep-doc-add-wrap mt-1">
                                                <button type="button" class="btn btn-sm btn-light-success btn-ep-doc-add">Add document</button>
                                            </div>
                                            @endif
                                            <span class="ep-docs-empty text-muted" @if($ep->eventParticipantDocuments && $ep->eventParticipantDocuments->count() > 0) style="display:none" @endif>—</span>
                                        </td>
                                        @if (validatePermissions('admin/acl/events/edit'))
                                        <td class="ep-cell-actions text-end">
                                            <button type="button" class="btn btn-sm btn-light-primary btn-ep-inline-edit me-1">Edit</button>
                                            <button type="button" class="btn btn-sm btn-light-danger btn-ep-delete" title="Remove participant">Delete</button>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr class="ep-empty-placeholder">
                                        <td colspan="{{ validatePermissions('admin/acl/events/edit') ? 8 : 7 }}" class="text-center text-muted py-5">No participants yet.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('models')
<div id="kt_activities" class="bg-body" data-kt-drawer-permanent="true" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'300px', 'lg': '900px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activities_toggle" data-kt-drawer-close="#kt_activities_close">
    <div class="card shadow-none border-0 rounded-0 w-100">
        <div class="card-header" id="kt_activities_header">
            <h3 class="card-title drawer-title fw-bold text-gray-900">Events</h3>
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
var eventViewParticipants = @json($participants ?? []);
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var baseUrl = typeof admin_url !== 'undefined' ? admin_url : (window.location.origin + '/admin');
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
        if (typeof res === 'string') { try { return JSON.parse(res); } catch (e) { return null; } }
        return res;
    }
    $(document).on('click', '.btn-edit-event', function(e) {
        e.preventDefault();
        var token = $(this).data('token');
        if (!token) return;
        $.ajax($.extend({ url: baseUrl + '/acl/events/edit/' + token, type: 'GET' }, ajaxOpts))
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.html) showDrawer(d.html, 'Edit Event');
                else if (d && d.msg) showAdminAlert('error', d.msg);
                else showAdminAlert('error', 'Could not load form');
            })
            .fail(function() { showAdminAlert('error', 'Request failed'); });
    });
    $(document).on('click', '#kt_activities .drawer-body .drawer-close', function() { hideDrawer(); });
    $(document).on('click', '#kt_activities #add-event-participant-row', function() {
        var container = $('#kt_activities #event-participants-rows');
        var first = container.find('.event-participant-row').first();
        if (first.length) {
            var newIndex = container.find('.event-participant-row').length;
            var clone = first.clone();
            clone.removeClass('first-participant-row');
            clone.find('input:not([type="hidden"]), select').val('');
            clone.find('input[name="ep_id[]"]').val('');
            clone.find('.ep-doc-rows').empty();
            clone.attr('data-ep-row-index', newIndex);
            clone.find('.btn-add-doc').attr('data-ep-row-index', newIndex);
            clone.find('input[name^="ep_doc_titles["]').attr('name', 'ep_doc_titles[' + newIndex + '][]');
            clone.find('input[name^="ep_doc_ids["]').attr('name', 'ep_doc_ids[' + newIndex + '][]');
            clone.find('input[name^="ep_doc_files["]').attr('name', 'ep_doc_files[' + newIndex + '][]');
            clone.find('.form-check-input[name="ep_doc_remove[]"]').prop('checked', false);
            container.append(clone);
        }
    });
    $(document).on('click', '#kt_activities .btn-remove-ep', function() {
        var row = $(this).closest('.event-participant-row');
        if ($('#kt_activities .event-participant-row').length > 1) row.remove();
    });
    $(document).on('click', '#kt_activities .btn-add-doc', function() {
        var row = $(this).closest('.event-participant-row');
        var ri = row.data('ep-row-index');
        var docRow = '<div class="ep-doc-row d-flex flex-wrap gap-2 align-items-end mb-2">' +
            '<input type="text" name="ep_doc_titles[' + ri + '][]" placeholder="Presentation name" class="form-control form-control-sm" style="min-width:180px">' +
            '<input type="file" name="ep_doc_files[' + ri + '][]" accept=".pdf" class="form-control form-control-sm" style="max-width:200px">' +
            '<button type="button" class="btn btn-sm btn-light-danger btn-remove-doc">×</button></div>';
        row.find('.ep-doc-rows').append(docRow);
    });
    $(document).on('click', '#kt_activities .btn-remove-doc', function() { $(this).closest('.ep-doc-row').remove(); });
    $(document).on('submit', '#kt_activities .drawer-body form', function(e) {
        e.preventDefault();
        var form = $(this)[0];
        var eventToken = $(this).find('input[name="event_token"]').val();
        var url = baseUrl + '/acl/events/add';
        if (eventToken) url = baseUrl + '/acl/events/edit/' + eventToken;
        var formData = new FormData(form);
        $.ajax({ url: url, type: 'POST', data: formData, processData: false, contentType: false, headers: ajaxOpts.headers, xhrFields: ajaxOpts.xhrFields })
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

    // Inline edit participants on event view
    function buildParticipantSelect(selectedId) {
        var html = '<select class="form-select form-select-sm ep-edit-participant-id">';
        (eventViewParticipants || []).forEach(function(p) {
            var sel = (p.id == selectedId) ? ' selected' : '';
            html += '<option value="' + (p.id || '') + '"' + sel + '>' + (p.name || '').replace(/</g, '&lt;') + '</option>';
        });
        html += '</select>';
        return html;
    }
    function buildDisplayParticipant(participant) {
        var imgPath = (participant.image || '').replace(/^\/+/, '');
        var img = imgPath ? '<img src="/' + imgPath + '" alt="" class="rounded me-3 ep-participant-img" style="width:48px;height:48px;object-fit:cover;">' : '';
        var meta = (participant.position || participant.company_name) ? '<br><span class="text-muted small ep-participant-meta">' + (participant.position || '') + (participant.position && participant.company_name ? ', ' : '') + (participant.company_name || '') + '</span>' : '';
        return '<div class="d-flex align-items-center">' + img + '<div><span class="fw-semibold ep-participant-name">' + (participant.name || '').replace(/</g, '&lt;') + '</span>' + meta + '</div></div>';
    }
    function buildDisplayImage(epImage, epVideo) {
        if (epVideo && (epVideo || '').trim() !== '') return '<span class="badge badge-light-info">Video</span>';
        if (!epImage || (epImage || '').trim() === '') return '—';
        var path = (epImage || '').replace(/^\/+/, '');
        return '<img src="/' + path + '" alt="" class="rounded" style="width:48px;height:48px;object-fit:cover;">';
    }
    function truncateDesc(s, len) {
        if (!s || (s || '').trim() === '') return '—';
        s = (s || '').replace(/</g, '&lt;');
        return s.length <= (len || 40) ? s : s.substring(0, len || 40) + '…';
    }
    function escapeHtml(s) { return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function buildDocsCellHtml(epId, docs) {
        var html = '<div class="ep-docs-list">';
        (docs || []).forEach(function(d) {
            var path = (d.file_path || '').replace(/^\/+/, '');
            html += '<div class="ep-doc-item d-flex align-items-center gap-1 mb-1" data-doc-id="' + d.id + '"><a href="/' + path + '" target="_blank" rel="noopener" class="text-primary small ep-doc-link">' + escapeHtml(d.title) + '</a><button type="button" class="btn btn-sm btn-icon btn-light-warning btn-ep-doc-edit" title="Edit title"><i class="ki-duotone ki-pencil fs-6"><span class="path1"></span><span class="path2"></span></i></button><button type="button" class="btn btn-sm btn-icon btn-light-danger btn-ep-doc-remove" title="Remove"><i class="ki-duotone ki-trash fs-6"><span class="path1"></span><span class="path2"></span></i></button></div>';
        });
        html += '</div><div class="ep-doc-add-wrap mt-1"><button type="button" class="btn btn-sm btn-light-success btn-ep-doc-add">Add document</button></div>';
        html += '<span class="ep-docs-empty text-muted" style="' + ((docs && docs.length > 0) ? 'display:none' : '') + '">—</span>';
        return html;
    }
    $(document).on('click', '.btn-ep-inline-edit', function() {
        var row = $(this).closest('tr.ep-view-row');
        if (row.hasClass('ep-row-editing')) return;
        var epId = row.data('ep-id');
        var pid = row.data('participant-id');
        var topic = row.data('topic') || '';
        var description = row.data('description') || '';
        var startTime = row.data('start-time') || '';
        var endTime = row.data('end-time') || '';
        var epImage = row.data('ep-image') || '';
        var epVideo = row.data('ep-video') || '';
        row.data('original-participant', row.find('.ep-cell-participant').html());
        row.data('original-image', row.find('.ep-cell-image').html());
        row.data('original-description', row.find('.ep-cell-description').html());
        row.data('original-topic', row.find('.ep-cell-topic').html());
        row.data('original-time', row.find('.ep-cell-time').html());
        row.data('original-actions', row.find('.ep-cell-actions').html());
        row.find('.ep-cell-participant').html(buildParticipantSelect(pid));
        var imgPreview = epVideo ? '<div class="mb-1"><span class="badge badge-light-info">Video</span></div>' : (epImage ? '<div class="mb-1"><img src="' + epImage.replace(/"/g, '&quot;') + '" alt="" class="rounded" style="width:48px;height:48px;object-fit:cover;"></div>' : '');
        row.find('.ep-cell-image').html(imgPreview + '<label class="small text-muted d-block">Image</label><input type="file" class="form-control form-control-sm ep-edit-image mb-1" accept=".jpg,.jpeg,.png"><label class="small text-muted d-block">Video</label><input type="file" class="form-control form-control-sm ep-edit-video" accept=".mp4,.webm,.mov,video/mp4,video/webm,video/quicktime">');
        row.find('.ep-cell-description').html('<textarea class="form-control form-control-sm ep-edit-description" rows="2" placeholder="Description">' + (description || '').replace(/</g, '&lt;').replace(/"/g, '&quot;') + '</textarea>');
        row.find('.ep-cell-topic').html('<input type="text" class="form-control form-control-sm ep-edit-topic" value="' + (topic || '').replace(/"/g, '&quot;') + '" placeholder="Topic">');
        row.find('.ep-cell-time').html('<input type="time" class="form-control form-control-sm d-inline-block ep-edit-start-time" style="width:auto" value="' + startTime + '"> – <input type="time" class="form-control form-control-sm d-inline-block ep-edit-end-time" style="width:auto" value="' + endTime + '">');
        row.find('.ep-cell-actions').html('<button type="button" class="btn btn-sm btn-success btn-ep-inline-save me-1">Save</button><button type="button" class="btn btn-sm btn-light btn-ep-inline-cancel">Cancel</button>');
        row.addClass('ep-row-editing');
    });
    $(document).on('click', '.btn-ep-inline-cancel', function() {
        var row = $(this).closest('tr.ep-view-row');
        if (row.hasClass('ep-row-new')) {
            row.remove();
            return;
        }
        row.find('.ep-cell-participant').html(row.data('original-participant'));
        row.find('.ep-cell-image').html(row.data('original-image'));
        row.find('.ep-cell-description').html(row.data('original-description'));
        row.find('.ep-cell-topic').html(row.data('original-topic'));
        row.find('.ep-cell-time').html(row.data('original-time'));
        row.find('.ep-cell-actions').html(row.data('original-actions'));
        row.removeClass('ep-row-editing');
    });
    $(document).on('click', '.btn-ep-inline-save', function() {
        var row = $(this).closest('tr.ep-view-row');
        var epId = row.data('ep-id');
        var isNew = row.hasClass('ep-row-new');
        var participantId = row.find('.ep-edit-participant-id').val();
        if (!participantId && (eventViewParticipants || []).length > 0) {
            if (typeof showAdminAlert === 'function') showAdminAlert('error', 'Please select a participant.');
            return;
        }
        var topic = row.find('.ep-edit-topic').val();
        var description = row.find('.ep-edit-description').val();
        var startTime = row.find('.ep-edit-start-time').val();
        var endTime = row.find('.ep-edit-end-time').val();
        var imageInput = row.find('.ep-edit-image')[0];
        var videoInput = row.find('.ep-edit-video')[0];
        var hasImage = imageInput && imageInput.files && imageInput.files.length > 0;
        var hasVideo = videoInput && videoInput.files && videoInput.files.length > 0;
        if (startTime && endTime && startTime >= endTime) {
            if (typeof showAdminAlert === 'function') showAdminAlert('error', 'Start time must be before end time.');
            return;
        }
        var table = $('#event-participants-table');
        var eventStart = table.data('event-start');
        var eventEnd = table.data('event-end');
        if (startTime && eventStart && startTime < eventStart) {
            if (typeof showAdminAlert === 'function') showAdminAlert('error', 'Participant start time cannot be before the event start time.');
            return;
        }
        if (endTime && eventEnd && endTime > eventEnd) {
            if (typeof showAdminAlert === 'function') showAdminAlert('error', 'Participant end time cannot be after the event end time.');
            return;
        }
        var duplicateParticipant = false;
        $('#event-participants-table tbody tr.ep-view-row').each(function() {
            if (this === row[0]) return true;
            var otherId = $(this).hasClass('ep-row-editing') ? $(this).find('.ep-edit-participant-id').val() : $(this).data('participant-id');
            if (otherId && String(otherId) === String(participantId)) {
                duplicateParticipant = true;
                return false;
            }
        });
        if (duplicateParticipant) {
            if (typeof showAdminAlert === 'function') showAdminAlert('error', 'This participant is already added to the event.');
            return;
        }
        var btn = $(this);
        var url = isNew
            ? (baseUrl + '/acl/events/' + ($('#event-participants-table').data('event-token')) + '/participants')
            : (baseUrl + '/acl/events/participant/' + epId);
        btn.prop('disabled', true);
        var sendData;
        if (hasImage || hasVideo) {
            sendData = new FormData();
            sendData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            sendData.append('participant_id', participantId);
            sendData.append('topic', topic || '');
            sendData.append('description', description || '');
            sendData.append('start_time', startTime || '');
            sendData.append('end_time', endTime || '');
            if (hasImage) sendData.append('image', imageInput.files[0]);
            if (hasVideo) sendData.append('video', videoInput.files[0]);
        } else {
            sendData = { _token: $('meta[name="csrf-token"]').attr('content'), participant_id: participantId, topic: topic, description: description, start_time: startTime, end_time: endTime };
        }
        var ajaxOptsSave = { url: url, type: 'POST', headers: ajaxHeaders, xhrFields: { withCredentials: true } };
        if (hasImage || hasVideo) {
            ajaxOptsSave.data = sendData;
            ajaxOptsSave.processData = false;
            ajaxOptsSave.contentType = false;
        } else {
            ajaxOptsSave.data = sendData;
        }
        $.ajax(ajaxOptsSave)
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.participant) {
                    var p = d.participant;
                    if (isNew && d.ep_id) row.data('ep-id', d.ep_id);
                    row.find('.ep-cell-participant').html(buildDisplayParticipant(p));
                    row.find('.ep-cell-image').html(buildDisplayImage(p.ep_image, p.ep_video));
                    row.find('.ep-cell-topic').html((p.topic && p.topic.trim() !== '') ? (p.topic || '').replace(/</g, '&lt;') : '—');
                    row.find('.ep-cell-description').html(truncateDesc(p.description, 40));
                    row.find('.ep-cell-time').html(((p.start_time || '—') + ' – ' + (p.end_time || '—')).replace(/– –/g, '— –').replace(/– $/, '—'));
                    row.find('.ep-cell-actions').html('<button type="button" class="btn btn-sm btn-light-primary btn-ep-inline-edit me-1">Edit</button><button type="button" class="btn btn-sm btn-light-danger btn-ep-delete" title="Remove participant">Delete</button>');
                    if (isNew && d.ep_id) {
                        row.find('.ep-cell-docs').html(buildDocsCellHtml(d.ep_id, []));
                    }
                    row.data('participant-id', p.id);
                    row.data('ep-image', p.ep_image || '');
                    row.data('ep-video', p.ep_video || '');
                    row.data('description', p.description || '');
                    row.data('topic', p.topic || '');
                    row.data('start-time', p.start_time || '');
                    row.data('end-time', p.end_time || '');
                    row.removeClass('ep-row-editing ep-row-new');
                    if (typeof showAdminAlert === 'function') showAdminAlert('success', d.msg);
                } else {
                    if (typeof showAdminAlert === 'function') showAdminAlert('error', (d && d.msg) ? d.msg : 'Error');
                }
            })
            .fail(function(xhr) {
                var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                if (typeof showAdminAlert === 'function') showAdminAlert('error', (d && d.msg) ? d.msg : 'Request failed');
            })
            .always(function() { btn.prop('disabled', false); });
    });
    $(document).on('click', '.btn-ep-add-participant', function() {
        var table = $('#event-participants-table');
        var tbody = table.find('tbody');
        tbody.find('.ep-empty-placeholder').remove();
        var firstId = (eventViewParticipants && eventViewParticipants[0]) ? eventViewParticipants[0].id : '';
        var newRow = '<tr class="ep-view-row ep-row-new ep-row-editing" data-ep-id="0" data-participant-id="" data-ep-image="" data-ep-video="" data-description="" data-topic="" data-start-time="" data-end-time="">' +
            '<td class="ep-cell-participant">' + buildParticipantSelect(firstId) + '</td>' +
            '<td class="ep-cell-image"><label class="small text-muted d-block">Image</label><input type="file" class="form-control form-control-sm ep-edit-image mb-1" accept=".jpg,.jpeg,.png"><label class="small text-muted d-block">Video</label><input type="file" class="form-control form-control-sm ep-edit-video" accept=".mp4,.webm,.mov,video/mp4,video/webm,video/quicktime"></td>' +
            '<td class="ep-cell-topic"><input type="text" class="form-control form-control-sm ep-edit-topic" value="" placeholder="Topic"></td>' +
            '<td class="ep-cell-description"><textarea class="form-control form-control-sm ep-edit-description" rows="2" placeholder="Description"></textarea></td>' +
            '<td class="ep-cell-time"><input type="time" class="form-control form-control-sm d-inline-block ep-edit-start-time" style="width:auto" value=""> – <input type="time" class="form-control form-control-sm d-inline-block ep-edit-end-time" style="width:auto" value=""></td>' +
            '<td class="ep-cell-docs">—</td>' +
            '<td class="ep-cell-actions text-end"><button type="button" class="btn btn-sm btn-success btn-ep-inline-save me-1">Save</button><button type="button" class="btn btn-sm btn-light btn-ep-inline-cancel">Cancel</button></td>' +
            '</tr>';
        tbody.append(newRow);
    });

    // Delete participant from event (SweetAlert confirm)
    $(document).on('click', '.btn-ep-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var row = $(this).closest('tr.ep-view-row');
        var epId = row.data('ep-id');
        if (!epId || row.hasClass('ep-row-new')) return;
        Swal.fire({
            title: 'Remove participant?',
            text: 'This participant will be removed from the event. This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: baseUrl + '/acl/events/participant/' + epId,
                type: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                headers: ajaxHeaders,
                xhrFields: { withCredentials: true }
            })
                .done(function(res) {
                    var d = parseJsonRes(res);
                    if (d && d.responseCode === 1) {
                        row.remove();
                        var tbody = $('#event-participants-table tbody');
                        if (tbody.find('tr.ep-view-row').length === 0) {
                            var colCount = $('#event-participants-table thead th').length;
                            tbody.append('<tr class="ep-empty-placeholder"><td colspan="' + colCount + '" class="text-center text-muted py-5">No participants yet.</td></tr>');
                        }
                        if (typeof showAdminAlert === 'function') showAdminAlert('success', d.msg);
                    } else {
                        if (typeof showAdminAlert === 'function') showAdminAlert('error', (d && d.msg) ? d.msg : 'Error');
                    }
                })
                .fail(function(xhr) {
                    var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                    if (typeof showAdminAlert === 'function') showAdminAlert('error', (d && d.msg) ? d.msg : 'Request failed');
                });
        });
    });

    // Add document form
    $(document).on('click', '.btn-ep-doc-add', function() {
        var cell = $(this).closest('td.ep-cell-docs');
        var wrap = cell.find('.ep-doc-add-wrap');
        var epId = cell.closest('tr.ep-view-row').data('ep-id');
        if (!epId) return;
        var formHtml = '<div class="ep-doc-form border rounded p-2 bg-light mb-1">' +
            '<input type="text" class="form-control form-control-sm ep-doc-title mb-1" placeholder="Document title">' +
            '<input type="file" class="form-control form-control-sm ep-doc-file mb-1" accept=".pdf">' +
            '<button type="button" class="btn btn-sm btn-success btn-ep-doc-save me-1">Save</button><button type="button" class="btn btn-sm btn-light btn-ep-doc-form-cancel">Cancel</button>' +
            '</div>';
        wrap.html(formHtml);
    });
    $(document).on('click', '.btn-ep-doc-form-cancel', function() {
        var wrap = $(this).closest('.ep-doc-add-wrap');
        wrap.html('<button type="button" class="btn btn-sm btn-light-success btn-ep-doc-add">Add document</button>');
    });
    $(document).on('click', '.btn-ep-doc-save', function() {
        var cell = $(this).closest('td.ep-cell-docs');
        var row = cell.closest('tr.ep-view-row');
        var epId = row.data('ep-id');
        var wrap = cell.find('.ep-doc-add-wrap');
        var title = cell.find('.ep-doc-title').val();
        var fileInput = cell.find('.ep-doc-file')[0];
        if (!fileInput || !fileInput.files || !fileInput.files.length) {
            if (typeof showAdminAlert === 'function') showAdminAlert('error', 'Please select a PDF file.');
            return;
        }
        var formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('title', title || 'Presentation');
        formData.append('file', fileInput.files[0]);
        var btn = $(this);
        btn.prop('disabled', true);
        $.ajax({
            url: baseUrl + '/acl/events/participant/' + epId + '/documents',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            xhrFields: { withCredentials: true }
        })
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.document) {
                    var doc = d.document;
                    var list = cell.find('.ep-docs-list');
                    var itemHtml = '<div class="ep-doc-item d-flex align-items-center gap-1 mb-1" data-doc-id="' + doc.id + '"><a href="/' + (doc.file_path || '').replace(/^\/+/, '') + '" target="_blank" rel="noopener" class="text-primary small ep-doc-link">' + escapeHtml(doc.title) + '</a><button type="button" class="btn btn-sm btn-icon btn-light-warning btn-ep-doc-edit" title="Edit title"><i class="ki-duotone ki-pencil fs-6"><span class="path1"></span><span class="path2"></span></i></button><button type="button" class="btn btn-sm btn-icon btn-light-danger btn-ep-doc-remove" title="Remove"><i class="ki-duotone ki-trash fs-6"><span class="path1"></span><span class="path2"></span></i></button></div>';
                    list.append(itemHtml);
                    cell.find('.ep-docs-empty').hide();
                    wrap.html('<button type="button" class="btn btn-sm btn-light-success btn-ep-doc-add">Add document</button>');
                    if (typeof showAdminAlert === 'function') showAdminAlert('success', d.msg);
                } else {
                    if (typeof showAdminAlert === 'function') showAdminAlert('error', (d && d.msg) ? d.msg : 'Error');
                }
            })
            .fail(function(xhr) {
                var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                if (typeof showAdminAlert === 'function') showAdminAlert('error', (d && d.msg) ? d.msg : 'Request failed');
            })
            .always(function() { btn.prop('disabled', false); });
    });

    // Edit document (inline title)
    $(document).on('click', '.btn-ep-doc-edit', function() {
        var item = $(this).closest('.ep-doc-item');
        if (item.hasClass('ep-doc-editing')) return;
        var docId = item.data('doc-id');
        var currentTitle = item.find('.ep-doc-link').text();
        item.data('original-html', item.html());
        item.addClass('ep-doc-editing').html('<input type="text" class="form-control form-control-sm ep-doc-edit-title d-inline-block" style="width:140px" value="' + escapeHtml(currentTitle) + '"> <button type="button" class="btn btn-sm btn-success btn-ep-doc-edit-save">Save</button> <button type="button" class="btn btn-sm btn-light btn-ep-doc-edit-cancel">Cancel</button>');
    });
    $(document).on('click', '.btn-ep-doc-edit-cancel', function() {
        var item = $(this).closest('.ep-doc-item');
        item.removeClass('ep-doc-editing').html(item.data('original-html'));
    });
    $(document).on('click', '.btn-ep-doc-edit-save', function() {
        var item = $(this).closest('.ep-doc-item');
        var docId = item.data('doc-id');
        var title = item.find('.ep-doc-edit-title').val();
        var btn = $(this);
        btn.prop('disabled', true);
        $.ajax({
            url: baseUrl + '/acl/events/participant-document/' + docId,
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), title: title },
            headers: ajaxHeaders,
            xhrFields: { withCredentials: true }
        })
            .done(function(res) {
                var d = parseJsonRes(res);
                if (d && d.responseCode === 1 && d.document) {
                    var doc = d.document;
                    var path = (doc.file_path || '').replace(/^\/+/, '');
                    var linkHtml = '<a href="/' + path + '" target="_blank" rel="noopener" class="text-primary small ep-doc-link">' + escapeHtml(doc.title) + '</a><button type="button" class="btn btn-sm btn-icon btn-light-warning btn-ep-doc-edit" title="Edit title"><i class="ki-duotone ki-pencil fs-6"><span class="path1"></span><span class="path2"></span></i></button><button type="button" class="btn btn-sm btn-icon btn-light-danger btn-ep-doc-remove" title="Remove"><i class="ki-duotone ki-trash fs-6"><span class="path1"></span><span class="path2"></span></i></button>';
                    item.removeClass('ep-doc-editing').html(linkHtml);
                    if (typeof showAdminAlert === 'function') showAdminAlert('success', d.msg);
                } else {
                    if (typeof showAdminAlert === 'function') showAdminAlert('error', (d && d.msg) ? d.msg : 'Error');
                }
            })
            .fail(function(xhr) {
                var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                if (typeof showAdminAlert === 'function') showAdminAlert('error', (d && d.msg) ? d.msg : 'Request failed');
            })
            .always(function() { btn.prop('disabled', false); });
    });

    // Remove document (SweetAlert confirm)
    $(document).on('click', '.btn-ep-doc-remove', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var item = $(this).closest('.ep-doc-item');
        var docId = item.data('doc-id');
        Swal.fire({
            title: 'Remove document?',
            text: 'This document will be removed from the participant. This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: baseUrl + '/acl/events/participant-document/' + docId,
                type: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                headers: ajaxHeaders,
                xhrFields: { withCredentials: true }
            })
                .done(function(res) {
                    var d = parseJsonRes(res);
                    if (d && d.responseCode === 1) {
                        item.remove();
                        var cell = item.closest('td.ep-cell-docs');
                        if (cell.find('.ep-doc-item').length === 0) {
                            cell.find('.ep-docs-empty').show();
                        }
                        if (typeof showAdminAlert === 'function') showAdminAlert('success', d.msg);
                    } else {
                        if (typeof showAdminAlert === 'function') showAdminAlert('error', (d && d.msg) ? d.msg : 'Error');
                    }
                })
                .fail(function(xhr) {
                    var d = xhr.responseText ? parseJsonRes(xhr.responseText) : null;
                    if (typeof showAdminAlert === 'function') showAdminAlert('error', (d && d.msg) ? d.msg : 'Request failed');
                });
        });
    });
});
</script>
@stop
