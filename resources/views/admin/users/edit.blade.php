@extends('layouts.admin')

@section('title', __('Редактирование пользователя'))

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.querySelector('#user-edit-form');
      const passwordField = document.querySelector('#password');
      const confirmField = document.querySelector('#password_confirmation');

      const validatePasswords = () => {
        if (!passwordField || !confirmField) {
          return;
        }

        if (!passwordField.value && !confirmField.value) {
          confirmField.setCustomValidity('');
          return;
        }

        if (passwordField.value !== confirmField.value) {
          confirmField.setCustomValidity('{{ __('Пароли не совпадают') }}');
        } else {
          confirmField.setCustomValidity('');
        }
      };

      if (passwordField && confirmField) {
        passwordField.addEventListener('input', validatePasswords);
        confirmField.addEventListener('input', validatePasswords);
      }

      // Переключение видимости паролей
      document.querySelectorAll('[data-toggle-password]').forEach(toggle => {
        const targetSelector = toggle.getAttribute('data-toggle-password');
        const targetInput = document.querySelector(targetSelector);

        if (!targetInput) {
          return;
        }

        toggle.addEventListener('click', () => {
          const isHidden = targetInput.type === 'password';
          targetInput.type = isHidden ? 'text' : 'password';

          const icon = toggle.querySelector('i');
          if (icon) {
            icon.classList.toggle('tabler-eye', isHidden);
            icon.classList.toggle('tabler-eye-off', !isHidden);
          }
        });
      });

      if (form) {
        form.addEventListener('submit', event => {
          validatePasswords();

          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
          }

          const submitBtn = form.querySelector('button[type="submit"]');
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>{{ __('Сохранение...') }}';
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
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h5 class="card-title mb-0">{{ __('Изменение пользователя') }}</h5>
              <small class="text-muted">
                {{ __('Обновите данные учетной записи и роль пользователя.') }}
              </small>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-label-secondary">
              <i class="icon-base ti tabler-arrow-left"></i> {{ __('Назад') }}
            </a>
          </div>
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

          @if ($roles->isEmpty())
            <div class="alert alert-warning">
              <i class="icon-base ti tabler-alert-triangle me-2"></i>
              {{ __('Не найдены активные роли. Выполните сидер RoleSeeder, чтобы добавить роли в систему.') }}
            </div>
          @endif

          <form
            id="user-edit-form"
            method="POST"
            action="{{ route('admin.users.update', $user) }}"
            class="needs-validation"
            novalidate>
            @csrf
            @method('PUT')

            <div class="row g-4">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="name" class="form-label">{{ __('Имя и фамилия') }}</label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}"
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
                    value="{{ old('email', $user->email) }}"
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
                    value="{{ old('phone', $user->phone) }}"
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
                    value="{{ old('operator', $user->operator) }}"
                    placeholder="{{ __('Например: МТС, Билайн, Мегафон') }}">
                  @error('operator')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-12">
                <div class="mb-3">
                  <label for="phone_comment" class="form-label">{{ __('Комментарий к телефону') }}</label>
                  <textarea
                    id="phone_comment"
                    name="phone_comment"
                    rows="3"
                    class="form-control @error('phone_comment') is-invalid @enderror"
                    placeholder="{{ __('Дополнительная информация о номере телефона') }}">{{ old('phone_comment', $user->phone_comment) }}</textarea>
                  @error('phone_comment')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="password" class="form-label">{{ __('Новый пароль') }}</label>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      name="password"
                      class="form-control @error('password') is-invalid @enderror"
                      placeholder="••••••••"
                      minlength="8"
                      autocomplete="new-password">
                    <span class="input-group-text cursor-pointer" data-toggle-password="#password"><i class="icon-base ti tabler-eye-off"></i></span>
                  </div>
                  <div class="form-text">{{ __('Оставьте поле пустым, если не хотите менять пароль.') }}</div>
                  @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="password_confirmation" class="form-label">{{ __('Подтверждение нового пароля') }}</label>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password_confirmation"
                      name="password_confirmation"
                      class="form-control @error('password_confirmation') is-invalid @enderror"
                      placeholder="••••••••"
                      minlength="8"
                      autocomplete="new-password">
                    <span class="input-group-text cursor-pointer" data-toggle-password="#password_confirmation"><i class="icon-base ti tabler-eye-off"></i></span>
                  </div>
                  @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
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
                      <option value="{{ $role->id }}" {{ (int) old('role_id', $user->role_id) === $role->id ? 'selected' : '' }}>
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
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4">
              <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">{{ __('Отмена') }}</a>
              <button type="submit" class="btn btn-primary" {{ $roles->isEmpty() ? 'disabled' : '' }}>
                <i class="ti ti-device-floppy me-1"></i> {{ __('Сохранить изменения') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

