<form method="post" action="{{ url('/cmcontrol/acl/portal-users/add') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="eid" value="">
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Image</label>
        <input type="file" name="image" class="form-control form-control-solid" accept=".jpg,.jpeg,.png" />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">First Name</label>
        <input type="text" name="first_name" class="form-control form-control-solid" placeholder="First Name" required />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Last Name</label>
        <input type="text" name="last_name" class="form-control form-control-solid" placeholder="Last Name" required />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Email</label>
        <input type="email" name="email" class="form-control form-control-solid" placeholder="E-mail" required />
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Phone Number</label>
        <input type="text" name="phone_number" class="form-control form-control-solid" placeholder="Phone Number" />
    </div>
    <div class="fv-row mb-5">
        <label class="fs-6 fw-semibold mb-2">Gender</label>
        <select name="gender" class="form-select form-select-solid">
            <option value="">Select</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
