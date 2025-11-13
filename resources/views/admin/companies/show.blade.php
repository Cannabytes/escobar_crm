@php
  use App\Models\BankDetail;
  use Illuminate\Support\Str;
@endphp

@extends('layouts.admin')

@php
  $canEditCompany = auth()->user()?->can('update', $company);
@endphp

@section('title', $company->name)

@push('styles')
  <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/libs/select2/select2.css') }}">
  <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endpush

@push('scripts')
  <script src="{{ url('public/vendor/vuexy/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{ url('public/vendor/vuexy/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof $ !== 'undefined' && $.fn.select2) {
        // Инициализация Select2 для модального окна
        $('#addAccessModal').on('shown.bs.modal', function() {
          const $select = $('#addAccessModal .select2');
          if (!$select.hasClass('select2-hidden-accessible')) {
            $select.select2({
              placeholder: '{{ __('Выберите модератора') }}',
              dropdownParent: $('#addAccessModal'),
              width: '100%',
              language: {
                noResults: function() {
                  return '{{ __('Модераторы не найдены') }}';
                }
              }
            });
          }
        });
        
        // Очистка Select2 при закрытии модального окна
        $('#addAccessModal').on('hidden.bs.modal', function() {
          const $select = $('#addAccessModal .select2');
          if ($select.hasClass('select2-hidden-accessible')) {
            $select.val('').trigger('change');
          }
        });
        
        // Инициализация Select2 для других элементов (если есть)
        $('.select2').not('#addAccessModal .select2').select2({
          placeholder: '{{ __('Выберите из списка') }}',
          width: '100%'
        });
      }
      
      // Отладка модального окна
      const addAccessModal = document.getElementById('addAccessModal');
      if (addAccessModal) {
        addAccessModal.addEventListener('show.bs.modal', function() {
          console.log('Modal is opening...');
        });
        addAccessModal.addEventListener('shown.bs.modal', function() {
          console.log('Modal is fully shown');
        });
      }

      // AJAX удаление лицензий
      document.querySelectorAll('.delete-license-btn, .delete-license-btn-modal').forEach(button => {
        button.addEventListener('click', async function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          const licenseId = this.getAttribute('data-license-id');
          const url = this.getAttribute('data-url');
          const licenseItem = document.getElementById('license-item-' + licenseId);
          
          // Подтверждение удаления
          let confirmed = false;
          
          try {
            if (typeof Swal !== 'undefined' && Swal.fire) {
              const result = await Swal.fire({
                title: '{{ __('Удалить лицензию?') }}',
                text: '{{ __('Это действие нельзя отменить') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('Да, удалить') }}',
                cancelButtonText: '{{ __('Отмена') }}',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
              });
              confirmed = result.isConfirmed;
            } else {
              confirmed = confirm('{{ __('Удалить лицензию?') }}');
            }
          } catch (error) {
            confirmed = confirm('{{ __('Удалить лицензию?') }}');
          }
          
          if (!confirmed) return;
          
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
          
          try {
            const response = await fetch(url, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              },
              credentials: 'same-origin'
            });
            
            const data = await response.json();
            
            if (response.ok && data.success !== false) {
              // Удаляем элемент с анимацией
              if (licenseItem) {
                licenseItem.style.transition = 'opacity 0.3s ease-out';
                licenseItem.style.opacity = '0';
                setTimeout(() => {
                  licenseItem.remove();
                  
                  // Если лицензий больше нет, перезагружаем страницу
                  const remainingLicenses = document.querySelectorAll('[id^="license-item-"]').length;
                  if (remainingLicenses === 0) {
                    location.reload();
                  }
                }, 300);
              }
              
              // Показываем уведомление
              if (typeof Swal !== 'undefined' && Swal.fire) {
                Swal.fire({
                  icon: 'success',
                  title: '{{ __('Успешно') }}',
                  text: data.message || '{{ __('Лицензия удалена') }}',
                  timer: 2000,
                  showConfirmButton: false,
                  toast: true,
                  position: 'top-end'
                });
              }
            } else {
              throw new Error(data.message || '{{ __('Ошибка при удалении лицензии') }}');
            }
          } catch (error) {
            console.error('Ошибка при удалении лицензии:', error);
            
            if (typeof Swal !== 'undefined' && Swal.fire) {
              Swal.fire({
                icon: 'error',
                title: '{{ __('Ошибка') }}',
                text: error.message || '{{ __('Ошибка при удалении лицензии') }}'
              });
            } else {
              alert(error.message || '{{ __('Ошибка при удалении лицензии') }}');
            }
          }
        });
      });
      
      // Предпросмотр изображений перед загрузкой
      const fileInput = document.querySelector('input[name="license_files[]"]');
      if (fileInput) {
        fileInput.addEventListener('change', function(e) {
          const previewContainer = document.getElementById('preview-container');
          previewContainer.innerHTML = '';
          
          const files = Array.from(e.target.files);
          files.forEach(file => {
            if (file.type.startsWith('image/')) {
              const reader = new FileReader();
              reader.onload = function(event) {
                const col = document.createElement('div');
                col.className = 'col-4';
                col.innerHTML = `
                  <img src="${event.target.result}" 
                       class="img-fluid rounded" 
                       style="width: 100%; height: 80px; object-fit: cover;">
                `;
                previewContainer.appendChild(col);
              };
              reader.readAsDataURL(file);
            }
          });
        });
      }

      // AJAX удаление реквизитов банка
      document.querySelectorAll('.delete-bank-detail-btn').forEach(button => {
        button.addEventListener('click', async function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          const detailId = this.getAttribute('data-detail-id');
          const bankId = this.getAttribute('data-bank-id');
          const url = this.getAttribute('data-url');
          const row = document.getElementById('bank-detail-row-' + detailId);
          const buttonElement = this;
          
          // Подтверждение удаления через SweetAlert2 или confirm
          let confirmed = false;
          
          try {
            // Проверяем наличие SweetAlert2
            if (typeof Swal !== 'undefined' && Swal.fire) {
              const result = await Swal.fire({
                title: '{{ __('Удалить реквизит?') }}',
                text: '{{ __('Это действие нельзя отменить') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('Да, удалить') }}',
                cancelButtonText: '{{ __('Отмена') }}',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
              });
              confirmed = result.isConfirmed;
            } else {
              // Fallback на обычный confirm
              confirmed = confirm('{{ __('Удалить реквизит?') }}');
            }
          } catch (error) {
            console.error('Ошибка при показе диалога подтверждения:', error);
            // Fallback на обычный confirm при ошибке
            confirmed = confirm('{{ __('Удалить реквизит?') }}');
          }
          
          // Блокируем кнопку во время запроса
          const originalHTML = buttonElement.innerHTML;
          const originalDisabled = buttonElement.disabled;
          buttonElement.disabled = true;
          buttonElement.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

          // Получаем CSRF токен
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
          
          if (!csrfToken) {
            console.error('CSRF токен не найден');
            buttonElement.disabled = originalDisabled;
            buttonElement.innerHTML = originalHTML;
            if (typeof Swal !== 'undefined' && Swal.fire) {
              Swal.fire({
                icon: 'error',
                title: '{{ __('Ошибка') }}',
                text: '{{ __('Ошибка безопасности. Обновите страницу.') }}'
              });
            } else {
              alert('{{ __('Ошибка безопасности. Обновите страницу.') }}');
            }
            return;
          }
          
          try {
            // Отправляем AJAX-запрос
            const response = await fetch(url, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              },
              credentials: 'same-origin'
            });
            
            // Пытаемся получить JSON ответ
            let data;
            try {
              data = await response.json();
            } catch (e) {
              // Если ответ не JSON, создаем объект с сообщением
              data = {
                success: false,
                message: response.status === 404 
                  ? '{{ __('Реквизит не найден') }}'
                  : '{{ __('Ошибка при удалении реквизита') }}'
              };
            }
            
            if (!response.ok) {
              throw new Error(data.message || data.error || '{{ __('Ошибка при удалении реквизита') }}');
            }
            
            if (data.success !== false) {
              // Удаляем строку из таблицы с анимацией
              if (row) {
                row.style.transition = 'opacity 0.3s ease-out';
                row.style.opacity = '0';
                setTimeout(() => {
                  row.remove();
                  
                  // Обновляем счетчик реквизитов в заголовке банка
                  const tbody = row.closest('tbody');
                  const remainingCount = tbody ? tbody.querySelectorAll('tr').length : 0;
                  const countBadge = document.getElementById('bank-details-count-' + bankId);
                  
                  if (countBadge) {
                    if (remainingCount > 0) {
                      countBadge.innerHTML = '<i class="icon-base ti tabler-files me-1"></i>' + remainingCount;
                    } else {
                      countBadge.remove();
                    }
                  }
                  
                  // Проверяем, остались ли реквизиты в таблице
                  if (tbody && tbody.querySelectorAll('tr').length === 0) {
                    // Если реквизитов не осталось, показываем сообщение
                    const table = tbody.closest('.table-responsive');
                    if (table) {
                      const emptyMessage = document.createElement('div');
                      emptyMessage.className = 'text-center text-muted py-4';
                      emptyMessage.innerHTML = '<i class="icon-base ti tabler-inbox"></i><p class="mt-2 mb-0">{{ __('Реквизиты не добавлены') }}</p>';
                      table.innerHTML = '';
                      table.appendChild(emptyMessage);
                    }
                  }
                }, 300);
              }

              // Показываем уведомление об успехе
              if (typeof Swal !== 'undefined' && Swal.fire) {
                Swal.fire({
                  icon: 'success',
                  title: '{{ __('Успешно') }}',
                  text: data.message || '{{ __('Реквизит удален') }}',
                  timer: 2000,
                  showConfirmButton: false,
                  toast: true,
                  position: 'top-end'
                });
              } else if (typeof toastr !== 'undefined') {
                toastr.success(data.message || '{{ __('Реквизит удален') }}');
              } else {
                alert(data.message || '{{ __('Реквизит удален') }}');
              }
            } else {
              throw new Error(data.message || '{{ __('Ошибка при удалении реквизита') }}');
            }
          } catch (error) {
            console.error('Ошибка при удалении реквизита:', error);
            
            // Восстанавливаем кнопку
            buttonElement.disabled = originalDisabled;
            buttonElement.innerHTML = originalHTML;
            
            // Показываем ошибку
            const errorMessage = error.message || '{{ __('Ошибка при удалении реквизита') }}';
            
            if (typeof Swal !== 'undefined' && Swal.fire) {
              Swal.fire({
                icon: 'error',
                title: '{{ __('Ошибка') }}',
                text: errorMessage
              });
            } else if (typeof toastr !== 'undefined') {
              toastr.error(errorMessage);
            } else {
              alert(errorMessage);
            }
          }
        });
      });
    });
  </script>
