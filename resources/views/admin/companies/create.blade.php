@extends('layouts.admin')

@section('title', __('Добавление компании'))

@push('styles')
  <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/form-validation.css') }}">
  <link rel="stylsheet" href="{{ url('public/vendor/vuexy/vendor/libs/select2/select2.css') }}">
@endpush

@push('scripts')gi
  <script src="{{ url('public/vendor/vuexy/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/popular.js') }}"></script>
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/auto-focus.js') }}"></script>
  <script>
    // Ждем полной загрузки всех скриптов
    window.addEventListener('load', function() {
      // Инициализация Select2
      if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        jQuery('.select2').select2({
          placeholder: '{{ __('Выберите из списка') }}',
          allowClear: false,
          width: '100%'
        });
      }

      const form = document.querySelector('#company-create-form');
      if (!form) {
        return;
      }

      // Инициализация валидации только если библиотека доступна
      if (typeof FormValidation !== 'undefined') {
        const fv = FormValidation.formValidation(form, {
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
            country: {
              validators: {
                notEmpty: { message: '{{ __('Выберите страну') }}' }
              }
            },
            moderator_id: {
              validators: {
                notEmpty: { message: '{{ __('Выберите модератора') }}' },
                callback: {
                  message: '{{ __('Выберите модератора') }}',
                  callback: function(value) {
                    return value !== '' && value !== null && value !== '0';
                  }
                }
              }
            }
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({
              eleValidClass: '',
              rowSelector: '.mb-3'
            }),
            submitButton: new FormValidation.plugins.SubmitButton(),
            autoFocus: new FormValidation.plugins.AutoFocus()
          }
        });

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
                       placeholder="{{ __('ООО "Компания"') }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="country" class="form-label required">{{ __('Страна') }}</label>
                <select class="form-select select2 @error('country') is-invalid @enderror" 
                        id="country" name="country" required>
                  <option value="">{{ __('Выберите страну') }}</option>
                  <option value="UAE" {{ old('country') == 'UAE' ? 'selected' : '' }}>{{ __('ОАЭ') }}</option>
                  <option value="Cyprus" {{ old('country') == 'Cyprus' ? 'selected' : '' }}>{{ __('Кипр') }}</option>
                  <option value="Malta" {{ old('country') == 'Malta' ? 'selected' : '' }}>{{ __('Мальта') }}</option>
                  <option value="Seychelles" {{ old('country') == 'Seychelles' ? 'selected' : '' }}>{{ __('Сейшелы') }}</option>
                  <option value="BVI" {{ old('country') == 'BVI' ? 'selected' : '' }}>{{ __('BVI') }}</option>
                  <option value="USA" {{ old('country') == 'USA' ? 'selected' : '' }}>{{ __('США') }}</option>
                  <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>{{ __('Великобритания') }}</option>
                  <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>{{ __('Сингапур') }}</option>
                  <option value="Hong Kong" {{ old('country') == 'Hong Kong' ? 'selected' : '' }}>{{ __('Гонконг') }}</option>
                  <option value="Other" {{ old('country') == 'Other' ? 'selected' : '' }}>{{ __('Другая') }}</option>
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
                        id="moderator_id" name="moderator_id" required>
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
</style>
@endsection
