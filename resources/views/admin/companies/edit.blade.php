@extends('layouts.admin')

@section('title', __('Редактирование компании'))

@push('styles')
  <link rel="stylesheet" href="{{ asset('vendor/vuexy/vendor/libs/@form-validation/form-validation.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/vuexy/vendor/libs/select2/select2.css') }}">
@endpush

@push('scripts')
  <script src="{{ asset('vendor/vuexy/vendor/libs/@form-validation/popular.js') }}"></script>
  <script src="{{ asset('vendor/vuexy/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
  <script src="{{ asset('vendor/vuexy/vendor/libs/@form-validation/auto-focus.js') }}"></script>
  <script src="{{ asset('vendor/vuexy/vendor/libs/select2/select2.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
          placeholder: '{{ __('Выберите из списка') }}',
          allowClear: false
        });
      }

      const form = document.querySelector('#company-edit-form');
      if (!form || typeof FormValidation === 'undefined') {
        return;
      }

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
              notEmpty: { message: '{{ __('Выберите модератора') }}' }
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

      fv.on('core.form.valid', () => {
        form.submit();
      });
    });
  </script>
@endpush

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">
      <span class="text-muted fw-light">{{ __('Управление') }} / {{ __('Компании') }} /</span> {{ __('Редактирование') }}
    </h4>
    <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-label-secondary">
      {{ __('Отмена') }}
    </a>
  </div>

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
          <h5 class="mb-0">{{ __('Редактировать данные компании') }}</h5>
        </div>
        <div class="card-body">
          <form id="company-edit-form" action="{{ route('admin.companies.update', $company) }}" 
                method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label required">{{ __('Название компании') }}</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $company->name) }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="country" class="form-label required">{{ __('Страна') }}</label>
                <select class="form-select select2 @error('country') is-invalid @enderror" 
                        id="country" name="country" required>
                  <option value="">{{ __('Выберите страну') }}</option>
                  @foreach (config('countries.list', []) as $code => $name)
                    <option value="{{ $code }}" {{ old('country', $company->country) == $code ? 'selected' : '' }}>
                      {{ __($name) }}
                    </option>
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
                        id="moderator_id" name="moderator_id" required>
                  <option value="">{{ __('Выберите модератора') }}</option>
                  @foreach ($moderators as $moderator)
                    <option value="{{ $moderator->id }}" 
                            {{ old('moderator_id', $company->moderator_id) == $moderator->id ? 'selected' : '' }}>
                      {{ $moderator->name }} ({{ $moderator->email }})
                    </option>
                  @endforeach
                </select>
                @error('moderator_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="license_file" class="form-label">{{ __('Лицензия компании') }}</label>
                @if ($company->license_file)
                  <div class="mb-2">
                    <img src="{{ asset('storage/' . $company->license_file) }}" 
                         alt="{{ __('Текущая лицензия') }}" 
                         class="img-thumbnail" 
                         style="max-width: 200px;">
                    <p class="text-muted mt-1 mb-0">
                      <small>{{ __('Загрузите новый файл, чтобы заменить текущий') }}</small>
                    </p>
                  </div>
                @endif
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
                <i class="mdi mdi-content-save me-1"></i> {{ __('Сохранить изменения') }}
              </button>
              <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-label-secondary">
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