@endpush

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">
      <span class="text-muted fw-light">{{ __('Управление') }} / {{ __('Компании') }} /</span> {{ $company->name }}
    </h4>
    <div class="d-flex gap-2">
      @if($canEditCompany)
        <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-primary">
          <i class="icon-base ti tabler-pencil me-1"></i> {{ __('Редактировать') }}
        </a>
      @endif
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
          @if ($company->licenses->count() > 0)
            <!-- Галерея лицензий -->
            <div class="mb-4">
              <div class="row g-2">
                @foreach($company->licenses as $license)
                  <div class="col-6 col-md-4" id="license-item-{{ $license->id }}">
                    <div class="position-relative license-image-wrapper">
                      <img src="{{ url('public/storage/' . $license->file_path) }}" 
                           alt="{{ __('Лицензия') }}" 
                           class="img-fluid rounded cursor-pointer license-thumbnail"
                           style="width: 100%; height: 150px; object-fit: cover;"
                           data-bs-toggle="modal" 
                           data-bs-target="#licenseModal{{ $license->id }}">
                      
                      @if($canEditCompany)
                        <button type="button" 
                                class="btn btn-sm btn-danger position-absolute delete-license-btn"
                                style="top: 5px; right: 5px; padding: 2px 6px; opacity: 0.9;"
                                data-license-id="{{ $license->id }}"
                                data-url="{{ route('admin.companies.licenses.destroy', [$company, $license]) }}"
                                title="{{ __('Удалить') }}">
                          <i class="ti tabler-trash" style="font-size: 0.9rem;"></i>
                        </button>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
            
            @if($canEditCompany)
              <div class="d-grid mb-4">
                <button type="button" class="btn btn-sm btn-primary" 
                        data-bs-toggle="modal" data-bs-target="#uploadLicenseModal">
                  <i class="icon-base ti tabler-upload me-1"></i> {{ __('Загрузить ещё') }}
                </button>
              </div>
            @endif
          @else
            <div class="text-center text-muted py-5 mb-4 border rounded border-dashed">
              <i class="icon-base ti tabler-file-text" style="font-size: 48px;"></i>
              <p class="mt-2 mb-0">{{ __('Лицензии не загружены') }}</p>
              @if($canEditCompany)
                <button type="button" class="btn btn-sm btn-primary mt-3" 
                        data-bs-toggle="modal" data-bs-target="#uploadLicenseModal">
                  <i class="icon-base ti tabler-upload me-1"></i> {{ __('Загрузить лицензию') }}
                </button>
              @endif
            </div>
          @endif

          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0">{{ __('Детали компании') }}</h6>
              @if($canEditCompany)
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#companyLicenseModal">
                  <i class="icon-base ti tabler-pencil me-1"></i> {{ $company->hasLicenseDetails() ? __('Редактировать') : __('Заполнить') }}
                </button>
              @endif
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
              @if($canEditCompany)
                <div class="text-center text-muted py-5 border rounded border-dashed"
                     role="button"
                     data-bs-toggle="modal"
                     data-bs-target="#companyLicenseModal">
                  <i class="ti tabler-circle-plus" style="font-size: 48px;"></i>
                  <p class="mt-2 mb-0">{{ __('Заполните данные компании') }}</p>
                  <p class="text-muted small mb-0">{{ __('Нажмите, чтобы добавить информацию о лицензии') }}</p>
                </div>
              @else
                <div class="text-center text-muted py-5 border rounded border-dashed">
                  <i class="ti tabler-info-circle" style="font-size: 48px;"></i>
                  <p class="mt-2 mb-0">{{ __('Данные компании пока не заполнены.') }}</p>
                  <p class="text-muted small mb-0">{{ __('Обратитесь к модератору или супер-администратору для обновления информации.') }}</p>
                </div>
              @endif
            @endif
          </div>

          <div class="mt-auto">
            <h6 class="mb-3">{{ __('Основная информация') }}</h6>
            <dl class="row mb-0">
              <dt class="col-sm-5 text-muted">{{ __('Страна:') }}</dt>
              <dd class="col-sm-7"><span class="badge bg-label-secondary">{{ $company->country }}</span></dd>
              
              <dt class="col-sm-5 text-muted">{{ __('Главный модератор:') }}</dt>
              <dd class="col-sm-7">{{ $company->moderator->name ?? '—' }}</dd>
              
              @if($company->accessUsers->count() > 0)
                <dt class="col-sm-5 text-muted">{{ __('Доп. модераторы:') }}</dt>
                <dd class="col-sm-7">
                  <span class="badge bg-label-info">{{ $company->accessUsers->count() }}</span>
                </dd>
              @endif
              
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
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#bank-accounts-tab" 
                      type="button" role="tab">
                <i class="icon-base ti tabler-building-bank me-1"></i> {{ __('Банки и реквизиты') }}
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#access-tab" 
                      type="button" role="tab">
                <i class="icon-base ti tabler-users me-1"></i> {{ __('Доступ') }}
              </button>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content p-0">
            <!-- Вкладка: Банки и реквизиты (новая структура) -->
            <div class="tab-pane fade show active" id="bank-accounts-tab" role="tabpanel">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0">{{ __('Банки и реквизиты') }}</h6>
                @if($canEditCompany)
                  <button type="button" class="btn btn-sm btn-primary" 
                          data-bs-toggle="modal" data-bs-target="#addBankModal">
                    <i class="icon-base ti tabler-plus me-1"></i> {{ __('Добавить банк') }}
                  </button>
                @endif
              </div>

              <div class="alert alert-info" role="alert">
                <i class="icon-base ti tabler-info-circle me-1"></i>
                {{ __('Структура: Банк → Реквизиты. Для одного банка можно добавить несколько реквизитов (счета, IBAN, SWIFT и т.д.)') }}
              </div>

            @forelse ($company->banks as $bank)
              @php
                $authUser = auth()->user();
                $canManageBank = $authUser?->can('update', $bank);
                $canViewBankCredentials = $authUser ? $company->canUserViewCredentials($authUser) : false;
                $credentialItems = collect([
                  ['key' => 'login', 'label' => __('Логин'), 'value' => $bank->login],
                  ['key' => 'login_id', 'label' => __('Логин ID'), 'value' => $bank->login_id],
                  ['key' => 'password', 'label' => __('Пароль'), 'value' => $bank->password],
                  ['key' => 'email', 'label' => __('Email'), 'value' => $bank->email],
                  ['key' => 'email_password', 'label' => __('Пароль от email'), 'value' => $bank->email_password],
                  ['key' => 'online_banking_url', 'label' => __('Ссылка на онлайн-банк'), 'value' => $bank->online_banking_url],
                  ['key' => 'manager_name', 'label' => __('Менеджер'), 'value' => $bank->manager_name],
                  ['key' => 'manager_phone', 'label' => __('Телефон менеджера'), 'value' => $bank->manager_phone],
                ]);
                $hasCredentialData = $credentialItems->contains(fn ($item) => filled($item['value']));
              @endphp
              <div class="card mb-3">
                  <div class="card-header p-3 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#bank-{{ $bank->id }}">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="mb-1">
                      <i class="icon-base ti tabler-building-bank me-2"></i>
                      <strong>{{ $bank->name }}</strong>
                      @if ($bank->country)
                        <span class="badge bg-label-secondary ms-2">{{ $bank->country }}</span>
                      @endif
                      @if ($bank->details->count())
                        <span class="badge bg-label-info ms-2" id="bank-details-count-{{ $bank->id }}">
                          <i class="icon-base ti tabler-files me-1"></i>{{ $bank->details->count() }}
                        </span>
                      @endif
                      @if($hasCredentialData)
                        <span class="badge bg-label-success ms-2">
                          <i class="icon-base ti tabler-key me-1"></i>{{ __('Доступы заполнены') }}
                        </span>
                      @endif
                    </h6>
                    @if ($bank->notes)
                      <small class="text-muted">{{ Str::limit($bank->notes, 120) }}</small>
                    @endif
                  </div>
                  @if($canManageBank)
                    <div class="d-flex gap-2 ms-3">
                          <button type="button" class="btn btn-sm btn-icon btn-label-success" 
                                  data-bs-toggle="modal" 
                                  data-bs-target="#addBankDetailModal{{ $bank->id }}"
                                  onclick="event.stopPropagation()">
                            <i class="icon-base ti tabler-circle-plus"></i>
                          </button>
                          <button type="button" class="btn btn-sm btn-icon btn-label-primary" 
                                  data-bs-toggle="modal" 
                                  data-bs-target="#editBankModal{{ $bank->id }}"
                                  onclick="event.stopPropagation()">
                            <i class="ti tabler-pencil"></i>
                          </button>
                          <form action="{{ route('admin.companies.banks.destroy', [$company, $bank]) }}" 
                                method="POST" class="d-inline" 
                                onsubmit="return confirm('{{ __('Удалить банк со всеми реквизитами?') }}')"
                                onclick="event.stopPropagation()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-icon btn-label-danger">
                              <i class="icon-base ti tabler-trash"></i>
                            </button>
                          </form>
                        </div>
                      @endif
                    </div>
                  </div>

                  <!-- Реквізити банку (з разворотом/згортанням) -->
                <div class="collapse" id="bank-{{ $bank->id }}">
                  <div class="card-body">
                    <div class="mb-4">
                      <div class="border rounded p-3">
                        <div class="d-flex align-items-center mb-3">
                          <i class="icon-base ti tabler-key me-2 text-primary"></i>
                          <h6 class="mb-0">{{ __('Доступ к онлайн-банку') }}</h6>
                        </div>
                        @if($canViewBankCredentials)
                          @if($hasCredentialData)
                            @php
                              $itemsArray = $credentialItems->filter(fn($item) => filled($item['value']))->values()->all();
                              $halfCount = ceil(count($itemsArray) / 2);
                              $leftColumn = array_slice($itemsArray, 0, $halfCount);
                              $rightColumn = array_slice($itemsArray, $halfCount);
                            @endphp
                            <div class="row">
                              <div class="col-md-6">
                                @foreach ($leftColumn as $item)
                                  <div class="mb-3">
                                    <small class="text-muted d-block mb-1">{{ $item['label'] }}</small>
                                    <div class="fw-medium">
                                      @switch($item['key'])
                                        @case('online_banking_url')
                                          <a href="{{ $item['value'] }}" target="_blank" rel="noopener" class="text-break">
                                            {{ $item['value'] }}
                                            <i class="icon-base ti tabler-external-link ms-1"></i>
                                          </a>
                                          @break
                                        @case('email')
                                          <a href="mailto:{{ $item['value'] }}">{{ $item['value'] }}</a>
                                          @break
                                        @case('manager_phone')
                                          <a href="tel:{{ preg_replace('/\D+/', '', $item['value']) }}">{{ $item['value'] }}</a>
                                          @break
                                        @default
                                          <span class="font-monospace text-break">{{ $item['value'] }}</span>
                                      @endswitch
                                    </div>
                                  </div>
                                @endforeach
                              </div>
                              <div class="col-md-6">
                                @foreach ($rightColumn as $item)
                                  <div class="mb-3">
                                    <small class="text-muted d-block mb-1">{{ $item['label'] }}</small>
                                    <div class="fw-medium">
                                      @switch($item['key'])
                                        @case('online_banking_url')
                                          <a href="{{ $item['value'] }}" target="_blank" rel="noopener" class="text-break">
                                            {{ $item['value'] }}
                                            <i class="icon-base ti tabler-external-link ms-1"></i>
                                          </a>
                                          @break
                                        @case('email')
                                          <a href="mailto:{{ $item['value'] }}">{{ $item['value'] }}</a>
                                          @break
                                        @case('manager_phone')
                                          <a href="tel:{{ preg_replace('/\D+/', '', $item['value']) }}">{{ $item['value'] }}</a>
                                          @break
                                        @default
                                          <span class="font-monospace text-break">{{ $item['value'] }}</span>
                                      @endswitch
                                    </div>
                                  </div>
                                @endforeach
                              </div>
                            </div>
                          @else
                            <p class="text-muted small mb-0">{{ __('Данные не заполнены') }}</p>
                          @endif
                        @else
                          <div class="alert alert-warning small mb-0">
                            <i class="icon-base ti табler-lock me-1"></i>{{ __('У вас нет доступа к данным этого банка.') }}
                          </div>
                        @endif
                      </div>
                    </div>

                    @if ($bank->details->count())
                        <div class="table-responsive">
                          <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                              <tr>
                                <th>{{ __('Валюта') }}</th>
                                <th>{{ __('ACCOUNT') }}</th>
                                <th>{{ __('IBAN') }}</th>
                                <th>{{ __('SWIFT') }}</th>
                                <th>{{ __('Статус') }}</th>
                                @if($canManageBank)
                                  <th class="text-end">{{ __('Действия') }}</th>
                                @endif
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($bank->details as $detail)
                                <tr id="bank-detail-row-{{ $detail->id }}">
                                  <td>{{ $detail->currency ?? '—' }}</td>
                                  <td>{{ $detail->account_number ?? '—' }}</td>
                                  <td>{{ $detail->iban ?? '—' }}</td>
                                  <td>{{ $detail->swift ?? '—' }}</td>
                                  <td>
                                    @php
                                      $statusColors = [
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'hold' => 'warning',
                                        'closed' => 'danger',
                                      ];
                                      $statusColor = $statusColors[$detail->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-label-{{ $statusColor }}">
                                      {{ BankDetail::getStatuses()[$detail->status] ?? $detail->status }}
                                    </span>
                                  </td>
                                  @if($canManageBank)
                                    <td class="text-end">
                                      <button type="button" class="btn btn-sm btn-icon btn-label-primary" 
                                              data-bs-toggle="modal" 
                                              data-bs-target="#editBankDetailModal{{ $detail->id }}"
                                              title="{{ __('Редактировать') }}">
                                        <i class="ti tabler-pencil"></i>
                                      </button>
                                      <button type="button" 
                                              class="btn btn-sm btn-icon btn-label-danger waves-effect delete-bank-detail-btn" 
                                              data-detail-id="{{ $detail->id }}"
                                              data-bank-id="{{ $bank->id }}"
                                              data-company-id="{{ $company->id }}"
                                              data-url="{{ route('admin.companies.bank-details.destroy', [$company, $detail]) }}"
                                              title="{{ __('Удалить') }}">
                                        <i class="icon-base ti tabler-trash"></i>
                                      </button>
                                    </td>
                                  @endif
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      @else
                        <div class="text-center text-muted py-4">
                          <i class="icon-base ti tabler-inbox"></i>
                          <p class="mt-2 mb-0">{{ __('Реквизиты не добавлены') }}</p>
                        </div>
                      @endif
                    </div>
                  </div>

                @if($canManageBank)
                    <!-- Модал: Редагування банку -->
                  <div class="modal fade" id="editBankModal{{ $bank->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <form action="{{ route('admin.companies.banks.update', [$company, $bank]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                              <h5 class="modal-title">{{ __('Редактировать банк') }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <div class="mb-3">
                                <label class="form-label required">{{ __('Название банка') }}</label>
                                <input type="text" class="form-control" name="name" 
                                       value="{{ old('name', $bank->name) }}" required>
                              </div>

                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">{{ __('Страна') }}</label>
                                  <input type="text" class="form-control" name="country" 
                                         value="{{ old('country', $bank->country) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">{{ __('Код банка') }}</label>
                                  <input type="text" class="form-control" name="bank_code" 
                                         value="{{ old('bank_code', $bank->bank_code) }}" placeholder="MFI, SWIFT">
                                </div>
                              </div>

                              <div class="mb-3">
                                <label class="form-label">{{ __('Примечания') }}</label>
                                <textarea class="form-control" name="notes" rows="3">{{ old('notes', $bank->notes) }}</textarea>
                              </div>

                              <hr>
                              <h6 class="text-uppercase text-muted small mb-3">{{ __('Доступы и контакты') }}</h6>
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">{{ __('Логин') }}</label>
                                  <input type="text" class="form-control" name="login" value="{{ old('login', $bank->login) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">{{ __('Логин ID') }}</label>
                                  <input type="text" class="form-control" name="login_id" value="{{ old('login_id', $bank->login_id) }}">
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">{{ __('Пароль') }}</label>
                                  <input type="text" class="form-control" name="password" value="{{ old('password', $bank->password) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">{{ __('Email') }}</label>
                                  <input type="email" class="form-control" name="email" value="{{ old('email', $bank->email) }}">
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">{{ __('Пароль от email') }}</label>
                                  <input type="text" class="form-control" name="email_password" value="{{ old('email_password', $bank->email_password) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">{{ __('Ссылка на онлайн-банк') }}</label>
                                  <input type="url" class="form-control" name="online_banking_url" 
                                         value="{{ old('online_banking_url', $bank->online_banking_url) }}">
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">{{ __('Имя менеджера') }}</label>
                                  <input type="text" class="form-control" name="manager_name" 
                                         value="{{ old('manager_name', $bank->manager_name) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label class="form-label">{{ __('Телефон менеджера') }}</label>
                                  <input type="text" class="form-control" name="manager_phone" 
                                         value="{{ old('manager_phone', $bank->manager_phone) }}">
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

                    <!-- Модал: Добавление реквізиту банку -->
                    <div class="modal fade" id="addBankDetailModal{{ $bank->id }}" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form action="{{ route('admin.companies.bank-details.store', [$company, $bank]) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                              <h5 class="modal-title">{{ __('Добавить реквизит для') }} <strong>{{ $bank->name }}</strong></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <div class="mb-3">
                                <label class="form-label">{{ __('Валюта') }}</label>
                                <select class="form-select" name="currency">
                                  <option value="">— {{ __('Не указано') }} —</option>
                                  <option value="USD">USD</option>
                                  <option value="EUR">EUR</option>
                                  <option value="GBP">GBP</option>
                                  <option value="AED">AED</option>
                                  <option value="CHF">CHF</option>
                                </select>
                              </div>

                              <div class="mb-3">
                                <label class="form-label">{{ __('Статус') }}</label>
                                <select class="form-select" name="status">
                                  @foreach (BankDetail::getStatuses() as $key => $label)
                                    <option value="{{ $key }}" {{ $key === 'active' ? 'selected' : '' }}>{{ $label }}</option>
                                  @endforeach
                                </select>
                              </div>

                              <div class="mb-3">
                                <label class="form-label">{{ __('ACCOUNT') }}</label>
                                <input type="text" class="form-control" name="account_number" 
                                       value="{{ old('account_number') }}" 
                                       placeholder="{{ __('Введите номер счета') }}">
                              </div>

                              <div class="mb-3">
                                <label class="form-label">{{ __('IBAN') }}</label>
                                <input type="text" class="form-control" name="iban" 
                                       value="{{ old('iban') }}" 
                                       placeholder="{{ __('Введите IBAN') }}">
                              </div>

                              <div class="mb-3">
                                <label class="form-label">{{ __('SWIFT') }}</label>
                                <input type="text" class="form-control" name="swift" 
                                       value="{{ old('swift') }}" 
                                       placeholder="{{ __('Введите SWIFT код') }}">
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
                  @endif
                </div>

                @if($canEditCompany)
                  <!-- Модалі редагування реквізитів -->
                  @foreach ($bank->details as $detail)
                    <div class="modal fade" id="editBankDetailModal{{ $detail->id }}" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form action="{{ route('admin.companies.bank-details.update', [$company, $detail]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                              <h5 class="modal-title">{{ __('Редактировать реквизит') }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <div class="mb-3">
                                <label class="form-label">{{ __('Валюта') }}</label>
                                <select class="form-select" name="currency">
                                  <option value="">— {{ __('Не указано') }} —</option>
                                  <option value="USD" {{ $detail->currency === 'USD' ? 'selected' : '' }}>USD</option>
                                  <option value="EUR" {{ $detail->currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                                  <option value="GBP" {{ $detail->currency === 'GBP' ? 'selected' : '' }}>GBP</option>
                                  <option value="AED" {{ $detail->currency === 'AED' ? 'selected' : '' }}>AED</option>
                                  <option value="CHF" {{ $detail->currency === 'CHF' ? 'selected' : '' }}>CHF</option>
                                </select>
                              </div>

                              <div class="mb-3">
                                <label class="form-label">{{ __('Статус') }}</label>
                                <select class="form-select" name="status">
                                  @foreach (BankDetail::getStatuses() as $key => $label)
                                    <option value="{{ $key }}" {{ $detail->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                  @endforeach
                                </select>
                              </div>

                              <div class="mb-3">
                                <label class="form-label">{{ __('ACCOUNT') }}</label>
                                <input type="text" class="form-control" name="account_number" 
                                       value="{{ old('account_number', $detail->account_number) }}" 
                                       placeholder="{{ __('Введите номер счета') }}">
                              </div>

                              <div class="mb-3">
                                <label class="form-label">{{ __('IBAN') }}</label>
                                <input type="text" class="form-control" name="iban" 
                                       value="{{ old('iban', $detail->iban) }}" 
                                       placeholder="{{ __('Введите IBAN') }}">
                              </div>

                              <div class="mb-3">
                                <label class="form-label">{{ __('SWIFT') }}</label>
                                <input type="text" class="form-control" name="swift" 
                                       value="{{ old('swift', $detail->swift) }}" 
                                       placeholder="{{ __('Введите SWIFT код') }}">
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
                @endif
              @empty
                <div class="text-center py-6">
                  <i class="icon-base ti tabler-building-bank" style="font-size: 64px; color: #ccc;"></i>
                  <p class="mt-3 text-muted">{{ __('Банки и реквизиты еще не добавлены') }}</p>
                  <p class="small text-muted">{{ __('Нажмите кнопку выше, чтобы добавить первый банк') }}</p>
                </div>
              @endforelse
            </div>

            <!-- Вкладка: Доступ модераторов -->
            <div class="tab-pane fade" id="access-tab" role="tabpanel">
              <div class="alert alert-info mb-3">
                <i class="icon-base ti tabler-info-circle me-1"></i>
                {{ __('В этом разделе отображаются все модераторы компании: главный и дополнительные') }}
              </div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                @php
                  $totalModerators = ($company->moderator ? 1 : 0) + $company->accessUsers->count();
                @endphp
                <h6 class="mb-0">{{ __('Модераторы с доступом') }} ({{ $totalModerators }})</h6>
                @if($canEditCompany)
                  <button type="button" class="btn btn-sm btn-primary" 
                          data-bs-toggle="modal" data-bs-target="#addAccessModal">
                    <i class="icon-base ti tabler-plus me-1"></i> {{ __('Добавить модератора') }}
                  </button>
                @endif
              </div>

              @if($company->moderator || $company->accessUsers->count() > 0)
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead class="table-light">
                      <tr>
                        <th>{{ __('Модератор') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Тип доступа') }}</th>
                        <th>{{ __('Добавлен') }}</th>
                        @if($canEditCompany)
                          <th class="text-end">{{ __('Действия') }}</th>
                        @endif
                      </tr>
                    </thead>
                    <tbody>
                      @if($company->moderator)
                        <tr>
                          <td>
                            <div class="d-flex align-items-center">
                              @if ($company->moderator->avatar)
                                <img src="{{ $company->moderator->avatar_url }}" alt="{{ $company->moderator->name }}" 
                                     class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                              @else
                                <div class="avatar-initial rounded-circle bg-label-primary me-2" 
                                     style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                  {{ strtoupper(substr($company->moderator->name, 0, 1)) }}
                                </div>
                              @endif
                              <div>
                                <strong>{{ $company->moderator->name }}</strong>
                                <span class="badge bg-label-warning ms-1">{{ __('Главный') }}</span>
                                @if($company->moderator->id === auth()->id())
                                  <span class="badge bg-label-info ms-1">{{ __('Вы') }}</span>
                                @endif
                              </div>
                            </div>
                          </td>
                          <td>{{ $company->moderator->email }}</td>
                          <td>
                            <span class="badge bg-label-success">
                              <i class="icon-base ti tabler-pencil me-1"></i>{{ __('Полный доступ') }}
                            </span>
                          </td>
                          <td>
                            <small class="text-muted">{{ $company->created_at->format('d.m.Y H:i') }}</small>
                          </td>
                          @if($canEditCompany)
                            <td class="text-end">
                              @if(auth()->user()?->role === \App\Models\User::ROLE_SUPER_ADMIN)
                                <form action="{{ route('admin.companies.moderator.remove', $company) }}" 
                                      method="POST" class="d-inline" 
                                      onsubmit="return confirm('{{ __('Удалить главного модератора?') }}')">
                                  @csrf
                                  @method('DELETE')
                                  <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="{{ __('Удалить главного модератора') }}">
                                    <i class="icon-base ti tabler-trash"></i>
                                  </button>
                                </form>
                              @else
                                <span class="text-muted small">{{ __('Главный модератор') }}</span>
                              @endif
                            </td>
                          @endif
                        </tr>
                      @endif
                      @foreach ($company->accessUsers as $user)
                        <tr>
                          <td>
                            <div class="d-flex align-items-center">
                              @if ($user->avatar)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                     class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                              @else
                                <div class="avatar-initial rounded-circle bg-label-primary me-2" 
                                     style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                  {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                              @endif
                              <div>
                                <strong>{{ $user->name }}</strong>
                                @if($user->id === auth()->id())
                                  <span class="badge bg-label-info ms-1">{{ __('Вы') }}</span>
                                @endif
                              </div>
                            </div>
                          </td>
                          <td>{{ $user->email }}</td>
                          <td>
                            @if($user->pivot->access_type === 'edit')
                              <span class="badge bg-label-success">
                                <i class="icon-base ti tabler-pencil me-1"></i>{{ __('Полный доступ') }}
                              </span>
                            @else
                              <span class="badge bg-label-info">
                                <i class="icon-base ti tabler-eye me-1"></i>{{ __('Только просмотр') }}
                              </span>
                            @endif
                          </td>
                          <td>
                            <small class="text-muted">{{ $user->pivot->created_at?->format('d.m.Y H:i') ?? '—' }}</small>
                          </td>
                          @if($canEditCompany)
                            <td class="text-end">
                              <form action="{{ route('admin.companies.access.destroy', [$company, $user->pivot->id]) }}" 
                                    method="POST" class="d-inline" onsubmit="return confirm('{{ __('Удалить доступ модератора?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="{{ __('Удалить доступ') }}">
                                  <i class="icon-base ti tabler-trash"></i>
                                </button>
                              </form>
                            </td>
                          @endif
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="text-center py-6">
                  <i class="icon-base ti tabler-users-off" style="font-size: 64px; color: #ccc;"></i>
                  <p class="mt-3 text-muted">{{ __('Модераторы не назначены') }}</p>
                  <p class="small text-muted mb-3">{{ __('Нажмите кнопку выше, чтобы добавить модератора с доступом к компании') }}</p>
                  @if($canEditCompany)
                    <button type="button" class="btn btn-primary" 
                            data-bs-toggle="modal" data-bs-target="#addAccessModal">
                      <i class="icon-base ti tabler-plus me-1"></i> {{ __('Добавить первого модератора') }}
                    </button>
                  @endif
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if($canEditCompany)
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
@endif

<!-- Модальные окна для просмотра лицензий -->
@foreach($company->licenses as $license)
  <div class="modal fade" id="licenseModal{{ $license->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Лицензия компании') }} — {{ $company->name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center" style="max-height: 80vh; overflow-y: auto;">
          <img src="{{ url('public/storage/' . $license->file_path) }}" 
               alt="{{ __('Лицензия') }}" 
               class="img-fluid"
               style="max-width: 100%; height: auto;">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
            {{ __('Закрыть') }}
          </button>
          <a href="{{ url('public/storage/' . $license->file_path) }}" 
             class="btn btn-primary" download="{{ $license->original_name }}">
            <i class="icon-base ti tabler-download me-1"></i> {{ __('Скачать') }}
          </a>
          @if($canEditCompany)
            <button type="button" 
                    class="btn btn-danger delete-license-btn-modal"
                    data-license-id="{{ $license->id }}"
                    data-url="{{ route('admin.companies.licenses.destroy', [$company, $license]) }}"
                    data-bs-dismiss="modal">
              <i class="icon-base ti tabler-trash me-1"></i> {{ __('Удалить') }}
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>
@endforeach

@if($canEditCompany)
<!-- Modal: Загрузка лицензий -->
<div class="modal fade" id="uploadLicenseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.companies.licenses.store', $company) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Загрузить лицензии') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">{{ __('Выберите изображения лицензий') }}</label>
            <input type="file" 
                   class="form-control @error('license_files.*') is-invalid @enderror" 
                   name="license_files[]" 
                   accept="image/*"
                   multiple
                   required>
            <small class="text-muted">
              {{ __('Вы можете выбрать несколько файлов. Допустимые форматы: JPEG, PNG, JPG, GIF, WEBP. Максимальный размер файла: 10 МБ.') }}
            </small>
            @error('license_files.*')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div id="preview-container" class="row g-2 mt-2"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
            {{ __('Отмена') }}
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-upload me-1"></i> {{ __('Загрузить') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

@if($canEditCompany)
<!-- Modal: Добавить банк (новая структура) -->
<div class="modal fade" id="addBankModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('admin.companies.banks.store', $company) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Добавить новый банк') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">{{ __('Название банка') }}</label>
            <input type="text" class="form-control" name="name" 
                   placeholder="напр. Монобанк, ПриватБанк" required>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Страна') }}</label>
              <input type="text" class="form-control" name="country" 
                     placeholder="напр. Украина">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Код банка') }}</label>
              <input type="text" class="form-control" name="bank_code" 
                     placeholder="MFI, SWIFT">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('Примечания') }}</label>
            <textarea class="form-control" name="notes" rows="3" 
                      placeholder="{{ __('Дополнительная информация о банке') }}"></textarea>
          </div>

          <hr>
          <h6 class="text-uppercase text-muted small mb-3">{{ __('Доступы и контакты') }}</h6>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Логин') }}</label>
              <input type="text" class="form-control" name="login">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Логин ID') }}</label>
              <input type="text" class="form-control" name="login_id">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Пароль') }}</label>
              <input type="text" class="form-control" name="password">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Email') }}</label>
              <input type="email" class="form-control" name="email">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Пароль от email') }}</label>
              <input type="text" class="form-control" name="email_password">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Ссылка на онлайн-банк') }}</label>
              <input type="url" class="form-control" name="online_banking_url">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Имя менеджера') }}</label>
              <input type="text" class="form-control" name="manager_name">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Телефон менеджера') }}</label>
              <input type="text" class="form-control" name="manager_phone">
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
            {{ __('Отмена') }}
          </button>
          <button type="submit" class="btn btn-primary">
            {{ __('Добавить банк') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

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
@endif

@if($canEditCompany)
<!-- Modal: Добавить доступ -->
<div class="modal fade" id="addAccessModal" tabindex="-1" aria-labelledby="addAccessModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.companies.access.store', $company) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addAccessModalLabel">{{ __('Добавить модератора') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info mb-3">
            <i class="icon-base ti tabler-info-circle me-1"></i>
            {{ __('Выберите пользователя и уровень доступа: полный — для редактирования, только чтение — для просмотра без изменений.') }}
          </div>
          <div class="mb-3">
            <label class="form-label required">{{ __('Пользователь') }}</label>
            <select class="form-select select2" name="user_id" required>
              <option value="">{{ __('Выберите пользователя') }}</option>
              @php
                // Получаем всех пользователей, кроме тех, кто уже имеет доступ к компании
                $availableUsers = \App\Models\User::with('roleModel')
                  ->whereNotIn('id', $company->accessUsers->pluck('id'))
                  ->where('id', '!=', $company->moderator_id) // Исключаем главного модератора
                  ->orderBy('name')
                  ->get();
                $selectedUserId = old('user_id');
                $selectedAccessType = old('access_type', 'edit');
              @endphp
              @forelse ($availableUsers as $user)
                @php
                  $label = $user->roleModel?->name;
                  if (! $label && $user->role === \App\Models\User::ROLE_SUPER_ADMIN) {
                      $label = __('Супер-админ');
                  } elseif (! $label) {
                      $label = __('Пользователь');
                  }
                @endphp
                <option value="{{ $user->id }}" @selected((string) $selectedUserId === (string) $user->id)>
                  {{ $user->name }} ({{ $user->email }}) - {{ $label }}
                </option>
              @empty
                <option value="" disabled>{{ __('Нет доступных пользователей для добавления') }}</option>
              @endforelse
            </select>
            @error('user_id')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-2">
              {{ __('Пользователь получит доступ к данным выбранной компании') }}
            </small>
            @if($availableUsers->isEmpty())
              <div class="alert alert-warning mt-2 mb-0">
                <small>
                  <i class="icon-base ti tabler-info-circle me-1"></i>
                  {{ __('Все доступные пользователи уже имеют доступ к этой компании') }}
                </small>
              </div>
            @endif
          </div>
          <div class="mb-3">
            <label class="form-label required">{{ __('Уровень доступа') }}</label>
            <div class="d-flex flex-column gap-2">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="access_type" id="access_type_edit" value="edit" @checked($selectedAccessType === 'edit')>
                <label class="form-check-label" for="access_type_edit">
                  <strong>{{ __('Полный доступ') }}</strong>
                  <span class="d-block text-muted small">{{ __('Пользователь сможет редактировать и управлять данными компании.') }}</span>
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="access_type" id="access_type_view" value="view" @checked($selectedAccessType === 'view')>
                <label class="form-check-label" for="access_type_view">
                  <strong>{{ __('Только чтение') }}</strong>
                  <span class="d-block text-muted small">{{ __('Пользователь сможет просматривать данные, но не сможет их изменять.') }}</span>
                </label>
              </div>
            </div>
            @error('access_type')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
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
@endif

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
  
  .cursor-pointer {
    cursor: pointer;
    transition: background-color 0.15s ease-in-out;
  }
  
  .cursor-pointer:hover {
    background-color: rgba(0, 0, 0, 0.02);
  }
  
  /* Исправление отображения модального окна */
  #addAccessModal .modal-dialog {
    z-index: 1055;
  }
  
  #addAccessModal .modal-content {
    background-color: #fff !important;
    opacity: 1 !important;
  }
  
  #addAccessModal .modal-header,
  #addAccessModal .modal-body,
  #addAccessModal .modal-footer {
    opacity: 1 !important;
    visibility: visible !important;
    background-color: transparent;
  }
  
  #addAccessModal .modal-body {
    padding: 1.5rem;
  }
  
  /* Исправление z-index для Select2 в модальном окне */
  .select2-container {
    z-index: 9999;
  }
  
  .select2-dropdown {
    z-index: 10000;
  }
  
  .select2-container--open {
    z-index: 10001;
  }
  
  /* Стили для галереи лицензий */
  .license-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 0.375rem;
  }
  
  .license-thumbnail {
    transition: transform 0.3s ease, opacity 0.3s ease;
  }
  
  .license-image-wrapper:hover .license-thumbnail {
    transform: scale(1.05);
    opacity: 0.9;
  }
  
  .license-image-wrapper .delete-license-btn {
    transition: opacity 0.3s ease;
  }
  
  .license-image-wrapper:not(:hover) .delete-license-btn {
    opacity: 0;
  }
  
  .license-image-wrapper:hover .delete-license-btn {
    opacity: 1 !important;
  }
  
  .cursor-pointer {
    cursor: pointer;
  }
</style>
@endsection

