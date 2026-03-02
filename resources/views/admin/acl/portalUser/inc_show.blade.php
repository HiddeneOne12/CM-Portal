<div class="mb-3">
    @if($row->image)
    <img src="{{ asset($row->image) }}" alt="" class="rounded mb-2" style="max-height: 80px; max-width: 80px; object-fit: cover;">
    @else
    <span class="text-muted">No image</span>
    @endif
</div>
<div class="fw-bold text-gray-600 mb-2">First Name</div>
<div class="mb-4">{{ $row->first_name }}</div>
<div class="fw-bold text-gray-600 mb-2">Last Name</div>
<div class="mb-4">{{ $row->last_name }}</div>
<div class="fw-bold text-gray-600 mb-2">Email</div>
<div class="mb-4">{{ $row->email }}</div>
<div class="fw-bold text-gray-600 mb-2">Phone Number</div>
<div class="mb-4">{{ $row->phone_number ?? '—' }}</div>
<div class="fw-bold text-gray-600 mb-2">Gender</div>
<div class="mb-4">{{ $row->gender ? ucfirst($row->gender) : '—' }}</div>
<div class="d-flex flex-stack justify-content-end">
    <button type="button" class="btn btn-light drawer-close">Close</button>
</div>
