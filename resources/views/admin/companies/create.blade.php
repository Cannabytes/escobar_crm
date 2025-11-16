@extends('layouts.admin')

@section('title', __('Добавление компании'))

@push('styles')
  <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/form-validation.css') }}">
  <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/libs/select2/select2.css') }}">
@endpush

@push('scripts')
  <script src="{{ url('public/vendor/vuexy/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/popular.js') }}"></script>
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/auto-focus.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('#company-create-form');
      if (!form) {
        return;
      }

      // Глобальная переменная для FormValidation
      let fv = null;

      // Полностью отключаем FormValidation для Select2 полей
      const selectElements = form.querySelectorAll('select.select2');
      selectElements.forEach(function(select) {
        select.setAttribute('data-fv-validate', 'false');
        select.setAttribute('data-fv-ignore', 'true');
        select.setAttribute('data-fv-excluded', 'true');
        select.setAttribute('data-fv-field-ignore', 'true');
      });

      // Инициализация Select2 ПЕРЕД валидацией
      if (typeof $ !== 'undefined' && $.fn.select2) {
        // Инициализируем Select2 с правильными настройками
        $('.select2').each(function() {
          const $select = $(this);
          $select.select2({
            placeholder: $select.data('placeholder') || '{{ __('Выберите из списка') }}',
            allowClear: false,
            width: '100%',
            dropdownParent: $('body'),
            language: {
              noResults: function() {
                return '{{ __('Результаты не найдены') }}';
              }
            }
          });

          // Устанавливаем флаг при открытии Select2
          $select.on('select2:open', function() {
            // Помечаем, что Select2 открыт
            $select.data('select2-open', true);

            // Отключаем валидацию на время работы с Select2
            if (fv) {
              fv.disableValidator($select.attr('name'));
            }
          });

          // Сбрасываем флаг при закрытии
          $select.on('select2:close', function() {
            $select.data('select2-open', false);

            // Включаем валидацию обратно
            if (fv) {
              fv.enableValidator($select.attr('name'));
            }
          });

          // Дополнительная защита: предотвращаем закрытие при клике на сам select
          $select.on('select2:opening', function(e) {
            // Разрешаем открытие
            return true;
          });
        });
      }

      // Инициализация валидации только если библиотека доступна
      if (typeof FormValidation !== 'undefined') {
        fv = FormValidation.formValidation(form, {
          fields: {
            name: {
              validators: {
                notEmpty: { message: '{{ __('Укажите название компании') }}' },
                stringLength: {
                  max: 191,
                  message: '{{ __('Максимальная длина — 191 символ') }}'
                }
              }
            },
            // Отключаем валидацию для Select2 полей
            country: {
              enabled: false
            },
            moderator_id: {
              enabled: false
            }
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger({
              // Валидировать только при потере фокуса, не при клике
              on: 'blur'
            }),
            bootstrap5: new FormValidation.plugins.Bootstrap5({
              eleValidClass: '',
              rowSelector: '.mb-3'
            }),
            submitButton: new FormValidation.plugins.SubmitButton(),
            autoFocus: new FormValidation.plugins.AutoFocus()
          }
        });

        // Ручная валидация Select2 полей при отправке формы
        form.addEventListener('submit', function(e) {
          let isValid = true;

          // Проверяем Select2 поля вручную
          const countrySelect = form.querySelector('select[name="country"]');
          const moderatorSelect = form.querySelector('select[name="moderator_id"]');

          // Сбрасываем предыдущие ошибки
          if (countrySelect) {
            countrySelect.closest('.mb-3').classList.remove('was-validated');
          }
          if (moderatorSelect) {
            moderatorSelect.closest('.mb-3').classList.remove('was-validated');
          }

          if (countrySelect && (!countrySelect.value || countrySelect.value === '')) {
            isValid = false;
            countrySelect.closest('.mb-3').classList.add('was-validated');
          }

          if (moderatorSelect && (!moderatorSelect.value || moderatorSelect.value === '')) {
            isValid = false;
            moderatorSelect.closest('.mb-3').classList.add('was-validated');
          }

          if (!isValid) {
            e.preventDefault();
            return false;
          }
        });
        
        // Агрессивная защита Select2 от закрытия
        if ($ && $.fn.select2) {
          // Функция для проверки, открыт ли какой-либо Select2
          function isAnySelect2Open() {
            return $('.select2-container--open').length > 0;
          }

          // Отключаем все возможные события, которые могут закрыть Select2
          $(document).on('mousedown.select2-protection touchstart.select2-protection', function(e) {
            const $target = $(e.target);

            // Если клик внутри открытого Select2, предотвращаем закрытие
            if ($target.closest('.select2-container--open').length) {
              e.stopPropagation();
              e.stopImmediatePropagation();
              e.preventDefault();
              return false;
            }

            // Если клик на Select2 элементе, предотвращаем
            if ($target.hasClass('select2-container') ||
                $target.closest('.select2-container').length ||
                $target.hasClass('select2-selection') ||
                $target.closest('.select2-selection').length) {

              // Если Select2 закрыт, позволяем открыть
              if (!isAnySelect2Open()) {
                return true;
              }

              // Если Select2 открыт, предотвращаем закрытие
              e.stopPropagation();
              e.stopImmediatePropagation();
              e.preventDefault();
              return false;
            }
          });

          // Дополнительная защита при клике на документ
          $(document).on('click.select2-protection', function(e) {
            const $target = $(e.target);

            // Если клик не внутри открытого Select2, позволяем закрыться
            if (!$target.closest('.select2-container--open').length) {
              return true;
            }

            // Если клик внутри открытого Select2, предотвращаем
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
          });
        }

        fv.on('core.form.valid', function() {
          form.submit();
        });
      }
    });
  </script>
