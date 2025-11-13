@extends('layouts.admin')

@section('title', __('Создание пользователя'))

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.querySelector('#user-create-form');
      const passwordField = document.querySelector('#password');
      const confirmField = document.querySelector('#password_confirmation');
      
      // Проверка совпадения паролей
      if (confirmField) {
        confirmField.addEventListener('input', function() {
          if (this.value !== passwordField.value) {
            this.setCustomValidity('{{ __('Пароли не совпадают') }}');
          } else {
            this.setCustomValidity('');
          }
        });
        
        passwordField.addEventListener('input', function() {
          if (confirmField.value) {
            confirmField.dispatchEvent(new Event('input'));
          }
        });
      }

      // Обработка отправки формы
      if (form) {
        form.addEventListener('submit', function(e) {
          // Проверяем валидность формы
          if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated');
            return false;
          }
          
          // Отключаем кнопку отправки для предотвращения двойной отправки
          const submitBtn = form.querySelector('button[type="submit"]');
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>{{ __('Создание...') }}';
          }
        });
      }
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
          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
              <h5 class="alert-heading mb-2">{{ __('Ошибки при заполнении формы:') }}</h5>
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <form id="user-create-form" method="POST" action="{{ route('admin.users.store') }}" class="needs-validation">
            @csrf

            @if ($roles->isEmpty())
              <div class="alert alert-warning mb-4">
                <i class="icon-base ti tabler-alert-triangle me-2"></i>
                {{ __('Не найдены активные роли. Выполните сидер RoleSeeder, чтобы добавить роли в систему.') }}
              </div>
            @endif

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
                  <label for="role_id" class="form-label">{{ __('Роль пользователя') }} <span class="text-danger">*</span></label>
                  <select
                    id="role_id"
                    name="role_id"
                    class="form-select @error('role_id') is-invalid @enderror"
                    {{ $roles->isEmpty() ? 'disabled' : '' }}
                    required>
                    <option value="">{{ __('Выберите роль') }}</option>
                    @foreach ($roles as $role)
                      <option value="{{ $role->id }}" {{ (int) old('role_id') === $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                        @if ($role->description)
                          — {{ $role->description }}
                        @endif
                      </option>
                    @endforeach
                  </select>
                  @error('role_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="text-muted d-block mt-2">
                    {{ __('Роль определяет доступ пользователя к разделам системы.') }}
                  </small>
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
              <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">{{ __('Отмена') }}</a>
              <button type="submit" class="btn btn-primary" {{ $roles->isEmpty() ? 'disabled' : '' }}>
                <i class="ti ti-plus me-1"></i>
                {{ __('Создать пользователя') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

