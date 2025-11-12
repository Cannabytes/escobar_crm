@extends('layouts.admin')

@section('title', __('Компании'))

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">
      <span class="text-muted fw-light">{{ __('Управление') }} /</span> {{ __('Компании') }}
    </h4>
    @can('create', \App\Models\Company::class)
      <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
        <i class="mdi mdi-plus me-1"></i> {{ __('Добавить компанию') }}
      </a>
    @endcan
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
          @can('create', \App\Models\Company::class)
            <a href="{{ route('admin.companies.create') }}" class="btn btn-primary mt-2">
              {{ __('Добавить первую компанию') }}
            </a>
          @endcan
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
                <th>{{ __('Срок') }}</th>
                <th class="text-end">{{ __('Действия') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($companies as $company)
                @php
                  $canUpdateCompany = auth()->user()?->can('update', $company);
                  $canDeleteCompany = auth()->user()?->can('delete', $company);
                @endphp
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
                    @if ($company->moderator || $company->accessUsers->count() > 0)
                      <div>
                        @if ($company->moderator)
                          <div class="d-flex align-items-center mb-1">
                            <div class="avatar avatar-xs me-2">
                              <img
                                src="{{ $company->moderator->avatar_url }}"
                                alt="{{ $company->moderator->name }}"
                                class="rounded-circle"
                              >
                            </div>
                            <strong>{{ $company->moderator->name }}</strong>
                            <small class="text-muted ms-1">({{ __('Главный') }})</small>
                          </div>
                        @endif
                        @if ($company->accessUsers->count() > 0)
                          @foreach ($company->accessUsers as $user)
                            <div class="d-flex align-items-center mb-1">
                              <div class="avatar avatar-xs me-2">
                                <img
                                  src="{{ $user->avatar_url }}"
                                  alt="{{ $user->name }}"
                                  class="rounded-circle"
                                >
                              </div>
                              {{ $user->name }}
                              @if($user->pivot->access_type === 'edit')
                                <small class="text-muted ms-1">({{ __('Редактирование') }})</small>
                              @else
                                <small class="text-muted ms-1">({{ __('Просмотр') }})</small>
                              @endif
                            </div>
                          @endforeach
                        @endif
                      </div>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td>
                    @if ($company->expiry_date)
                      <div>{{ __('До :date', ['date' => $company->expiry_date->format('d.m.Y')]) }}</div>
                      @php
                        $daysLeft = (int) now()->startOfDay()->diffInDays($company->expiry_date->endOfDay(), false);
                      @endphp
                      @if ($daysLeft < 0)
                        <span class="text-danger">{{ __('Срок истёк') }}</span>
                      @elseif ($daysLeft < 40)
                        <span class="text-danger">{{ trans_choice(':count день остался|:count дня осталось|:count дней осталось', $daysLeft, ['count' => $daysLeft]) }}</span>
                      @endif
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <div class="d-flex justify-content-end align-items-center gap-2">
                      @if ($canUpdateCompany && ! $company->hasLicenseDetails())
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
                          @if ($canUpdateCompany)
                            <a class="dropdown-item" href="{{ route('admin.companies.edit', $company) }}">
                              <i class="mdi mdi-pencil-outline me-1"></i> {{ __('Редактировать') }}
                            </a>
                          @endif
                          @if ($canUpdateCompany || $canDeleteCompany)
                            <div class="dropdown-divider"></div>
                          @endif
                          @if ($canDeleteCompany)
                            <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" 
                                  onsubmit="return confirm('{{ __('Вы уверены?') }}')">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="dropdown-item text-danger">
                                <i class="mdi mdi-trash-can-outline me-1"></i> {{ __('Удалить') }}
                              </button>
                            </form>
                          @endif
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

