@extends('layouts.admin')

@section('title', __('Управление ролями'))

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
              <h5 class="card-title mb-1">{{ __('Роли и разрешения') }}</h5>
              <small class="text-muted">{{ __('Управление ролями пользователей и их разрешениями в системе') }}</small>
            </div>
            <div class="d-flex gap-2">
              <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="icon-base ti tabler-plus me-1"></i>
                {{ __('Создать роль') }}
              </a>
            </div>
          </div>

          <form class="mt-4" method="GET" action="{{ route('admin.roles.index') }}">
            <div class="row g-3">
              <div class="col-md-6 col-lg-5">
                <label for="search" class="form-label mb-1">{{ __('Поиск по названию или описанию') }}</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                  <input
                    type="search"
                    id="search"
                    name="search"
                    value="{{ $filters['search'] }}"
                    class="form-control"
                    placeholder="{{ __('Начните вводить название роли') }}">
                </div>
              </div>

              <div class="col-md-4 col-lg-3">
                <label for="status" class="form-label mb-1">{{ __('Статус') }}</label>
                <select id="status" name="status" class="form-select">
                  <option value="">{{ __('Все роли') }}</option>
                  <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>{{ __('Активные') }}</option>
                  <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>{{ __('Неактивные') }}</option>
                  <option value="system" {{ $filters['status'] === 'system' ? 'selected' : '' }}>{{ __('Системные') }}</option>
                </select>
              </div>

              <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-label-primary w-100">
                  <i class="icon-base ti tabler-filter me-1"></i>{{ __('Фильтровать') }}
                </button>
                @if ($filters['search'] || $filters['status'])
                  <a href="{{ route('admin.roles.index') }}" class="btn btn-label-secondary w-100">
                    <i class="icon-base ti tabler-refresh me-1"></i>{{ __('Сбросить') }}
                  </a>
                @endif
              </div>
            </div>
          </form>
        </div>

        <div class="card-body p-0">
          @if ($roles->isEmpty())
            <div class="p-5 text-center">
              <div class="mb-2">
                <i class="icon-base ti tabler-shield-off text-muted" style="font-size: 48px;"></i>
              </div>
              <h6 class="mb-1">{{ __('Роли не найдены') }}</h6>
              <p class="text-muted mb-3">{{ __('Создайте первую роль или измените параметры поиска.') }}</p>
              <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">{{ __('Создать роль') }}</a>
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>{{ __('Название роли') }}</th>
                    <th>{{ __('Slug') }}</th>
                    <th>{{ __('Пользователей') }}</th>
                    <th>{{ __('Разрешений') }}</th>
                    <th>{{ __('Статус') }}</th>
                    <th class="text-end">{{ __('Действия') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($roles as $role)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-{{ $role->is_system ? 'primary' : 'secondary' }}">
                              <i class="icon-base ti {{ $role->is_system ? 'tabler-shield-lock' : 'tabler-shield' }}"></i>
                            </span>
                          </div>
                          <div>
                            <a href="{{ route('admin.roles.show', $role) }}" class="text-heading fw-medium">
                              {{ $role->name }}
                            </a>
                            @if ($role->is_system)
                              <span class="badge bg-label-primary ms-1">{{ __('Системная') }}</span>
                            @endif
                            @if ($role->description)
                              <div class="text-muted small">{{ Str::limit($role->description, 60) }}</div>
                            @endif
                          </div>
                        </div>
                      </td>
                      <td>
                        <code class="text-sm">{{ $role->slug }}</code>
                      </td>
                      <td>
                        <span class="badge bg-label-info">
                          <i class="icon-base ti tabler-users me-1"></i>
                          {{ $role->users_count }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-label-success">
                          <i class="icon-base ti tabler-lock me-1"></i>
                          {{ $role->permissions->count() }}
                        </span>
                      </td>
                      <td>
                        @if ($role->is_active)
                          <span class="badge bg-label-success">{{ __('Активна') }}</span>
                        @else
                          <span class="badge bg-label-secondary">{{ __('Неактивна') }}</span>
                        @endif
                      </td>
                      <td class="text-end">
                        <div class="dropdown">
                          <button
                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                            data-bs-toggle="dropdown">
                            <i class="icon-base ti tabler-dots-vertical"></i>
                          </button>
                          <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                              <a class="dropdown-item" href="{{ route('admin.roles.show', $role) }}">
                                <i class="icon-base ti tabler-eye me-2"></i>{{ __('Просмотр') }}
                              </a>
                            </li>
                            <li>
                              <a class="dropdown-item" href="{{ route('admin.roles.edit', $role) }}">
                                <i class="icon-base ti tabler-edit me-2"></i>{{ __('Редактировать') }}
                              </a>
                            </li>
                            @if (!$role->is_system)
                              <li><hr class="dropdown-divider"></li>
                              <li>
                                <form action="{{ route('admin.roles.toggle-active', $role) }}" method="POST" class="d-inline">
                                  @csrf
                                  <button type="submit" class="dropdown-item">
                                    <i class="icon-base ti {{ $role->is_active ? 'tabler-ban' : 'tabler-check' }} me-2"></i>
                                    {{ $role->is_active ? __('Деактивировать') : __('Активировать') }}
                                  </button>
                                </form>
                              </li>
                              @if ($role->users_count === 0)
                                <li>
                                  <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('Вы уверены, что хотите удалить эту роль?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                      <i class="icon-base ti tabler-trash me-2"></i>{{ __('Удалить') }}
                                    </button>
                                  </form>
                                </li>
                              @endif
                            @endif
                          </ul>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

        @if ($roles->hasPages())
          <div class="card-footer">
            {{ $roles->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

