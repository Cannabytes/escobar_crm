@extends('layouts.admin')

@section('title', __('Добавление компании'))

@push('styles')
  <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/form-validation.css') }}">
@endpush

@push('scripts')
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/popular.js') }}"></script>
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/auto-focus.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.querySelector('#company-create-form');
      if (!form || typeof FormValidation === 'undefined') {
        return;
      }

      const getDateValue = (input) => {
        if (!input || !input.value) {
          return null;
        }
        const value = input.value;
        const parsed = new Date(value);
        return Number.isNaN(parsed.getTime()) ? null : parsed;
      };

      FormValidation.formValidation(form, {
        fields: {
          license_number: {
            validators: {
              notEmpty: { message: '{{ __('Укажите номер лицензии') }}' },
              stringLength: {
                max: 191,
                message: '{{ __('Максимальная длина — 191 символ') }}'
              }
            }
          },
          registration_number: {
            validators: {
              notEmpty: { message: '{{ __('Укажите номер регистрации') }}' },
              stringLength: {
                max: 191,
                message: '{{ __('Максимальная длина — 191 символ') }}'
              }
            }
          },
          incorporation_date: {
            validators: {
              notEmpty: { message: '{{ __('Выберите дату основания') }}' }
            }
          },
          expiration_date: {
            validators: {
              notEmpty: { message: '{{ __('Выберите дату истечения срока') }}' },
              callback: {
                message: '{{ __('Дата истечения должна быть позже даты основания') }}',
                callback: (input) => {
                  const startDate = getDateValue(form.querySelector('[name="incorporation_date"]'));
                  const endDate = getDateValue(input.element);
                  if (!startDate || !endDate) {
                    return true;
                  }
                  return endDate > startDate;
                }
              }
            }
          },
          jurisdiction_zone: {
            validators: {
              notEmpty: { message: '{{ __('Укажите зону регистрации') }}' },
              stringLength: {
                max: 191,
                message: '{{ __('Максимальная длина — 191 символ') }}'
              }
            }
          },
          business_activities: {
            validators: {
              notEmpty: { message: '{{ __('Опишите виды деятельности') }}' }
            }
          },
          legal_address: {
            validators: {
              notEmpty: { message: '{{ __('Укажите юридический адрес') }}' },
              stringLength: {
                max: 255,
                message: '{{ __('Максимальная длина — 255 символов') }}'
              }
            }
          },
          factual_address: {
            validators: {
              notEmpty: { message: '{{ __('Укажите фактический адрес') }}' },
              stringLength: {
                max: 255,
                message: '{{ __('Максимальная длина — 255 символов') }}'
              }
            }
          },
          owner_name: {
            validators: {
              notEmpty: { message: '{{ __('Укажите имя владельца') }}' },
              stringLength: {
                max: 191,
                message: '{{ __('Максимальная длина — 191 символ') }}'
              }
            }
          },
          email: {
            validators: {
              notEmpty: { message: '{{ __('Укажите email') }}' },
              emailAddress: { message: '{{ __('Введите корректный email') }}' }
            }
          },
          phone: {
            validators: {
              notEmpty: { message: '{{ __('Укажите телефон') }}' },
              stringLength: {
                max: 64,
                message: '{{ __('Максимальная длина — 64 символа') }}'
              }
            }
          },
          website: {
            validators: {
              uri: {
                message: '{{ __('Введите корректный URL') }}',
                allowLocal: false
              },
              stringLength: {
                max: 255,
                message: '{{ __('Максимальная длина — 255 символов') }}'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            rowSelector: '.mb-3'
          }),
          submitButton: new FormValidation.plugins.SubmitButton(),
          autoFocus: new FormValidation.plugins.AutoFocus()
        }
      });
    });
  </script>
