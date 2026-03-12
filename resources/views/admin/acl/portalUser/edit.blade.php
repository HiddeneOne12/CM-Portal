<form method="post" action="{{ url('/cmcontrol/acl/portal-users/edit/' . $row->id) }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="eid" value="{{ $row->id }}">
    <input type="hidden" name="portal_user_token" value="{{ encryptIdForUrl($row->id) }}">
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Image</label>
        @if($row->image)
        <div class="mb-2">
            <img src="{{ asset($row->image) }}" alt="" class="rounded" style="max-height: 60px; max-width: 60px; object-fit: cover;">
            <span class="text-muted ms-2">Current image (upload new to replace)</span>
        </div>
        @endif
        <input type="file" name="image" class="form-control form-control-solid" accept=".jpg,.jpeg,.png" />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">First Name</label>
        <input type="text" name="first_name" class="form-control form-control-solid" value="{{ old('first_name', $row->first_name) }}" required />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Last Name</label>
        <input type="text" name="last_name" class="form-control form-control-solid" value="{{ old('last_name', $row->last_name) }}" required />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Email</label>
        <input type="email" name="email" class="form-control form-control-solid" value="{{ old('email', $row->email) }}" required />
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Phone Number</label>
        <input type="text" name="phone_number" class="form-control form-control-solid" value="{{ old('phone_number', $row->phone_number) }}" />
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Gender</label>
        <select name="gender" class="form-select form-select-solid">
            <option value="">Select</option>
            <option value="male" {{ old('gender', $row->gender) == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', $row->gender) == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('gender', $row->gender) == 'other' ? 'selected' : '' }}>Other</option>
        </select>
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
