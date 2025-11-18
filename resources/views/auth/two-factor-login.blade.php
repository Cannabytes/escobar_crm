@extends('layouts.guest')

@section('title', __('Двухфакторная аутентификация'))

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="app-brand justify-content-center mb-6">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
          <span class="app-brand-logo demo">
            <span class="text-primary">
              <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                  fill="currentColor" />
                <path
                  opacity="0.08"
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
                  fill="#161616" />
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                  fill="currentColor" />
              </svg>
            </span>
          </span>
          <span class="app-brand-text demo text-heading fw-bold">{{ config('app.name', 'Escobar CRM') }}</span>
        </a>
      </div>

      <h4 class="mb-1">{{ __('Двухфакторная аутентификация') }} 🔐</h4>
      <p class="mb-6 text-muted">{{ __('Введите код из приложения Google Authenticator для завершения входа.') }}</p>

      <form method="POST" action="{{ route('two-factor.login.store') }}" class="mb-4">
        @csrf

        <div class="mb-6 form-control-validation">
          <label for="code" class="form-label">{{ __('Код аутентификации') }}</label>
          <input
            type="text"
            id="code"
            name="code"
            value="{{ old('code') }}"
            class="form-control @error('code') is-invalid @enderror text-center"
            placeholder="000000"
            maxlength="6"
            pattern="[0-9]{6}"
            inputmode="numeric"
            required
            autofocus
            autocomplete="one-time-code">
          @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <div class="form-text">{{ __('Введите 6-значный код из приложения Google Authenticator') }}</div>
        </div>

        <button type="submit" class="btn btn-primary d-grid w-100">{{ __('Продолжить') }}</button>
      </form>

      <div class="text-center">
        <a href="{{ route('login') }}" class="text-muted">{{ __('Вернуться к входу') }}</a>
      </div>

    </div>
  </div>

  <script>
    // Автоматическая отправка формы при вводе 6 цифр
    document.addEventListener('DOMContentLoaded', function() {
      const codeInput = document.getElementById('code');
      if (codeInput) {
        codeInput.addEventListener('input', function(e) {
          // Удаляем все нецифровые символы
          this.value = this.value.replace(/\D/g, '');
          
          // Если введено 6 цифр, можно автоматически отправить форму
          if (this.value.length === 6) {
            // Не отправляем автоматически, чтобы пользователь мог проверить код
          }
        });
      }
    });
  </script>
@endsection

