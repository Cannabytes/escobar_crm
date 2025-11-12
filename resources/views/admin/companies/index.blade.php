@extends('layouts.admin')

@section('title', __('Компании'))

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">
      <span class="text-muted fw-light">{{ __('Управление') }} /</span> {{ __('Компании') }}
    </h4>
    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
      <i class="mdi mdi-plus me-1"></i> {{ __('Добавить компанию') }}
    </a>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">{{ __('Список компаний') }}</h5>
      <small class="text-muted">{{ __('Всего: :count', ['count' => $companies->total()]) }}</small>
    </div>
    <div class="card-body">
      @if ($companies->isEmpty())
        <div class="text-center py-5">
          <div class="mb-3">
            <i class="mdi mdi-briefcase-outline" style="font-size: 48px; color: #ccc;"></i>
          </div>
          <p class="text-muted">{{ __('Компании еще не добавлены') }}</p>
          <a href="{{ route('admin.companies.create') }}" class="btn btn-primary mt-2">
            {{ __('Добавить первую компанию') }}
          </a>
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Название') }}</th>
                <th>{{ __('Страна') }}</th>
                <th>{{ __('Модератор') }}</th>
                <th>{{ __('Создана') }}</th>
                <th class="text-end">{{ __('Действия') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($companies as $company)
                <tr>
                  <td><strong>#{{ $company->id }}</strong></td>
                  <td>
                    <a href="{{ route('admin.companies.show', $company) }}" class="text-body">
                      {{ $company->name }}
                    </a>
                  </td>
                  <td>
                    <span class="badge bg-label-secondary">{{ $company->country }}</span>
                  </td>
                  <td>
                    @if ($company->moderator)
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xs me-2">
                          <span class="avatar-initial rounded-circle bg-label-primary">
                            {{ mb_substr($company->moderator->name, 0, 1) }}
                          </span>
                        </div>
                        {{ $company->moderator->name }}
                      </div>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td>{{ $company->created_at->format('d.m.Y') }}</td>
                  <td class="text-end">
                    <div class="d-flex justify-content-end align-items-center gap-2">
                      @if (! $company->hasLicenseDetails())
                        <a href="{{ route('admin.companies.show', $company) }}?open_license=1"
                           class="btn btn-sm btn-warning">
                          <i class="mdi mdi-file-edit-outline me-1"></i>
                          {{ __('Заполните данные компании') }}
                        </a>
                      @endif
                      <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i class="mdi mdi-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                          <a class="dropdown-item" href="{{ route('admin.companies.show', $company) }}">
                            <i class="mdi mdi-eye-outline me-1"></i> {{ __('Просмотр') }}
                          </a>
                          <a class="dropdown-item" href="{{ route('admin.companies.edit', $company) }}">
                            <i class="mdi mdi-pencil-outline me-1"></i> {{ __('Редактировать') }}
                          </a>
                          <div class="dropdown-divider"></div>
                          <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" 
                                onsubmit="return confirm('{{ __('Вы уверены?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                              <i class="mdi mdi-trash-can-outline me-1"></i> {{ __('Удалить') }}
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        @if ($companies->hasPages())
          <div class="mt-4">
            {{ $companies->links() }}
          </div>
        @endif
      @endif
    </div>
  </div>
@endsection

