@php
    $adminNotifications = [];
    $resultLmCat = \App\Models\Acl\ModuleCategoryModel::orderBy('display_order')->get();
@endphp
<div id="kt_header" class="header">
    <div class="header-top align-items-stretch flex-grow-1">
        <div class="d-flex align-items-stretch container-fluid">
            <div class="d-flex align-items-center align-items-lg-stretch me-5 flex-row-fluid">
                <button class="d-lg-none btn btn-icon btn-color-white bg-hover-white bg-hover-opacity-10 w-35px h-35px h-md-40px w-md-40px ms-n3 me-2" id="kt_header_navs_toggle">
                    <i class="ki-duotone ki-abstract-14 fs-2"><span class="path1"></span><span class="path2"></span></i>
                </button>
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center">
                    <img alt="Logo" src="{{ asset('frontend/assets/images/logo-1.svg') }}" class="h-25px h-lg-30px admin-header-logo" style="height: 2rem;" />
                </a>
                <div class="align-self-end" id="kt_brand_tabs">
                    <div class="header-tabs mx-4 ms-lg-10 mb-5 mb-lg-0" id="kt_header_tabs">
                        <ul class="nav flex-nowrap text-nowrap">
                            @if ($resultLmCat)
                                @foreach ($resultLmCat as $rowLmCat)
                                    @php
                                        $catId = $rowLmCat->getKey();
                                        $resultLmModule = $catId !== null ? \App\Models\Acl\RolePrivilegeModel::drawLeftMenu((int) $catId) : collect();
                                    @endphp
                                    @if ($catId !== null && !$resultLmModule->isEmpty())
                                        @if ($rowLmCat->category_name == 'Dashboard')
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                                            </li>
                                        @elseif ($resultLmModule->count() == 1)
                                            @php
                                                $singleModule = $resultLmModule->first();
                                                $navUrl = str_starts_with($singleModule->route ?? '', 'admin/') ? 'cmcontrol/' . substr($singleModule->route, 6) : ($singleModule->route ?? 'cmcontrol');
                                                $isActiveSingle = Request::fullUrl() === url($navUrl);
                                            @endphp
                                            <li class="nav-item">
                                                <a class="nav-link {{ $isActiveSingle ? 'active' : '' }}" href="{{ url($navUrl) }}">{{ $rowLmCat->category_name }}</a>
                                            </li>
                                        @else
                                            @php $isDropdownActive = false; @endphp
                                            <li class="nav-item dropdown">
                                                <a href="javascript:void(0)" class="nav-link dropdown-toggle {{ $isDropdownActive ? 'active' : '' }}" id="dropdown-{{ $rowLmCat->getKey() }}" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" role="button">{{ $rowLmCat->category_name }}</a>
                                                <ul class="dropdown-menu" aria-labelledby="dropdown-{{ $rowLmCat->getKey() }}">
                                                    @foreach ($resultLmModule as $module)
                                                        @php
                                                            $modUrl = str_starts_with($module->route ?? '', 'admin/') ? 'cmcontrol/' . substr($module->route, 6) : ($module->route ?? 'cmcontrol');
                                                            $isItemActive = Request::fullUrl() === url($modUrl);
                                                            if ($isItemActive) { $isDropdownActive = true; }
                                                        @endphp
                                                        <li>
                                                            <a class="dropdown-item {{ $isItemActive ? 'active' : '' }}" href="{{ url($modUrl) }}">{{ $module->module_name }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center flex-row-auto">
                {{-- User menu (CM portal logic: user_name, logout route) --}}
                <div class="d-flex align-items-center ms-1" id="kt_header_user_menu_toggle">
                    <div class="btn btn-flex align-items-center bg-hover-white bg-hover-opacity-10 py-2 ps-2 pe-2 me-n2" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <div class="d-none d-md-flex flex-column align-items-end justify-content-center me-2 me-md-4">
                            <span class="text-white fs-8 fw-bold lh-1">{{ Auth::guard('admin')->user()->user_name }}</span>
                        </div>
                        <div class="symbol symbol-30px symbol-md-40px">
                            <img src="{{ photo(Auth::guard('admin')->user()->user_name) }}" alt="user" />
                        </div>
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="{{ photo(Auth::guard('admin')->user()->user_name) }}" />
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">{{ Auth::guard('admin')->user()->user_name }}</div>
                                    <span class="fw-semibold text-muted fs-7">Admin</span>
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="{{ route('admin.logout') }}" class="menu-link px-5">Sign Out</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
