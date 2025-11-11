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
                <label for="role" class="form-label mb-1">{{ __('Роль') }}</label>
                <select
                  id="role"
                  name="role"
                  class="form-select">
                  <option value="">{{ __('Все роли') }}</option>
                  @foreach ($roleLabels as $roleValue => $roleLabel)
                    <option value="{{ $roleValue }}" {{ $filters['role'] === $roleValue ? 'selected' : '' }}>
                      {{ $roleLabel }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-label-primary w-100">
                  <i class="icon-base ti tabler-filter me-1"></i>{{ __('Фильтровать') }}
                </button>
                @if ($filters['search'] || $filters['role'])
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
                    <th scope="col">{{ __('Роль') }}</th>
                    <th scope="col">{{ __('Дата создания') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($users as $index => $user)
                    <tr>
                      <td>{{ $users->firstItem() + $index }}</td>
                      <td class="fw-medium">
                        {{ $user->name }}
                      </td>
                      <td>
                        <a href="mailto:{{ $user->email }}" class="text-primary">{{ $user->email }}</a>
                      </td>
                      <td>
                        <span class="badge bg-label-primary text-capitalize">
                          {{ $roleLabels[$user->role] ?? $user->role }}
                        </span>
                      </td>
                      <td>{{ optional($user->created_at)->format('d.m.Y H:i') }}</td>
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
@endsection


