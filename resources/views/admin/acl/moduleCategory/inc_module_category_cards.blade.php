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
                @foreach ($row->modules->take(4) as $modules)
                <div class="d-flex align-items-center py-2">
                    <span class="bullet bg-primary me-3"></span>{{ $modules->module_name }}
                </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer flex-wrap pt-0">
            <a href="javascript:void(0)"><button type="button" data-id="{{ $row->getKey() }}" data-token="{{ encryptIdForUrl($row->getKey()) }}" class="btn btn-light btn-active-light-primary btn-edit my-1">Edit</button></a>
            <a href="{{ route('admin.acl.module-category.delete', encryptIdForUrl($row->getKey())) }}" class="btn btn-light btn-active-light-primary my-1" data-confirm-delete="Delete this category?">Delete</a>
        </div>
    </div>
</div>
@endforeach
@endif
