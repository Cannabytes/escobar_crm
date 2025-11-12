@extends('layouts.admin')

@section('title', __('Роль: :name', ['name' => $role->name]))

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-icon btn-label-secondary me-3">
                <i class="icon-base ti tabler-arrow-left"></i>
              </a>
              <div>
                <h5 class="card-title mb-1">{{ $role->name }}</h5>
                <small class="text-muted">
                  <code>{{ $role->slug }}</code>
                </small>
              </div>
            </div>
            <div class="d-flex gap-2">
              @if ($role->is_system)
                <span class="badge bg-label-warning align-self-center">
                  <i class="icon-base ti tabler-shield-lock me-1"></i>
                  {{ __('Системная роль') }}
                </span>
              @endif
              <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                <i class="icon-base ti tabler-edit me-1"></i>{{ __('Редактировать') }}
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-4 col-md-12 mb-4">
          <div class="card mb-4">
            <div class="card-body">
              <h6 class="card-title mb-3">{{ __('Информация о роли') }}</h6>
              
              <div class="mb-3">
                <label class="text-muted small">{{ __('Название') }}</label>
                <div class="fw-medium">{{ $role->name }}</div>
              </div>

              <div class="mb-3">
                <label class="text-muted small">{{ __('Slug') }}</label>
                <div><code>{{ $role->slug }}</code></div>
              </div>

              @if ($role->description)
                <div class="mb-3">
                  <label class="text-muted small">{{ __('Описание') }}</label>
                  <div>{{ $role->description }}</div>
                </div>
              @endif

              <div class="mb-3">
                <label class="text-muted small">{{ __('Статус') }}</label>
                <div>
                  @if ($role->is_active)
                    <span class="badge bg-label-success">{{ __('Активна') }}</span>
                  @else
                    <span class="badge bg-label-secondary">{{ __('Неактивна') }}</span>
                  @endif
                </div>
              </div>

              <div class="mb-3">
                <label class="text-muted small">{{ __('Тип') }}</label>
                <div>
                  @if ($role->is_system)
                    <span class="badge bg-label-warning">{{ __('Системная') }}</span>
                  @else
                    <span class="badge bg-label-info">{{ __('Пользовательская') }}</span>
                  @endif
                </div>
              </div>

              <div class="mb-3">
                <label class="text-muted small">{{ __('Создана') }}</label>
                <div>{{ $role->created_at->format('d.m.Y H:i') }}</div>
              </div>

              <div class="mb-0">
                <label class="text-muted small">{{ __('Обновлена') }}</label>
                <div>{{ $role->updated_at->format('d.m.Y H:i') }}</div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <h6 class="card-title mb-3">
                <i class="icon-base ti tabler-users me-2"></i>{{ __('Статистика') }}
              </h6>
              
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">{{ __('Пользователей с ролью') }}</span>
                <span class="badge bg-label-primary">{{ $usersCount }}</span>
              </div>

              <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">{{ __('Разрешений назначено') }}</span>
                <span class="badge bg-label-success">{{ $role->permissions->count() }}</span>
              </div>

              @if ($usersCount > 0)
                <hr>
                <a href="{{ route('admin.users.index', ['role' => $role->slug]) }}" class="btn btn-label-primary w-100">
                  <i class="icon-base ti tabler-users me-1"></i>
                  {{ __('Посмотреть пользователей') }}
                </a>
              @endif
            </div>
          </div>
        </div>

        <div class="col-lg-8 col-md-12 mb-4">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title mb-3">
                <i class="icon-base ti tabler-lock me-2"></i>{{ __('Разрешения роли') }}
                <span class="badge bg-label-secondary ms-2">{{ $role->permissions->count() }}</span>
              </h6>

              @if ($role->isSuperAdmin())
                <div class="alert alert-info">
                  <i class="icon-base ti tabler-crown me-2"></i>
                  <strong>{{ __('Супер Администратор') }}</strong> {{ __('имеет все возможные разрешения автоматически.') }}
                </div>
              @elseif ($permissionsByGroup->isEmpty())
                <div class="alert alert-warning">
                  <i class="icon-base ti tabler-alert-triangle me-2"></i>
                  {{ __('У этой роли пока нет разрешений.') }} <a href="{{ route('admin.roles.edit', $role) }}">{{ __('Добавьте разрешения') }}</a>
                </div>
              @else
                <div class="accordion" id="permissionsAccordion">
                  @foreach ($permissionsByGroup as $groupId => $permissions)
                    @php
                      $group = $permissions->first()->group;
                    @endphp
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading{{ $groupId }}">
                        <button
                          class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                          type="button"
                          data-bs-toggle="collapse"
                          data-bs-target="#collapse{{ $groupId }}"
                          aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                          aria-controls="collapse{{ $groupId }}">
                          <i class="icon-base ti tabler-shield me-2"></i>
                          <strong>{{ $group->name }}</strong>
                          @if ($group->description)
                            <span class="text-muted ms-2 small">{{ $group->description }}</span>
                          @endif
                          <span class="badge bg-label-secondary ms-auto me-2">{{ $permissions->count() }}</span>
                        </button>
                      </h2>
                      <div
                        id="collapse{{ $groupId }}"
                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                        aria-labelledby="heading{{ $groupId }}"
                        data-bs-parent="#permissionsAccordion">
                        <div class="accordion-body">
                          <div class="row g-3">
                            @foreach ($permissions as $permission)
                              <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                  <i class="icon-base ti tabler-check text-success me-2 mt-1"></i>
                                  <div>
                                    <strong>{{ $permission->name }}</strong>
                                    <span class="badge bg-label-{{ $permission->type === 'view' ? 'info' : ($permission->type === 'create' ? 'success' : ($permission->type === 'edit' ? 'warning' : ($permission->type === 'delete' ? 'danger' : 'primary'))) }} ms-1">
                                      {{ $permission->getTypeLabel() }}
                                    </span>
                                    @if ($permission->description)
                                      <div class="text-muted small">{{ $permission->description }}</div>
                                    @endif
                                  </div>
                                </div>
                              </div>
                            @endforeach
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