@endpush

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header pb-2">
          <h5 class="card-title mb-0">{{ __('Новая компания') }}</h5>
          <small class="text-muted">
            {{ __('Заполните карточку компании для учёта и назначения ответственных сотрудников.') }}
          </small>
        </div>
        <div class="card-body pt-3">
          <form id="company-create-form" method="POST" action="{{ route('admin.companies.store') }}" novalidate>
            @csrf

            <div class="row g-4">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="license_number" class="form-label">{{ __('Номер лицензии') }}</label>
                  <input
                    type="text"
                    id="license_number"
                    name="license_number"
                    class="form-control @error('license_number') is-invalid @enderror"
                    value="{{ old('license_number') }}"
                    placeholder="{{ __('Например, LIC-123456') }}"
                    required>
                  @error('license_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="registration_number" class="form-label">{{ __('Номер регистрации') }}</label>
                  <input
                    type="text"
                    id="registration_number"
                    name="registration_number"
                    class="form-control @error('registration_number') is-invalid @enderror"
                    value="{{ old('registration_number') }}"
                    placeholder="{{ __('Например, REG-987654') }}"
                    required>
                  @error('registration_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="incorporation_date" class="form-label">{{ __('Дата основания') }}</label>
                  <input
                    type="date"
                    id="incorporation_date"
                    name="incorporation_date"
                    class="form-control @error('incorporation_date') is-invalid @enderror"
                    value="{{ old('incorporation_date') }}"
                    required>
                  @error('incorporation_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="expiration_date" class="form-label">{{ __('Дата истечения срока') }}</label>
                  <input
                    type="date"
                    id="expiration_date"
                    name="expiration_date"
                    class="form-control @error('expiration_date') is-invalid @enderror"
                    value="{{ old('expiration_date') }}"
                    required>
                  @error('expiration_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="jurisdiction_zone" class="form-label">{{ __('Юрисдикция / зона регистрации') }}</label>
                  <input
                    type="text"
                    id="jurisdiction_zone"
                    name="jurisdiction_zone"
                    class="form-control @error('jurisdiction_zone') is-invalid @enderror"
                    value="{{ old('jurisdiction_zone') }}"
                    placeholder="{{ __('Например, ОАЭ, DMCC') }}"
                    required>
                  @error('jurisdiction_zone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="owner_name" class="form-label">{{ __('Имя владельца') }}</label>
                  <input
                    type="text"
                    id="owner_name"
                    name="owner_name"
                    class="form-control @error('owner_name') is-invalid @enderror"
                    value="{{ old('owner_name') }}"
                    placeholder="{{ __('ФИО владельца') }}"
                    required>
                  @error('owner_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-12">
                <div class="mb-3">
                  <label for="business_activities" class="form-label">{{ __('Виды деятельности') }}</label>
                  <textarea
                    id="business_activities"
                    name="business_activities"
                    class="form-control @error('business_activities') is-invalid @enderror"
                    rows="3"
                    placeholder="{{ __('Опишите основные направления бизнеса, лицензии и услуги') }}"
                    required>{{ old('business_activities') }}</textarea>
                  @error('business_activities')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="legal_address" class="form-label">{{ __('Юридический адрес') }}</label>
                  <input
                    type="text"
                    id="legal_address"
                    name="legal_address"
                    class="form-control @error('legal_address') is-invalid @enderror"
                    value="{{ old('legal_address') }}"
                    placeholder="{{ __('Адрес согласно регистрационным документам') }}"
                    required>
                  @error('legal_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="factual_address" class="form-label">{{ __('Фактический адрес') }}</label>
                  <input
                    type="text"
                    id="factual_address"
                    name="factual_address"
                    class="form-control @error('factual_address') is-invalid @enderror"
                    value="{{ old('factual_address') }}"
                    placeholder="{{ __('Адрес фактического расположения офиса') }}"
                    required>
                  @error('factual_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-4">
                <div class="mb-3">
                  <label for="email" class="form-label">{{ __('Email') }}</label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="company@example.com"
                    required>
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-4">
                <div class="mb-3">
                  <label for="phone" class="form-label">{{ __('Телефон') }}</label>
                  <input
                    type="tel"
                    id="phone"
                    name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone') }}"
                    placeholder="+971 50 123 4567"
                    required>
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-4">
                <div class="mb-3">
                  <label for="website" class="form-label">{{ __('Веб-сайт') }}</label>
                  <input
                    type="url"
                    id="website"
                    name="website"
                    class="form-control @error('website') is-invalid @enderror"
                    value="{{ old('website') }}"
                    placeholder="https://example.com">
                  @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4">
              <button type="reset" class="btn btn-label-secondary">{{ __('Сбросить') }}</button>
              <button type="submit" class="btn btn-primary">{{ __('Сохранить компанию') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection


