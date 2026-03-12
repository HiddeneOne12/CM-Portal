<form method="post" action="{{ url('/cmcontrol/acl/participants/edit/' . $row->id) }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="eid" value="{{ $row->id }}">
    <input type="hidden" name="participant_token" value="{{ encryptIdForUrl($row->id) }}">
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Image</label>
        @if($row->image)
        <div class="mb-2"><img src="{{ asset($row->image) }}" alt="" class="rounded" style="max-height: 60px;"></div>
        <span class="text-muted fs-7">Leave empty to keep current image.</span>
        @else
        <span class="text-muted fs-7">Image is required.</span>
        @endif
        <input type="file" name="image" class="form-control form-control-solid" accept=".jpg,.jpeg,.png" @if(!$row->image) required @endif />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Name</label>
        <input type="text" name="name" class="form-control form-control-solid" placeholder="Full name" value="{{ old('name', $row->name) }}" required />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Position</label>
        <input type="text" name="position" class="form-control form-control-solid" placeholder="Job title / position" value="{{ old('position', $row->position) }}" required />
    </div>
    <div class="fv-row mb-5">
        <label class="required fs-6 fw-semibold mb-2">Company</label>
        <select name="company_id" class="form-select form-select-solid" required>
            <option value="">— Select company —</option>
            @foreach($companies ?? [] as $c)
            <option value="{{ $c->id }}" {{ old('company_id', $row->company_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="d-flex flex-stack justify-content-end gap-2">
        <button type="button" class="btn btn-light drawer-close">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
