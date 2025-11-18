@extends('layouts.admin')

@section('title', __('Мои компании'))

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">
      <span class="text-muted fw-light">{{ __('Управление') }} /</span> {{ __('Мои компании') }}
    </h4>
  </div>

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">{{ __('Выберите компании для отображения в меню') }}</h5>
      <small class="text-muted">{{ __('Выберите компании, которые будут отображаться в боковом меню. Если ничего не выбрано, будут показаны все доступные компании.') }}</small>
    </div>
    <div class="card-body">
      @if ($allCompanies->isEmpty())
        <div class="text-center py-5">
          <div class="mb-3">
            <i class="mdi mdi-briefcase-outline" style="font-size: 48px; color: #ccc;"></i>
          </div>
          <p class="text-muted">{{ __('У вас нет доступа ни к одной компании') }}</p>
        </div>
      @else
        <form id="menu-companies-form" method="POST" action="{{ route('admin.my-companies.update') }}">
          @csrf
          @method('PUT')

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <button type="button" class="btn btn-sm btn-outline-primary" id="select-all-btn">
                <i class="ti tabler-check me-1"></i> {{ __('Выбрать все') }}
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect-all-btn">
                <i class="ti tabler-x me-1"></i> {{ __('Снять все') }}
              </button>
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="ti tabler-device-floppy me-1"></i> {{ __('Сохранить настройки') }}
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th style="width: 50px;">
                    <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                  </th>
                  <th>{{ __('ID') }}</th>
                  <th>{{ __('Название') }}</th>
                  <th>{{ __('Страна') }}</th>
                  <th>{{ __('Модератор') }}</th>
                  <th>{{ __('Срок') }}</th>
                  <th class="text-end">{{ __('Действия') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($allCompanies as $company)
                  @php
                    $isSelected = in_array($company->id, $selectedCompanyIds);
                    $canView = $company->canUserView(auth()->user());
                  @endphp
                  <tr>
                    <td>
                      <input
                        type="checkbox"
                        name="company_ids[]"
                        value="{{ $company->id }}"
                        class="form-check-input company-checkbox"
                        id="company-{{ $company->id }}"
                        {{ $isSelected ? 'checked' : '' }}
                      >
                    </td>
                    <td><strong>#{{ $company->id }}</strong></td>
                    <td>
                      @if ($canView)
                        <a href="{{ route('admin.companies.show', $company) }}" class="text-body">
                          {{ $company->name }}
                        </a>
                      @else
                        <span class="text-body">{{ $company->name }}</span>
                      @endif
                    </td>
                    <td>
                      <span class="badge bg-label-secondary">{{ $company->country }}</span>
                    </td>
                    <td>
                      @if ($company->moderator)
                        <div class="d-flex align-items-center">
                          <div class="avatar avatar-xs me-2">
                            <img
                              src="{{ $company->moderator->avatar_url }}"
                              alt="{{ $company->moderator->name }}"
                              class="rounded-circle"
                            >
                          </div>
                          <div>
                            <div>{{ $company->moderator->name }}</div>
                            <small class="text-muted">{{ __('Главный модератор') }}</small>
                          </div>
                        </div>
                      @else
                        <span class="text-muted">—</span>
                      @endif
                    </td>
                    <td>
                      @if ($company->expiry_date)
                        @php
                          $daysLeft = (int) now()->startOfDay()->diffInDays($company->expiry_date->endOfDay(), false);
                        @endphp
                        @if ($daysLeft < 0)
                          <span class="text-danger">{{ __('Срок истёк') }}</span>
                        @else
                          <span class="{{ $daysLeft < 40 ? 'text-danger' : '' }}">{{ trans_choice(':count день остался|:count дня осталось|:count дней осталось', $daysLeft, ['count' => $daysLeft]) }}</span>
                        @endif
                      @else
                        <span class="text-muted">—</span>
                      @endif
                    </td>
                    <td class="text-end">
                      @if ($canView)
                        <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-sm btn-icon btn-outline-primary">
                          <i class="ti tabler-eye"></i>
                        </a>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            <small class="text-muted">
              {{ __('Выбрано:') }} <span id="selected-count">{{ count($selectedCompanyIds) }}</span> {{ __('из') }} {{ $allCompanies->count() }}
            </small>
          </div>
        </form>
      @endif
    </div>
  </div>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('vendor/vuexy/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endpush

@push('scripts')
  <script src="{{ asset('vendor/vuexy/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const selectAllCheckbox = document.getElementById('select-all-checkbox');
      const selectAllBtn = document.getElementById('select-all-btn');
      const deselectAllBtn = document.getElementById('deselect-all-btn');
      const companyCheckboxes = document.querySelectorAll('.company-checkbox');
      const selectedCountSpan = document.getElementById('selected-count');
      const form = document.getElementById('menu-companies-form');

      // Обновить счетчик выбранных
      function updateSelectedCount() {
        const checked = document.querySelectorAll('.company-checkbox:checked').length;
        selectedCountSpan.textContent = checked;
        
        // Обновить состояние "Выбрать все"
        if (checked === 0) {
          selectAllCheckbox.indeterminate = false;
          selectAllCheckbox.checked = false;
        } else if (checked === companyCheckboxes.length) {
          selectAllCheckbox.indeterminate = false;
          selectAllCheckbox.checked = true;
        } else {
          selectAllCheckbox.indeterminate = true;
        }
      }

      // Выбрать все
      selectAllCheckbox.addEventListener('change', function() {
        companyCheckboxes.forEach(checkbox => {
          checkbox.checked = this.checked;
        });
        updateSelectedCount();
      });

      selectAllBtn.addEventListener('click', function() {
        companyCheckboxes.forEach(checkbox => {
          checkbox.checked = true;
        });
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
        updateSelectedCount();
      });

      // Снять все
      deselectAllBtn.addEventListener('click', function() {
        companyCheckboxes.forEach(checkbox => {
          checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        updateSelectedCount();
      });

      // Обновление счетчика при изменении отдельных чекбоксов
      companyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
      });

      // Инициализация счетчика
      updateSelectedCount();

      // Обработка отправки формы
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const companyIds = Array.from(document.querySelectorAll('.company-checkbox:checked'))
          .map(checkbox => checkbox.value);

        // Очистить массив company_ids и добавить только выбранные
        formData.delete('company_ids[]');
        companyIds.forEach(id => {
          formData.append('company_ids[]', id);
        });

        // Показать индикатор загрузки
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti tabler-loader me-1"></i> {{ __('Сохранение...') }}';

        fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Показать уведомление об успехе
            if (typeof Swal !== 'undefined' && Swal.fire) {
              Swal.fire({
                icon: 'success',
                title: '{{ __('Успешно') }}',
                text: data.message || '{{ __('Настройки меню успешно обновлены.') }}',
                timer: 2000,
                showConfirmButton: false
              });
            } else {
              alert(data.message || '{{ __('Настройки меню успешно обновлены.') }}');
            }
          }
        })
        .catch(error => {
          console.error('Ошибка:', error);
          alert('{{ __('Произошла ошибка при сохранении настроек.') }}');
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        });
      });
    });
  </script>
@endpush

