@php($currentRoute = request()->route() ? request()->route()->getName() : null)

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('admin.users.index') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <span class="text-primary">
          <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
              fill="currentColor" />
            <path
              opacity="0.08"
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
              fill="#161616" />
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
              fill="currentColor" />
          </svg>
        </span>
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-3">Escobar Admin</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="icon-base ti tabler-x"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ __('Навигация') }}</span>
    </li>

    <li class="menu-item {{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
      <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-layout-dashboard"></i>
        <div>{{ __('Панель управления') }}</div>
      </a>
    </li>

    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ __('Компании') }}</span>
    </li>
    <li class="menu-item {{ $currentRoute === 'admin.companies.create' ? 'active' : '' }}">
      <a href="{{ route('admin.companies.create') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-building"></i>
        <div>{{ __('Добавить компанию') }}</div>
      </a>
    </li>

    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ __('Пользователи') }}</span>
    </li>
    <li class="menu-item {{ $currentRoute === 'admin.users.index' ? 'active' : '' }}">
      <a href="{{ route('admin.users.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-users"></i>
        <div>{{ __('Список пользователей') }}</div>
      </a>
    </li>
    <li class="menu-item {{ $currentRoute === 'admin.users.create' ? 'active' : '' }}">
      <a href="{{ route('admin.users.create') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-user-plus"></i>
        <div>{{ __('Создать пользователя') }}</div>
      </a>
    </li>
  </ul>
</aside>

