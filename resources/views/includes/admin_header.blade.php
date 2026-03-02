@php
  $resultLmCat = \App\Models\Acl\ModuleCategoryModel::orderBy('display_order')->get();
@endphp
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
      <img src="{{ asset('frontend/assets/images/logo-1.svg') }}" alt="Logo" height="32">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav me-auto">
        @foreach ($resultLmCat as $rowLmCat)
          @php
            $resultLmModule = \App\Models\Acl\RolePrivilegeModel::drawLeftMenu($rowLmCat->id);
          @endphp
          @if (!$resultLmModule->isEmpty())
            @if ($rowLmCat->category_name == 'Dashboard')
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
              </li>
            @elseif ($resultLmModule->count() == 1)
              @php $singleModule = $resultLmModule->first(); @endphp
              <li class="nav-item">
                <a class="nav-link" href="{{ url($singleModule->route) }}">{{ $rowLmCat->category_name }}</a>
              </li>
            @else
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ $rowLmCat->category_name }}</a>
                <ul class="dropdown-menu">
                  @foreach ($resultLmModule as $module)
                    <li><a class="dropdown-item" href="{{ url($module->route) }}">{{ $module->module_name }}</a></li>
                  @endforeach
                </ul>
              </li>
            @endif
          @endif
        @endforeach
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <span class="navbar-text text-white me-3">{{ Auth::guard('admin')->user()->user_name }}</span>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="{{ route('admin.logout') }}">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
