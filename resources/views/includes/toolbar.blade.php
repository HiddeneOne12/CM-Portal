<!--begin::Toolbar-->
<div class="toolbar py-3 py-lg-6" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack flex-wrap gap-2">
        <div class="page-title d-flex flex-column align-items-start me-3 py-2 py-lg-0 gap-2">
            <h1 class="d-flex text-gray-900 fw-bold m-0 fs-3">{{ $pageTitle ?? '' }}</h1>
            <ul class="breadcrumb breadcrumb-dot fw-semibold text-gray-600 fs-7">
                <li class="breadcrumb-item text-gray-600">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item text-gray-600">{{ $pageTitle ?? '' }}</li>
                @if(!empty($subTitle))
                <li class="breadcrumb-item text-gray-500">{{ $subTitle }}</li>
                @endif
            </ul>
        </div>
        @stack('toolbar_actions')
    </div>
</div>
<!--end::Toolbar-->
