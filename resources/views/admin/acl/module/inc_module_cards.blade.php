@if (isset($result) && $result->count())
@foreach ($result as $row)
<div class="col-md-4">
    <div class="card card-flush h-md-100">
        <div class="card-header">
            <div class="card-title"><h2>{{ $row->module_name }}</h2></div>
        </div>
        <div class="card-body pt-1">
            <div class="fw-bolder text-gray-600 mb-5">Category: {{ $row->category->category_name ?? '-' }}</div>
            <div class="fw-bolder text-gray-600 mb-5">Slug: {{ $row->route }}</div>
            <div class="d-flex flex-column text-gray-600">
                <div class="d-flex align-items-center py-2"><strong>Assigned To:</strong></div>
                @if ($row->roles && $row->roles->count())
                @foreach ($row->roles->take(4) as $roles)
                <div class="d-flex align-items-center py-2">
                    <span class="bullet bg-primary me-3"></span>{{ $roles->role->role_name ?? '' }}
                </div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="card-footer flex-wrap pt-0">
            <a href="javascript:void(0)" role="button" data-id="{{ $row->getKey() }}" data-token="{{ encryptIdForUrl($row->getKey()) }}" class="btn btn-light btn-active-light-primary btn-edit my-1">Edit Module</a>
            <a href="{{ route('admin.acl.module.delete', encryptIdForUrl($row->getKey())) }}" class="btn btn-light btn-active-light-primary my-1 btn-del" data-confirm-delete="Delete this module?">Delete Module</a>
        </div>
    </div>
</div>
@endforeach
@endif
