@if (empty($permissionGroups))
  <div class="alert alert-warning">
    <i class="icon-base ti tabler-alert-triangle me-2"></i>
    {{ __('Разрешения не найдены. Выполните сидер PermissionSeeder.') }}
  </div>
  @push('scripts')
    <script>
      console.warn('[permissions-matrix] No permissions available. Run PermissionSeeder.');
    </script>
  @endpush
  @php return; @endphp
@endif

@php
  $selectedPermissionIds = collect($selectedPermissions ?? [])
    ->map(fn ($value) => (int) $value)
    ->filter()
    ->unique()
    ->values()
    ->all();

  $permissionsBySlug = [];
  foreach ($permissionGroups as $data) {
      foreach ($data['permissions'] as $permission) {
          $permissionsBySlug[$permission->slug] = $permission;
      }
  }

  $definedSections = [
    [
      'title' => __('Компании'),
      'icon' => 'ti tabler-building',
      'permissions' => [
        'companies.view' => __('Может просматривать список компаний'),
        'companies.create' => __('Может добавлять новые компании'),
        'companies.edit' => __('Может редактировать компании'),
        'companies.delete' => __('Может удалять компании'),
        'companies.manage' => __('Полное управление компаниями'),
      ],
    ],
    [
      'title' => __('Пользователи'),
      'icon' => 'ti tabler-users',
      'permissions' => [
        'users.view' => __('Может просматривать список пользователей'),
        'users.create' => __('Может добавлять новых пользователей'),
        'users.edit' => __('Может редактировать пользователей'),
        'users.delete' => __('Может удалять пользователей'),
        'users.manage' => __('Полное управление пользователями'),
      ],
    ],
    [
      'title' => __('Телефонный справочник'),
      'icon' => 'ti tabler-address-book',
      'permissions' => [
        'user-phones.view' => __('Может просматривать телефонный справочник'),
        'user-phones.create' => __('Может добавлять записи в телефонный справочник'),
        'user-phones.edit' => __('Может редактировать записи телефонного справочника'),
        'user-phones.delete' => __('Может удалять записи телефонного справочника'),
        'user-phones.manage' => __('Полное управление телефонным справочником'),
      ],
    ],
    [
      'title' => __('ledger.page_title'),
      'icon' => 'ti tabler-wallet',
      'permissions' => [
        'ledger.view' => __('ledger.permissions.view'),
        'ledger.manage' => __('ledger.permissions.manage'),
      ],
    ],
    [
      'title' => __('Роли и права'),
      'icon' => 'ti tabler-shield',
      'permissions' => [
        'roles.view' => __('Может просматривать список ролей'),
        'roles.create' => __('Может создавать роли'),
        'roles.edit' => __('Может редактировать роли'),
        'roles.delete' => __('Может удалять роли'),
        'roles.manage' => __('Полное управление ролями'),
      ],
    ],
    [
      'title' => __('Логи и аудит'),
      'icon' => 'ti tabler-list-details',
      'permissions' => [
        'logs.view' => __('Может просматривать логи активности'),
        'logs.show' => __('Может просматривать детали логов'),
      ],
    ],
  ];

  $coveredSlugs = collect($definedSections)
    ->flatMap(fn ($section) => array_keys($section['permissions']))
    ->filter(fn ($slug) => array_key_exists($slug, $permissionsBySlug))
    ->all();

  $remainingPermissions = collect($permissionGroups)
    ->map(function ($data) use ($coveredSlugs) {
        /** @var \App\Models\PermissionGroup $group */
        $group = $data['group'];
        $permissions = $data['permissions']->filter(fn ($permission) => !in_array($permission->slug, $coveredSlugs, true));

        return [
            'group' => $group,
            'permissions' => $permissions,
        ];
    })
    ->filter(fn ($item) => $item['permissions']->isNotEmpty());
@endphp

<div class="permissions-list">
  @foreach ($definedSections as $section)
    @php
      $sectionPermissions = collect($section['permissions'])
        ->filter(fn ($label, $slug) => array_key_exists($slug, $permissionsBySlug));
    @endphp

    @if ($sectionPermissions->isNotEmpty())
      <div class="card mb-4">
        <div class="card-header d-flex align-items-center">
          <i class="icon-base {{ $section['icon'] }} me-2"></i>
          <h6 class="card-title mb-0">{{ $section['title'] }}</h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            @foreach ($sectionPermissions as $slug => $label)
              @php
                $permission = $permissionsBySlug[$slug];
                $isChecked = in_array($permission->id, $selectedPermissionIds, true);
              @endphp
              <div class="col-md-6 col-lg-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      name="permissions[]"
                      value="{{ $permission->id }}"
                      id="permission{{ $permission->id }}"
                      {{ $isChecked ? 'checked' : '' }}>
                    <label class="form-check-label" for="permission{{ $permission->id }}">
                      <span class="fw-semibold d-block mb-1">{{ $label }}</span>
                      <span class="badge bg-label-secondary">{{ $permission->slug }}</span>
                    </label>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @endif
  @endforeach

  @if ($remainingPermissions->isNotEmpty())
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <i class="icon-base ti tabler-dots me-2"></i>
        <h6 class="card-title mb-0">{{ __('Дополнительные разрешения') }}</h6>
      </div>
      <div class="card-body">
        @foreach ($remainingPermissions as $item)
          @php
            /** @var \App\Models\PermissionGroup $group */
            $group = $item['group'];
            $permissions = $item['permissions'];
          @endphp
          <div class="mb-4">
            <h6 class="text-muted text-uppercase small mb-3">{{ $group->name }}</h6>
            <div class="row g-3">
              @foreach ($permissions as $permission)
                @php
                  $isChecked = in_array($permission->id, $selectedPermissionIds, true);
                @endphp
                <div class="col-md-6 col-lg-4">
                  <div class="border rounded-3 p-3 h-100">
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $permission->id }}"
                        id="permission{{ $permission->id }}"
                        {{ $isChecked ? 'checked' : '' }}>
                      <label class="form-check-label" for="permission{{ $permission->id }}">
                        <span class="fw-semibold d-block mb-1">{{ $permission->name }}</span>
                        <span class="badge bg-label-secondary">{{ $permission->slug }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif
</div>

