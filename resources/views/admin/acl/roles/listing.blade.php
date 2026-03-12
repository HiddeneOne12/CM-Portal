@extends('layouts.admin')
@push('title')
Roles - {{ config('global.SITE_NAME') }}
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
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-12">

                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center mb-5">
                        <i class="ki-duotone ki-shield-tick fs-2 me-3 text-success"><span class="path1"></span><span class="path2"></span></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger d-flex align-items-center mb-5">
                        <i class="ki-duotone ki-shield-cross fs-2 me-3 text-danger"><span class="path1"></span><span class="path2"></span></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                <div class="card card-flush shadow-sm">
                    <div class="card-header pt-6 pb-4 border-bottom">
                        <div class="card-title d-flex align-items-center gap-3">
                            <span class="bg-primary bg-opacity-10 rounded p-2">
                                <i class="ki-duotone ki-shield fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                            </span>
                            <div>
                                <h3 class="card-label fw-bold text-gray-900 mb-0">Roles</h3>
                                <span class="text-muted fs-7">{{ $roles->total() }} total roles</span>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            @if(validatePermissions('admin/acl/roles/add'))
                            <a href="{{ route('admin.acl.roles.add') }}" class="btn btn-primary btn-sm">
                                <i class="ki-duotone ki-plus fs-4 me-1"></i> Add Role
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body pt-4 px-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-4">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0 px-8">
                                    <th class="ps-8 min-w-200px">Name</th>
                                    <th class="min-w-100px text-center">Display Order</th>
                                    <th class="min-w-120px text-end pe-8">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                                @forelse($roles as $role)
                                <tr>
                                    <td class="ps-8">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="symbol symbol-35px symbol-circle">
                                                <span class="symbol-label bg-light-primary text-primary fw-bold fs-6">
                                                    {{ strtoupper(substr($role->role_name, 0, 1)) }}
                                                </span>
                                            </span>
                                            <div>
                                                <span class="text-gray-800 fw-bold d-block">{{ $role->role_name }}</span>
                                                @if($role->getKey() == 1)
                                                    <span class="badge badge-light-success fw-semibold fs-8">Super Admin</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light fw-bold">{{ $role->display_order }}</span>
                                    </td>
                                    <td class="text-end pe-8">
                                        @if(validatePermissions('admin/acl/roles/edit'))
                                        <a href="{{ route('admin.acl.roles.edit', encryptIdForUrl($role->getKey())) }}"
                                           class="btn btn-sm btn-icon btn-light-primary me-1" title="Edit">
                                            <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                                        </a>
                                        @endif
                                        @if($role->getKey() != 1)
                                            @if(validatePermissions('admin/acl/roles/delete'))
                                            <form action="{{ route('admin.acl.roles.delete', encryptIdForUrl($role->getKey())) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete role {{ $role->role_name }}?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Delete">
                                                    <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                                </button>
                                            </form>
                                            @endif
                                        @else
                                        <span class="btn btn-sm btn-icon btn-light disabled" title="Cannot delete Super Admin">
                                            <i class="ki-duotone ki-trash fs-4 opacity-25"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-10">
                                        <div class="text-muted">
                                            <i class="ki-duotone ki-shield fs-3x mb-3 d-block opacity-25"><span class="path1"></span><span class="path2"></span></i>
                                            No roles found.
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if($roles->hasPages())
                        <div class="d-flex justify-content-end px-8 pt-4 border-top">
                            {{ $roles->links() }}
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@stop