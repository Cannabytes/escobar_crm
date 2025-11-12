@extends('layouts.admin')

@section('title', __('Создание пользователя'))

@push('styles')
  <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/form-validation.css') }}">
@endpush

@push('scripts')
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/popular.js') }}"></script>
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
  <script src="{{ url('public/vendor/vuexy/vendor/libs/@form-validation/auto-focus.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.querySelector('#user-create-form');
      if (!form || typeof FormValidation === 'undefined') {
        return;
      }

      FormValidation.formValidation(form, {
        fields: {
          name: {
            validators: {
              notEmpty: {
                message: '{{ __('Введите имя пользователя') }}'
              },
              stringLength: {
                min: 3,
                max: 100,
                message: '{{ __('Длина имени должна быть от 3 до 100 символов') }}'
              }
            }
          },
          email: {
            validators: {
              notEmpty: {
                message: '{{ __('Введите email') }}'
              },
              emailAddress: {
                message: '{{ __('Введите корректный email') }}'
              }
            }
          },
          password: {
            validators: {
              notEmpty: {
                message: '{{ __('Введите пароль') }}'
              },
              stringLength: {
                min: 6,
                message: '{{ __('Пароль должен содержать минимум 6 символов') }}'
              }
            }
          },
          password_confirmation: {
            validators: {
              notEmpty: {
                message: '{{ __('Повторите пароль') }}'
              },
              identical: {
                compare: function() {
                  return form.querySelector('[name=\"password\"]').value;
                },
                message: '{{ __('Пароли не совпадают') }}'
              }
            }
          },
          role: {
            validators: {
              notEmpty: {
                message: '{{ __('Выберите роль') }}'
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
          <h5 class="card-title mb-0">{{ __('Регистрация нового пользователя') }}</h5>
          <small class="text-muted">{{ __('Супер админ может создавать и назначать доступы для сотрудников компаний.') }}</small>
        </div>
        <div class="card-body pt-3">
          <form id="user-create-form" method="POST" action="{{ route('admin.users.store') }}" novalidate>
            @csrf

            <div class="row g-4">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="name" class="form-label">{{ __('Имя и фамилия') }}</label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    placeholder="{{ __('Введите полное имя пользователя') }}"
                    required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="email" class="form-label">{{ __('Email') }}</label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="user@example.com"
                    required>
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="phone" class="form-label">{{ __('Номер телефона') }}</label>
                  <input
                    type="text"
                    id="phone"
                    name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone') }}"
                    placeholder="+7 (999) 123-45-67">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="operator" class="form-label">{{ __('Оператор') }}</label>
                  <input
                    type="text"
                    id="operator"
                    name="operator"
                    class="form-control @error('operator') is-invalid @enderror"
                    value="{{ old('operator') }}"
                    placeholder="{{ __('Например: МТС, Билайн, Мегафон') }}">
                  @error('operator')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="role" class="form-label">{{ __('Роль в системе') }}</label>
                  <select
                    id="role"
                    name="role"
                    class="form-select @error('role') is-invalid @enderror"
                    required>
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>{{ __('Выберите роль') }}</option>
                    <option value="moderator" {{ old('role') === 'moderator' ? 'selected' : '' }}>
                      {{ __('Модератор') }}
                    </option>
                    <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>
                      {{ __('Пользователь') }}
                    </option>
                  </select>
                  @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="password" class="form-label">{{ __('Пароль') }}</label>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      name="password"
                      class="form-control @error('password') is-invalid @enderror"
                      placeholder="••••••••"
                      minlength="6"
                      required>
                    <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    @error('password')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-text">{{ __('Минимум 6 символов, рекомендуем использовать буквы и цифры.') }}</div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="password_confirmation" class="form-label">{{ __('Подтверждение пароля') }}</label>
                  <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    placeholder="••••••••"
                    minlength="6"
                    required>
                  @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4">
              <a href="{{ url()->previous() }}" class="btn btn-label-secondary">{{ __('Отмена') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Создать пользователя') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

