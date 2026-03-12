@php $isEdit = isset($admin) && $admin; @endphp
<form method="POST" action="{{ $isEdit ? route('admin.acl.admin-users.edit', encryptIdForUrl($admin->id)) : route('admin.acl.admin-users.store') }}" class="admin-user-drawer-form">
    @csrf
    @if($isEdit)
    <input type="hidden" name="admin_user_token" value="{{ encryptIdForUrl($admin->id) }}">
    @endif
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Username</label>
        <input type="text" name="user_name" class="form-control form-control-solid" placeholder="e.g. john.doe"
               value="{{ old('user_name', $admin->user_name ?? '') }}" required autocomplete="off" />
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">{{ $isEdit ? 'New Password' : 'Password' }}</label>
        <input type="password" name="password" class="form-control form-control-solid"
               placeholder="{{ $isEdit ? 'Leave blank to keep current' : 'Min. 8 characters' }}"
               {{ $isEdit ? '' : 'required' }} autocomplete="new-password" />
    </div>
    @if(!$isEdit)
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control form-control-solid" placeholder="Repeat password" required autocomplete="new-password" />
    </div>
    @endif
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2 d-block">Status</label>
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $admin->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label text-gray-600">Active</label>
        </div>
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Assign Role</label>
        @if(($roles ?? collect())->isEmpty())
            <p class="text-muted fs-7">No roles available.</p>
        @else
            <div class="d-flex flex-wrap gap-2">
                @php
                    $roleRelation = ($isEdit && isset($admin)) ? ($admin->userRoles ?? collect()) : collect();
                    $currentRoleIds = $roleRelation instanceof \Illuminate\Support\Collection ? $roleRelation->pluck('role_ID')->toArray() : [];
                    $selected = (array) old('roles', $isEdit ? $currentRoleIds : []);
                    $selected = count($selected) ? [array_values($selected)[0]] : [];
                @endphp
                @foreach($roles as $r)
                <label class="form-check form-check-custom form-check-solid form-check-inline">
                    <input class="form-check-input" type="radio" name="roles[]" value="{{ $r->getKey() }}" {{ in_array($r->getKey(), $selected) ? 'checked' : '' }}>
                    <span class="form-check-label text-gray-700">{{ $r->role_name }}</span>
                </label>
                @endforeach
            </div>
        @endif
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Save' }}</button>
    </div>
</form>