@endpush

@section('content')
  <h4 class="fw-bold mb-4">
    <span class="text-muted fw-light">{{ __('Управление') }} / {{ __('Компании') }} /</span> {{ __('Добавление') }}
  </h4>

  @if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <h5 class="alert-heading mb-2">{{ __('Ошибки при заполнении формы') }}</h5>
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Основные данные компании') }}</h5>
          <small class="text-muted">
            {{ __('Заполните информацию о компании и выберите модератора') }}
          </small>
        </div>
        <div class="card-body">
          <form id="company-create-form" action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label required">{{ __('Название компании') }}</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name') }}"
                       placeholder="{{ __('ООО "Компания"') }}">
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="country" class="form-label required">{{ __('Страна') }}</label>
                <select class="form-select select2 @error('country') is-invalid @enderror"
                        id="country" name="country">
                  <option value="">{{ __('Выберите страну') }}</option>
                  @foreach (config('countries.list', []) as $code => $name)
                    <option value="{{ $code }}" {{ old('country') == $code ? 'selected' : '' }}>{{ __($name) }}</option>
                  @endforeach
                </select>
                @error('country')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="moderator_id" class="form-label required">{{ __('Модератор (кто будет вести)') }}</label>
                <select class="form-select select2 @error('moderator_id') is-invalid @enderror"
                        id="moderator_id" name="moderator_id">
                  <option value="">{{ __('Выберите модератора') }}</option>
                  @foreach ($moderators as $moderator)
                    <option value="{{ $moderator->id }}" {{ old('moderator_id') == $moderator->id ? 'selected' : '' }}>
                      {{ $moderator->name }} ({{ $moderator->email }})
                    </option>
                  @endforeach
                </select>
                @error('moderator_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">
                  {{ __('Модератор будет вести эту компанию и иметь полный доступ к её данным') }}
                </small>
              </div>

              <div class="col-md-6 mb-3">
                <label for="license_file" class="form-label">{{ __('Лицензия компании') }}</label>
                <input type="file" class="form-control @error('license_file') is-invalid @enderror" 
                       id="license_file" name="license_file" accept=".jpg,.jpeg,.png">
                @error('license_file')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">
                  {{ __('Форматы: JPG, PNG. Максимальный размер: 5 МБ') }}
                </small>
              </div>
            </div>

            <div class="mt-4">
              <button type="submit" class="btn btn-primary me-2">
                <i class="mdi mdi-content-save me-1"></i> {{ __('Сохранить') }}
              </button>
              <a href="{{ route('admin.companies.index') }}" class="btn btn-label-secondary">
                {{ __('Отмена') }}
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<style>
  .required:after {
    content: " *";
    color: red;
  }

  /* Исправление z-index для Select2 */
  .select2-container {
    z-index: 9999 !important;
  }

  .select2-dropdown {
    z-index: 10000 !important;
  }

  .select2-container--open {
    z-index: 10001 !important;
  }

  /* Предотвращаем закрытие при клике */
  .select2-container--open .select2-dropdown {
    pointer-events: auto !important;
  }

  .select2-results__option {
    cursor: pointer !important;
  }

  /* Скрываем оригинальные select элементы */
  select.select2 {
    opacity: 0;
    position: absolute;
    z-index: -1;
  }

  /* Убеждаемся, что Select2 контейнер перехватывает все клики */
  .select2-selection {
    pointer-events: auto !important;
  }

  /* Исправление отображения ошибок валидации */
  .was-validated .select2-selection {
    border-color: #dc3545 !important;
  }

  .was-validated + .invalid-feedback {
    display: block;
  }
</style>
@endsection
