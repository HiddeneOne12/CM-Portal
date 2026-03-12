@extends('layouts.admin')
@push('title')
Admin Users - {{ config('global.SITE_NAME') }}
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

                {{-- Flash Messages --}}
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
                                <i class="ki-duotone ki-people fs-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </span>
                            <div>
                                <h3 class="card-label fw-bold text-gray-900 mb-0">Admin Users</h3>
                                <span class="text-muted fs-7">{{ $admins->total() }} total users</span>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            @if(validatePermissions('admin/acl/admin-users/add'))
                            <a href="{{ route('admin.acl.admin-users.add') }}" class="btn btn-primary btn-sm">
                                <i class="ki-duotone ki-plus fs-4 me-1"></i> Add Admin User
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body pt-4 px-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-4 px-8" id="admin-users-table">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0 px-8">
                                    <th class="ps-8 min-w-150px">Username</th>
                                    <th class="min-w-200px">Roles</th>
                                    <th class="min-w-80px text-center">Status</th>
                                    <th class="min-w-120px text-end pe-8">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                                @forelse($admins as $admin)
                                <tr>
                                    <td class="ps-8">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="symbol symbol-35px symbol-circle">
                                                <span class="symbol-label bg-light-primary text-primary fw-bold fs-6">
                                                    {{ strtoupper(substr($admin->user_name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <span class="text-gray-800 fw-bold">{{ $admin->user_name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $roleNames = $admin->userRoles->pluck('role.role_name')->filter();
                                        @endphp
                                        @if($roleNames->isEmpty())
                                            <span class="text-muted fst-italic">No roles assigned</span>
                                        @else
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($roleNames as $roleName)
                                                    <span class="badge badge-light-primary fw-semibold fs-8">{{ $roleName }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($admin->is_active)
                                            <span class="badge badge-light-success fw-bold">Active</span>
                                        @else
                                            <span class="badge badge-light-danger fw-bold">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-8">
                                        @if(validatePermissions('admin/acl/admin-users/edit'))
                                        <a href="{{ route('admin.acl.admin-users.edit', encryptIdForUrl($admin->id)) }}"
                                           class="btn btn-sm btn-icon btn-light-primary me-1" title="Edit">
                                            <i class="ki-duotone ki-pencil fs-4"><span class="path1"></span><span class="path2"></span></i>
                                        </a>
                                        @endif
                                        @if(auth('admin')->id() !== $admin->id)
                                            @if(validatePermissions('admin/acl/admin-users/delete'))
                                            <form action="{{ route('admin.acl.admin-users.delete', encryptIdForUrl($admin->id)) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete {{ $admin->user_name }}?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Delete">
                                                    <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                                </button>
                                            </form>
                                            @endif
                                        @else
                                        <span class="btn btn-sm btn-icon btn-light disabled" title="Cannot delete your own account">
                                            <i class="ki-duotone ki-trash fs-4 opacity-25"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10">
                                        <div class="text-muted">
                                            <i class="ki-duotone ki-people fs-3x mb-3 d-block opacity-25"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                            No admin users found.
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if($admins->hasPages())
                        <div class="d-flex justify-content-end px-8 pt-4 border-top">
                            {{ $admins->links() }}
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@stop