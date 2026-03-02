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
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-fluid overflow-hidden">
    <div class="content flex-row-fluid w-100 min-w-0" id="kt_content">
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10 w-100 min-w-0">
            <div class="col-md-6 col-lg-4">
                <div class="card card-flush mb-5 mb-xl-10 dashboard-stat-card">
                    <div class="card-header pt-5 pb-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ \App\Models\Acl\ModuleCategoryModel::count() }}</span>
                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Module Categories</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-flush mb-5 mb-xl-10 dashboard-stat-card">
                    <div class="card-header pt-5 pb-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ \App\Models\Acl\ModuleModel::count() }}</span>
                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Modules</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-flush mb-5 mb-xl-10 dashboard-stat-card">
                    <div class="card-header pt-5 pb-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ \App\Models\Acl\RoleModel::count() }}</span>
                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Roles</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-flush">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Welcome</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Use the menu above to manage ACL (Module Categories & Modules).</span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <p class="text-gray-700">Dashboard overview. Navigate via the header to Module Categories or Modules.</p>
                    </div>
                </div>
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
@stop
