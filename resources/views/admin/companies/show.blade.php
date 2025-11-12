@extends('layouts.admin')

@section('title', $company->name)

@push('styles')
  <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/libs/select2/select2.css') }}">
@endpush

@push('scripts')
  <script src="{{ url('public/vendor/vuexy/vendor/libs/select2/select2.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
          placeholder: '{{ __('Выберите из списка') }}',
          dropdownParent: $('#addAccessModal')
        });
      }
    });
  </script>
@endpush

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">
      <span class="text-muted fw-light">{{ __('Управление') }} / {{ __('Компании') }} /</span> {{ $company->name }}
    </h4>
    <div>
      <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-primary">
        <i class="mdi mdi-pencil-outline me-1"></i> {{ __('Редактировать') }}
      </a>
      <a href="{{ route('admin.companies.index') }}" class="btn btn-label-secondary">
        {{ __('К списку') }}
      </a>
    </div>
  </div>

  <div class="row">
    <!-- Левая колонка - Лицензия -->
    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Лицензия компании') }}</h5>
        </div>
        <div class="card-body d-flex flex-column h-100">
          @if ($company->license_file)
            <div class="text-center mb-4">
              <img src="{{ asset('storage/' . $company->license_file) }}" 
                   alt="{{ __('Лицензия') }}" 
                   class="img-fluid rounded mb-3"
                   style="max-height: 300px; cursor: pointer;"
                   data-bs-toggle="modal" 
                   data-bs-target="#licenseModal">
              <div class="d-grid gap-2">
                <a href="{{ asset('storage/' . $company->license_file) }}" 
                   class="btn btn-sm btn-primary" download>
                  <i class="mdi mdi-download me-1"></i> {{ __('Скачать') }}
                </a>
                <button type="button" class="btn btn-sm btn-label-primary" 
                        data-bs-toggle="modal" data-bs-target="#licenseModal">
                  <i class="mdi mdi-eye-outline me-1"></i> {{ __('Просмотр') }}
                </button>
              </div>
            </div>
          @else
            <div class="text-center text-muted py-5 mb-4 border rounded border-dashed">
              <i class="mdi mdi-file-document-outline" style="font-size: 48px;"></i>
              <p class="mt-2 mb-0">{{ __('Лицензия не загружена') }}</p>
            </div>
          @endif

          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0">{{ __('Детали компании') }}</h6>
              <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#companyLicenseModal">
                <i class="mdi mdi-pencil-outline me-1"></i> {{ $company->hasLicenseDetails() ? __('Редактировать') : __('Заполнить') }}
              </button>
            </div>

            @if ($company->hasLicenseDetails())
              <dl class="row mb-0">
                <dt class="col-sm-5 text-muted">{{ __('Номер лицензии:') }}</dt>
                <dd class="col-sm-7">{{ $company->license_number }}</dd>

                <dt class="col-sm-5 text-muted">{{ __('Номер регистрации:') }}</dt>
                <dd class="col-sm-7">{{ $company->registration_number }}</dd>

                <dt class="col-sm-5 text-muted">{{ __('Дата основания:') }}</dt>
                <dd class="col-sm-7">{{ optional($company->incorporation_date)->format('d.m.Y') ?? '—' }}</dd>

                <dt class="col-sm-5 text-muted">{{ __('Дата истечения:') }}</dt>
                <dd class="col-sm-7">{{ optional($company->expiry_date)->format('d.m.Y') ?? '—' }}</dd>

                <dt class="col-sm-5 text-muted">{{ __('Зона регистрации:') }}</dt>
                <dd class="col-sm-7">{{ $company->free_zone }}</dd>

                <dt class="col-sm-5 text-muted">{{ __('Виды деятельности:') }}</dt>
                <dd class="col-sm-7">{!! nl2br(e($company->business_activities)) !!}</dd>

                <dt class="col-sm-5 text-muted">{{ __('Юр. адрес:') }}</dt>
                <dd class="col-sm-7">{!! nl2br(e($company->legal_address)) !!}</dd>

                <dt class="col-sm-5 text-muted">{{ __('Факт. адрес:') }}</dt>
                <dd class="col-sm-7">{!! nl2br(e($company->actual_address)) !!}</dd>

                <dt class="col-sm-5 text-muted">{{ __('Имя владельца:') }}</dt>
                <dd class="col-sm-7">{{ $company->owner_name }}</dd>

                <dt class="col-sm-5 text-muted">{{ __('Email:') }}</dt>
                <dd class="col-sm-7">{{ $company->owner_email }}</dd>

                <dt class="col-sm-5 text-muted">{{ __('Телефон:') }}</dt>
                <dd class="col-sm-7">{{ $company->owner_phone }}</dd>

                <dt class="col-sm-5 text-muted">{{ __('Веб-сайт:') }}</dt>
                <dd class="col-sm-7">
                  @if ($company->owner_website)
                    <a href="{{ $company->owner_website }}" target="_blank" rel="noopener noreferrer">
                      {{ $company->owner_website }}
                    </a>
                  @else
                    —
                  @endif
                </dd>
              </dl>
            @else
              <div class="text-center text-muted py-5 border rounded border-dashed"
                   role="button"
                   data-bs-toggle="modal"
                   data-bs-target="#companyLicenseModal">
                <i class="mdi mdi-plus-circle-outline" style="font-size: 48px;"></i>
                <p class="mt-2 mb-0">{{ __('Заполните данные компании') }}</p>
                <p class="text-muted small mb-0">{{ __('Нажмите, чтобы добавить информацию о лицензии') }}</p>
              </div>
            @endif
          </div>

          <div class="mt-auto">
            <h6 class="mb-3">{{ __('Основная информация') }}</h6>
            <dl class="row mb-0">
              <dt class="col-sm-5 text-muted">{{ __('Страна:') }}</dt>
              <dd class="col-sm-7"><span class="badge bg-label-secondary">{{ $company->country }}</span></dd>
              
              <dt class="col-sm-5 text-muted">{{ __('Модератор:') }}</dt>
              <dd class="col-sm-7">{{ $company->moderator->name ?? '—' }}</dd>
              
              <dt class="col-sm-5 text-muted">{{ __('Создана:') }}</dt>
              <dd class="col-sm-7">{{ $company->created_at->format('d.m.Y H:i') }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- Правая колонка - Вкладки с данными -->
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#credentials-tab" 
                      type="button" role="tab">
                <i class="mdi mdi-key-variant me-1"></i> {{ __('Логины и пароли') }}
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bank-accounts-tab" 
                      type="button" role="tab">
                <i class="mdi mdi-bank me-1"></i> {{ __('Реквизиты') }}
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#access-tab" 
                      type="button" role="tab">
                <i class="mdi mdi-account-group me-1"></i> {{ __('Доступ') }}
              </button>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content p-0">
            <!-- Вкладка: Логины и пароли -->
            <div class="tab-pane fade show active" id="credentials-tab" role="tabpanel">
              <div class="alert alert-info" role="alert">
                <i class="mdi mdi-information-outline me-1"></i>
                {{ __('Эти данные видны только модератору компании и супер-администратору') }}
              </div>

              <form action="{{ route('admin.companies.credentials.store', $company) }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Логин') }}</label>
                    <input type="text" class="form-control" name="login" 
                           value="{{ $company->credentials->login ?? '' }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Логин ID') }}</label>
                    <input type="text" class="form-control" name="login_id" 
                           value="{{ $company->credentials->login_id ?? '' }}">
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">{{ __('Пароль') }}</label>
                  <input type="text" class="form-control" name="password" 
                         value="{{ $company->credentials->password ?? '' }}">
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Email') }}</label>
                    <input type="email" class="form-control" name="email" 
                           value="{{ $company->credentials->email ?? '' }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Пароль от email') }}</label>
                    <input type="text" class="form-control" name="email_password" 
                           value="{{ $company->credentials->email_password ?? '' }}">
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">{{ __('Ссылка на онлайн банкинг') }}</label>
                  <input type="url" class="form-control" name="online_banking_url" 
                         value="{{ $company->credentials->online_banking_url ?? '' }}">
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Имя менеджера') }}</label>
                    <input type="text" class="form-control" name="manager_name" 
                           value="{{ $company->credentials->manager_name ?? '' }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Номер телефона менеджера') }}</label>
                    <input type="text" class="form-control" name="manager_phone" 
                           value="{{ $company->credentials->manager_phone ?? '' }}">
                  </div>
                </div>

                <button type="submit" class="btn btn-primary">
                  <i class="mdi mdi-content-save me-1"></i> {{ __('Сохранить') }}
                </button>
              </form>
            </div>

            <!-- Вкладка: Банковские реквизиты -->
            <div class="tab-pane fade" id="bank-accounts-tab" role="tabpanel">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">{{ __('Банковские счета') }}</h6>
                <button type="button" class="btn btn-sm btn-primary" 
                        data-bs-toggle="modal" data-bs-target="#addBankAccountModal">
                  <i class="mdi mdi-plus me-1"></i> {{ __('Добавить счет') }}
                </button>
              </div>

              @forelse ($company->bankAccounts as $account)
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                      <div class="flex-grow-1">
                        <h6 class="mb-2">
                          <span class="badge bg-label-primary me-2">{{ $account->currency }}</span>
                          {{ $account->bank_name }}
                        </h6>
                        <dl class="row mb-0">
                          <dt class="col-sm-4 text-muted">{{ __('Страна:') }}</dt>
                          <dd class="col-sm-8">{{ $account->country }}</dd>
                          
                          <dt class="col-sm-4 text-muted">{{ __('Компания:') }}</dt>
                          <dd class="col-sm-8">{{ $account->company_name }}</dd>
                          
                          <dt class="col-sm-4 text-muted">{{ __('Номер счета:') }}</dt>
                          <dd class="col-sm-8"><code>{{ $account->account_number }}</code></dd>
                          
                          @if ($account->iban)
                            <dt class="col-sm-4 text-muted">{{ __('IBAN:') }}</dt>
                            <dd class="col-sm-8"><code>{{ $account->iban }}</code></dd>
                          @endif
                          
                          @if ($account->swift)
                            <dt class="col-sm-4 text-muted">{{ __('SWIFT:') }}</dt>
                            <dd class="col-sm-8"><code>{{ $account->swift }}</code></dd>
                          @endif
                        </dl>
                      </div>
                      <div class="ms-3 d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-icon btn-label-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editBankAccountModal{{ $account->id }}">
                          <i class="mdi mdi-pencil-outline"></i>
                        </button>
                        <form action="{{ route('admin.companies.bank-accounts.destroy', [$company, $account]) }}" 
                              method="POST" class="d-inline" 
                              onsubmit="return confirm('{{ __('Удалить этот счет?') }}')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-icon btn-label-danger">
                            <i class="mdi mdi-delete-outline"></i>
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Modal: Редактировать банковский счет -->
                <div class="modal fade" id="editBankAccountModal{{ $account->id }}" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <form action="{{ route('admin.companies.bank-accounts.update', [$company, $account]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                          <h5 class="modal-title">{{ __('Редактировать банковский счет') }}</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-md-6 mb-3">
                              <label class="form-label required">{{ __('Название банка') }}</label>
                              <input type="text" class="form-control" name="bank_name" 
                                     value="{{ old('bank_name', $account->bank_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                              <label class="form-label required">{{ __('Страна') }}</label>
                              <input type="text" class="form-control" name="country" 
                                     value="{{ old('country', $account->country) }}" required>
                            </div>
                          </div>

                          <div class="mb-3">
                            <label class="form-label required">{{ __('Название компании') }}</label>
                            <input type="text" class="form-control" name="company_name" 
                                   value="{{ old('company_name', $account->company_name) }}" required>
                          </div>

                          <div class="row">
                            <div class="col-md-4 mb-3">
                              <label class="form-label required">{{ __('Валюта') }}</label>
                              <select class="form-select" name="currency" required>
                                <option value="USD" {{ $account->currency === 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ $account->currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ $account->currency === 'GBP' ? 'selected' : '' }}>GBP</option>
                                <option value="AED" {{ $account->currency === 'AED' ? 'selected' : '' }}>AED</option>
                                <option value="CHF" {{ $account->currency === 'CHF' ? 'selected' : '' }}>CHF</option>
                              </select>
                            </div>
                            <div class="col-md-8 mb-3">
                              <label class="form-label required">{{ __('Номер счета') }}</label>
                              <input type="text" class="form-control" name="account_number" 
                                     value="{{ old('account_number', $account->account_number) }}" required>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6 mb-3">
                              <label class="form-label">{{ __('IBAN') }}</label>
                              <input type="text" class="form-control" name="iban" 
                                     value="{{ old('iban', $account->iban) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                              <label class="form-label">{{ __('SWIFT') }}</label>
                              <input type="text" class="form-control" name="swift" 
                                     value="{{ old('swift', $account->swift) }}">
                            </div>
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
              @empty
                <div class="text-center text-muted py-5">
                  <i class="mdi mdi-bank-outline" style="font-size: 48px;"></i>
                  <p class="mt-2">{{ __('Банковские счета еще не добавлены') }}</p>
                </div>
              @endforelse
            </div>

            <!-- Вкладка: Доступ пользователей -->
            <div class="tab-pane fade" id="access-tab" role="tabpanel">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">{{ __('Пользователи с доступом') }}</h6>
                <button type="button" class="btn btn-sm btn-primary" 
                        data-bs-toggle="modal" data-bs-target="#addAccessModal">
                  <i class="mdi mdi-plus me-1"></i> {{ __('Добавить доступ') }}
                </button>
              </div>

              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>{{ __('Пользователь') }}</th>
                      <th>{{ __('Email') }}</th>
                      <th>{{ __('Тип доступа') }}</th>
                      <th class="text-end">{{ __('Действия') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($company->accessUsers as $user)
                      <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                          @if ($user->pivot->access_type === 'edit')
                            <span class="badge bg-label-success">{{ __('Редактирование') }}</span>
                          @else
                            <span class="badge bg-label-info">{{ __('Просмотр') }}</span>
                          @endif
                        </td>
                        <td class="text-end">
                          <form action="{{ route('admin.companies.access.destroy', [$company, $user->pivot->id]) }}" 
                                method="POST" class="d-inline" onsubmit="return confirm('{{ __('Удалить доступ?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-icon btn-label-danger">
                              <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                          {{ __('Дополнительный доступ не настроен') }}
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Детали компании -->
<div class="modal fade" id="companyLicenseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('admin.companies.license.update', $company) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="form_type" value="license">
        <div class="modal-header">
          <h5 class="modal-title">{{ $company->hasLicenseDetails() ? __('Редактировать данные компании') : __('Заполнить данные компании') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="license_number" class="form-label required">{{ __('Номер лицензии') }}</label>
              <input type="text" class="form-control @error('license_number') is-invalid @enderror"
                     id="license_number" name="license_number"
                     value="{{ old('license_number', $company->license_number) }}" required>
              @error('license_number')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="registration_number" class="form-label required">{{ __('Номер регистрации') }}</label>
              <input type="text" class="form-control @error('registration_number') is-invalid @enderror"
                     id="registration_number" name="registration_number"
                     value="{{ old('registration_number', $company->registration_number) }}" required>
              @error('registration_number')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="incorporation_date" class="form-label required">{{ __('Дата основания') }}</label>
              <input type="date" class="form-control @error('incorporation_date') is-invalid @enderror"
                     id="incorporation_date" name="incorporation_date"
                     value="{{ old('incorporation_date', optional($company->incorporation_date)->format('Y-m-d')) }}" required>
              @error('incorporation_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="expiry_date" class="form-label required">{{ __('Дата истечения срока') }}</label>
              <input type="date" class="form-control @error('expiry_date') is-invalid @enderror"
                     id="expiry_date" name="expiry_date"
                     value="{{ old('expiry_date', optional($company->expiry_date)->format('Y-m-d')) }}" required>
              @error('expiry_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="free_zone" class="form-label required">{{ __('Зона открытия компании') }}</label>
            <input type="text" class="form-control @error('free_zone') is-invalid @enderror"
                   id="free_zone" name="free_zone"
                   value="{{ old('free_zone', $company->free_zone) }}" required>
            @error('free_zone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="business_activities" class="form-label required">{{ __('Виды деятельности') }}</label>
            <textarea class="form-control @error('business_activities') is-invalid @enderror"
                      id="business_activities" name="business_activities" rows="3" required>{{ old('business_activities', $company->business_activities) }}</textarea>
            @error('business_activities')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="legal_address" class="form-label required">{{ __('Юридический адрес') }}</label>
            <textarea class="form-control @error('legal_address') is-invalid @enderror"
                      id="legal_address" name="legal_address" rows="3" required>{{ old('legal_address', $company->legal_address) }}</textarea>
            @error('legal_address')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="actual_address" class="form-label required">{{ __('Фактический адрес') }}</label>
            <textarea class="form-control @error('actual_address') is-invalid @enderror"
                      id="actual_address" name="actual_address" rows="3" required>{{ old('actual_address', $company->actual_address) }}</textarea>
            @error('actual_address')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="owner_name" class="form-label required">{{ __('Имя владельца') }}</label>
              <input type="text" class="form-control @error('owner_name') is-invalid @enderror"
                     id="owner_name" name="owner_name"
                     value="{{ old('owner_name', $company->owner_name) }}" required>
              @error('owner_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="owner_email" class="form-label required">{{ __('Email владельца') }}</label>
              <input type="email" class="form-control @error('owner_email') is-invalid @enderror"
                     id="owner_email" name="owner_email"
                     value="{{ old('owner_email', $company->owner_email) }}" required>
              @error('owner_email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="owner_phone" class="form-label required">{{ __('Телефон владельца') }}</label>
              <input type="text" class="form-control @error('owner_phone') is-invalid @enderror"
                     id="owner_phone" name="owner_phone"
                     value="{{ old('owner_phone', $company->owner_phone) }}" required>
              @error('owner_phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="owner_website" class="form-label">{{ __('Веб-сайт') }}</label>
              <input type="url" class="form-control @error('owner_website') is-invalid @enderror"
                     id="owner_website" name="owner_website"
                     value="{{ old('owner_website', $company->owner_website) }}">
              @error('owner_website')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Отмена') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Просмотр лицензии -->
@if ($company->license_file)
<div class="modal fade" id="licenseModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Лицензия компании') }} — {{ $company->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img src="{{ asset('storage/' . $company->license_file) }}" 
             alt="{{ __('Лицензия') }}" 
             class="img-fluid">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
          {{ __('Закрыть') }}
        </button>
        <a href="{{ asset('storage/' . $company->license_file) }}" 
           class="btn btn-primary" download>
          <i class="mdi mdi-download me-1"></i> {{ __('Скачать') }}
        </a>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Modal: Добавить банковский счет -->
<div class="modal fade" id="addBankAccountModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('admin.companies.bank-accounts.store', $company) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Добавить банковский счет') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label required">{{ __('Название банка') }}</label>
              <input type="text" class="form-control" name="bank_name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label required">{{ __('Страна') }}</label>
              <input type="text" class="form-control" name="country" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label required">{{ __('Название компании') }}</label>
            <input type="text" class="form-control" name="company_name" 
                   value="{{ $company->name }}" required>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label required">{{ __('Валюта') }}</label>
              <select class="form-select" name="currency" required>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="GBP">GBP</option>
                <option value="AED">AED</option>
                <option value="CHF">CHF</option>
              </select>
            </div>
            <div class="col-md-8 mb-3">
              <label class="form-label required">{{ __('Номер счета') }}</label>
              <input type="text" class="form-control" name="account_number" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('IBAN') }}</label>
              <input type="text" class="form-control" name="iban">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('SWIFT') }}</label>
              <input type="text" class="form-control" name="swift">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
            {{ __('Отмена') }}
          </button>
          <button type="submit" class="btn btn-primary">
            {{ __('Добавить') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Добавить доступ -->
<div class="modal fade" id="addAccessModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.companies.access.store', $company) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Добавить доступ пользователю') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">{{ __('Пользователь') }}</label>
            <select class="form-select select2" name="user_id" required>
              <option value="">{{ __('Выберите пользователя') }}</option>
              @php
                $users = \App\Models\User::whereNotIn('id', $company->accessUsers->pluck('id'))
                  ->where('id', '!=', $company->moderator_id)
                  ->orderBy('name')
                  ->get();
              @endphp
              @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label required">{{ __('Тип доступа') }}</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="access_type" value="view" id="access_view" checked>
              <label class="form-check-label" for="access_view">
                {{ __('Просмотр') }} — <small class="text-muted">{{ __('только реквизиты, без логинов/паролей') }}</small>
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="access_type" value="edit" id="access_edit">
              <label class="form-check-label" for="access_edit">
                {{ __('Редактирование') }} — <small class="text-muted">{{ __('полный доступ ко всем данным') }}</small>
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
            {{ __('Отмена') }}
          </button>
          <button type="submit" class="btn btn-primary">
            {{ __('Добавить') }}
          </button>
        </div>
      </form>
    </div>
  </div>

@php
  $shouldOpenLicenseModal = (old('form_type') === 'license' && $errors->any()) || request()->boolean('open_license');
@endphp

@if ($shouldOpenLicenseModal)
  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const modalElement = document.getElementById('companyLicenseModal');
        if (modalElement && typeof bootstrap !== 'undefined') {
          const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
          modal.show();
        }
      });
    </script>
  @endpush
@endif

<style>
  .required:after {
    content: " *";
    color: red;
  }
</style>
@endsection

