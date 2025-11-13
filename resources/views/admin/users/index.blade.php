@extends('layouts.admin')

@section('title', __('Список пользователей'))

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
              <h5 class="card-title mb-1">{{ __('Пользователи системы') }}</h5>
              <small class="text-muted">{{ __('Управление учетными записями, ролями и доступами к компаниям.') }}</small>
            </div>
            <div class="d-flex gap-2">
              <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="icon-base ti tabler-user-plus me-1"></i>
                {{ __('Создать пользователя') }}
              </a>
            </div>
          </div>

          <form class="mt-4" method="GET" action="{{ route('admin.users.index') }}">
            <div class="row g-3">
              <div class="col-md-6 col-lg-5">
                <label for="search" class="form-label mb-1">{{ __('Поиск по имени или email') }}</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                  <input
                    type="search"
                    id="search"
                    name="search"
                    value="{{ $filters['search'] }}"
                    class="form-control"
                    placeholder="{{ __('Начните вводить имя или email') }}">
                </div>
              </div>

              <div class="col-md-4 col-lg-3">
                <label for="role_id" class="form-label mb-1">{{ __('Роль') }}</label>
                <select
                  id="role_id"
                  name="role_id"
                  class="form-select">
                  <option value="">{{ __('Все роли') }}</option>
                  @foreach ($availableRoles as $roleId => $roleName)
                    <option value="{{ $roleId }}" {{ (int) $filters['role_id'] === (int) $roleId ? 'selected' : '' }}>
                      {{ $roleName }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-label-primary w-100">
                  <i class="icon-base ti tabler-filter me-1"></i>{{ __('Фильтровать') }}
                </button>
                @if ($filters['search'] || $filters['role_id'])
                  <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary w-100">
                    <i class="icon-base ti tabler-refresh me-1"></i>{{ __('Сбросить') }}
                  </a>
                @endif
              </div>
            </div>
          </form>
        </div>

        <div class="card-body p-0">
          @if ($users->isEmpty())
            <div class="p-5 text-center">
              <div class="mb-2">
                <i class="icon-base ti tabler-users text-muted" style="font-size: 48px;"></i>
              </div>
              <h6 class="mb-1">{{ __('Пользователи не найдены') }}</h6>
              <p class="text-muted mb-3">
                {{ __('Создайте первого пользователя или измените параметры поиска.') }}
              </p>
              <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                {{ __('Создать пользователя') }}
              </a>
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                  <tr>
                    <th scope="col" style="width: 60px;">#</th>
                    <th scope="col">{{ __('Имя') }}</th>
                    <th scope="col">{{ __('Email') }}</th>
                    <th scope="col">{{ __('Телефон') }}</th>
                    <th scope="col">{{ __('Роль') }}</th>
                    <th scope="col">{{ __('Статус') }}</th>
                    <th scope="col">{{ __('Компании') }}</th>
                    <th scope="col">{{ __('Дата создания') }}</th>
                    <th scope="col" class="text-end">{{ __('Действия') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($users as $index => $user)
                    <tr>
                      <td>{{ $users->firstItem() + $index }}</td>
                      <td class="fw-medium">
                        <div class="d-flex align-items-center">
                          <div class="avatar avatar-sm {{ $user->isOnline() ? 'avatar-online' : '' }} me-2">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle">
                          </div>
                          <div class="d-flex flex-column">
                            @if(auth()->user()?->hasAnyPermission(['users.edit', 'users.manage']))
                              <a
                                href="{{ route('admin.users.edit', $user) }}"
                                class="fw-medium text-primary text-decoration-underline"
                                title="{{ __('Редактировать пользователя') }}">
                                {{ $user->name }}
                              </a>
                            @else
                              <span>{{ $user->name }}</span>
                            @endif
                          </div>
                        </div>
                      </td>
                      <td>
                        <a href="mailto:{{ $user->email }}" class="text-primary">{{ $user->email }}</a>
                      </td>
                      <td>
                        @if($user->phone || $user->operator || $user->phone_comment)
                          <div class="d-flex flex-column">
                            @if($user->phone)
                              <div class="d-flex align-items-center gap-1">
                                @if($user->operator)
                                  <span class="badge bg-label-info" style="font-size: 0.7rem;">{{ $user->operator }}</span>
                                @endif
                                <a href="tel:{{ $user->phone }}" class="text-dark">{{ $user->phone }}</a>
                              </div>
                            @endif
                            @if($user->phone_comment)
                              <small class="text-muted" style="cursor: pointer;" 
                                     data-bs-toggle="tooltip" 
                                     data-bs-placement="top" 
                                     title="{{ $user->phone_comment }}">
                                <i class="ti ti-message-circle"></i> {{ \Illuminate\Support\Str::limit($user->phone_comment, 20) }}
                              </small>
                            @endif
                          </div>
                        @else
                          <span class="text-muted">—</span>
                        @endif
                      </td>
                      <td>
                        @php
                          $roleName = $user->roleModel?->name;
                          if (! $roleName && $user->isSuperAdmin()) {
                              $roleName = __('Супер администратор');
                          }
                        @endphp
                        @if ($roleName)
                          <span class="badge bg-label-primary">
                            {{ $roleName }}
                          </span>
                        @else
                          <span class="text-muted">{{ __('Роль не назначена') }}</span>
                        @endif
                      </td>
                      <td>
                        @if($user->isOnline())
                          <span class="badge bg-success">
                            <i class="bx bx-circle bx-xs"></i> {{ __('users.online') }}
                          </span>
                        @else
                          <small class="text-muted">
                            {{ $user->last_activity }}
                          </small>
                        @endif
                      </td>
                      <td>
                        @php
                          $moderatedCompanies = $user->moderatedCompanies ?? collect();
                          $moderatedCompanyIds = $moderatedCompanies->pluck('id');
                          $accessibleCompanies = ($user->accessibleCompanies ?? collect())
                            ->reject(fn ($company) => $moderatedCompanyIds->contains($company->id));
                        @endphp

                        @if ($moderatedCompanies->isEmpty() && $accessibleCompanies->isEmpty())
                          <span class="text-muted">—</span>
                        @else
                          <div class="d-flex flex-wrap gap-1">
                            @foreach ($moderatedCompanies as $company)
                              <span class="badge bg-label-primary">
                                {{ $company->name }}
                                <span class="text-uppercase ms-1" style="font-size: 0.7rem;">{{ __('Модератор') }}</span>
                              </span>
                            @endforeach
                            @foreach ($accessibleCompanies as $company)
                              @php
                                $isEditor = $company->pivot->access_type === 'edit';
                                $badgeClass = $isEditor ? 'bg-label-success' : 'bg-label-info';
                                $badgeText = $isEditor ? __('Редактирование') : __('Просмотр');
                              @endphp
                              <span class="badge {{ $badgeClass }}">
                                {{ $company->name }}
                                <span class="text-uppercase ms-1" style="font-size: 0.7rem;">{{ $badgeText }}</span>
                              </span>
                            @endforeach
                          </div>
                        @endif
                      </td>
                      <td>{{ optional($user->created_at)->format('d.m.Y H:i') }}</td>
                      <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                          @canany(['users.edit', 'users.manage'])
                            <a
                              href="{{ route('admin.users.edit', $user) }}"
                              class="btn btn-sm btn-icon btn-label-primary"
                              title="{{ __('Редактировать пользователя') }}">
                              <i class="ti tabler-edit"></i>
                            </a>
                          @endcanany
                          @if(auth()->user()->isSuperAdmin())
                            <button type="button" class="btn btn-sm btn-icon btn-label-secondary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editPhoneModal{{ $user->id }}"
                                    title="{{ __('Редактировать телефон') }}">
                              <i class="ti tabler-phone-plus"></i>
                            </button>
                          @endif
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

        @if ($users->hasPages())
          <div class="card-footer d-flex justify-content-between flex-column flex-md-row gap-2 align-items-center">
            <div class="text-muted">
              {{ trans_choice(':count пользователь|:count пользователя|:count пользователей', $users->total(), ['count' => $users->total()]) }}
            </div>
            {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Модальные окна для редактирования телефонов -->
  @foreach ($users as $user)
    <div class="modal fade" id="editPhoneModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form action="{{ route('admin.users.update-phone', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
              <h5 class="modal-title">{{ __('Редактировать телефон') }} — {{ $user->name }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">{{ __('Оператор') }}</label>
                <input type="text" class="form-control" name="operator" 
                       value="{{ old('operator', $user->operator) }}"
                       placeholder="{{ __('Например: МТС, Билайн, Мегафон') }}">
                <small class="text-muted">{{ __('Название мобильного оператора') }}</small>
              </div>

              <div class="mb-3">
                <label class="form-label">{{ __('Номер телефона') }}</label>
                <input type="text" class="form-control" name="phone" 
                       value="{{ old('phone', $user->phone) }}"
                       placeholder="{{ __('Например: +7 (999) 123-45-67') }}">
              </div>

              <div class="mb-3">
                <label class="form-label">{{ __('Комментарий') }}</label>
                <textarea class="form-control" name="phone_comment" rows="4"
                          placeholder="{{ __('Дополнительная информация о номере телефона') }}">{{ old('phone_comment', $user->phone_comment) }}</textarea>
                <small class="text-muted">{{ __('Любая полезная информация: время работы, дополнительные контакты и т.д.') }}</small>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                {{ __('Отмена') }}
              </button>
              <button type="submit" class="btn btn-primary">
                {{ __('Сохранить') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach
@endsection


