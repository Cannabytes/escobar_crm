<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="container-fluid">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 d-xl-none">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="icon-base ti tabler-menu-2"></i>
      </a>
    </div>

    <div class="navbar-nav align-items-center flex-grow-1">
      <div class="nav-item navbar-search w-100">
        <h4 class="mb-0 fw-semibold text-body">{{ __('Администрирование компаний') }}</h4>
      </div>
    </div>

    @php
      $user = auth()->user();
      $roleLabels = [
        \App\Models\User::ROLE_SUPER_ADMIN => __('Супер админ'),
        \App\Models\User::ROLE_MODERATOR => __('Модератор'),
        \App\Models\User::ROLE_VIEWER => __('Пользователь'),
      ];
    @endphp

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      @if ($user)
        <li class="nav-item lh-1 me-3">
          <span class="badge bg-label-primary rounded-pill text-uppercase">
            {{ $roleLabels[$user->role] ?? __('Пользователь') }}
          </span>
        </li>
      @endif
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar {{ $user->isOnline() ? 'avatar-online' : '' }}">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-px-40 h-auto rounded-circle">
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar {{ $user->isOnline() ? 'avatar-online' : '' }}">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-px-40 h-auto rounded-circle">
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block">{{ $user->name }}</span>
                  <small class="text-muted">{{ $user->email }}</small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
              <i class="icon-base ti tabler-user me-2"></i>
              <span class="align-middle">{{ __('Мой профиль') }}</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('admin.settings.edit') }}">
              <i class="icon-base ti tabler-settings me-2"></i>
              <span class="align-middle">{{ __('Настройки') }}</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          @if (Route::has('logout'))
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">
                  <i class="icon-base ti tabler-logout me-2"></i>
                  <span class="align-middle">{{ __('Выйти') }}</span>
                </button>
              </form>
            </li>
          @endif
        </ul>
      </li>
    </ul>
  </div>
</nav>

