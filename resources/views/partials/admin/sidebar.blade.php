@php
  $currentRoute = request()->route() ? request()->route()->getName() : null;
  $routeCompany = request()->route('company');
  $currentCompanyId = $routeCompany instanceof \App\Models\Company ? $routeCompany->getKey() : null;
  $userSidebarCompanies = $sidebarUserCompanies ?? collect();
  $isCompaniesListActive = $currentRoute === 'admin.companies.index'
    || (in_array($currentRoute, ['admin.companies.show', 'admin.companies.edit'], true)
      && ! $userSidebarCompanies->contains(fn ($company) => $company->getKey() === $currentCompanyId));
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('admin.companies.index') }}" class="app-brand-link">
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
      <span class="app-brand-text demo menu-text fw-bold ms-3">{{ config('app.admin_sidebar_title') }}</span>
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
      <span class="menu-header-text">{{ __('Коммуникации') }}</span>
    </li>
    <li class="menu-item {{ $currentRoute === 'admin.chat.index' ? 'active' : '' }}">
      <a href="{{ route('admin.chat.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-message-circle"></i>
        <div class="d-flex align-items-center">
          {{ __('Чат') }}
          @if(!empty($hasUnreadPrivateMessages))
            <span class="ms-2 rounded-circle bg-danger" style="width: 8px; height: 8px; display: inline-block;" aria-label="{{ __('Есть непрочитанные приватные сообщения') }}"></span>
          @endif
        </div>
      </a>
    </li>

    @if(auth()->user()->hasAnyPermission(['companies.view', 'companies.manage']))
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ __('Компании') }}</span>
    </li>
    
    <li class="menu-item {{ $isCompaniesListActive ? 'active' : '' }}">
      <a href="{{ route('admin.companies.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-building"></i>
        <div>{{ __('Список компаний') }}</div>
      </a>
    </li>

    @if(auth()->user()->hasAnyPermission(['companies.create', 'companies.manage']))
    <li class="menu-item {{ $currentRoute === 'admin.companies.create' ? 'active' : '' }}">
      <a href="{{ route('admin.companies.create') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-building-plus"></i>
        <div>{{ __('Добавить компанию') }}</div>
      </a>
    </li>
    @endif
    @endif

    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ __('Мои компании') }}</span>
    </li>

    <li class="menu-item {{ $currentRoute === 'admin.my-companies.index' ? 'active' : '' }}">
      <a href="{{ route('admin.my-companies.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-settings"></i>
        <div>{{ __('Настройка меню') }}</div>
      </a>
    </li>

    @forelse($userSidebarCompanies as $sidebarCompany)
      @php
        $isMyCompanyActive = in_array($currentRoute, ['admin.companies.show', 'admin.companies.edit'], true)
          && $currentCompanyId === $sidebarCompany->getKey();
      @endphp
      <li class="menu-item {{ $isMyCompanyActive ? 'active' : '' }}">
        <a href="{{ route('admin.companies.show', $sidebarCompany) }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-building-skyscraper"></i>
          <div class="text-truncate" style="max-width: 160px;">
            {{ $sidebarCompany->name }}
          </div>
        </a>
      </li>
    @empty
      <li class="menu-item disabled">
        <a href="javascript:void(0);" class="menu-link">
          <i class="menu-icon icon-base ti tabler-briefcase-off"></i>
          <div>{{ __('Нет закрепленных компаний') }}</div>
        </a>
      </li>
    @endforelse

    @if(auth()->user()->hasAnyPermission(['users.view', 'users.manage', 'user-phones.view', 'user-phones.manage']))
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ __('Пользователи') }}</span>
    </li>
    @if(auth()->user()->hasAnyPermission(['users.view', 'users.manage']))
      <li class="menu-item {{ $currentRoute === 'admin.users.index' ? 'active' : '' }}">
        <a href="{{ route('admin.users.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-users"></i>
          <div>{{ __('Список пользователей') }}</div>
        </a>
      </li>
    @endif
    @if(auth()->user()->hasAnyPermission(['user-phones.view', 'user-phones.manage']))
    <li class="menu-item {{ $currentRoute === 'admin.users.phones.index' ? 'active' : '' }}">
      <a href="{{ route('admin.users.phones.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-address-book"></i>
        <div>{{ __('Телефоны') }}</div>
      </a>
    </li>
    @endif
    @if(auth()->user()->hasAnyPermission(['users.create', 'users.manage']))
    <li class="menu-item {{ $currentRoute === 'admin.users.create' ? 'active' : '' }}">
      <a href="{{ route('admin.users.create') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-user-plus"></i>
        <div>{{ __('Создать пользователя') }}</div>
      </a>
    </li>
    @endif
    @endif

    @if(auth()->user()->hasAnyPermission(['ledger.view', 'ledger.manage']))
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ __('ledger.page_title') }}</span>
    </li>
    <li class="menu-item {{ $currentRoute === 'admin.ledger.index' ? 'active' : '' }}">
      <a href="{{ route('admin.ledger.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-wallet"></i>
        <div>{{ __('ledger.page_title') }}</div>
      </a>
    </li>
    @endif

    @if(auth()->user()->hasAnyPermission(['companies.view', 'companies.manage']))
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ __('Статистика') }}</span>
    </li>
    <li class="menu-item {{ $currentRoute === 'admin.statistics.index' ? 'active' : '' }}">
      <a href="{{ route('admin.statistics.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-chart-bar"></i>
        <div>{{ __('Статистика') }}</div>
      </a>
    </li>
    @endif

    @if(auth()->user()->hasAnyPermission(['logs.view', 'roles.view']) || auth()->user()->isSuperAdmin())
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ __('Система') }}</span>
    </li>
    
    @if(auth()->user()->hasAnyPermission(['roles.view', 'roles.manage']) || auth()->user()->isSuperAdmin())
    <li class="menu-item {{ in_array($currentRoute, ['admin.roles.index', 'admin.roles.show', 'admin.roles.edit', 'admin.roles.create']) ? 'active' : '' }}">
      <a href="{{ route('admin.roles.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-shield"></i>
        <div>{{ __('Роли и права') }}</div>
      </a>
    </li>
    @endif
    
    @if(auth()->user()->hasAnyPermission(['logs.view']) || auth()->user()->isSuperAdmin())
    <li class="menu-item {{ $currentRoute === 'admin.logs.index' ? 'active' : '' }}">
      <a href="{{ route('admin.logs.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-list"></i>
        <div>{{ __('logs.page_title') }}</div>
      </a>
    </li>
    @endif
    @endif
  </ul>
</aside>
