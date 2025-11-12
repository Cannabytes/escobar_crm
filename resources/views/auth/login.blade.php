@extends('layouts.guest')

@section('title', __('Вход в CRM'))

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

      <h4 class="mb-1">{{ __('С возвращением!') }} 👋</h4>
      <p class="mb-6 text-muted">{{ __('Войдите в учетную запись, чтобы продолжить работу с CRM.') }}</p>

      @if (session('status'))
        <div class="alert alert-success alert-dismissible" role="alert">
          {{ session('status') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Закрыть') }}"></button>
        </div>
      @endif

      <form method="POST" action="{{ route('login.store') }}" class="mb-4">
        @csrf

        <div class="mb-6 form-control-validation">
          <label for="email" class="form-label">{{ __('Email') }}</label>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="you@example.com"
            required
            autofocus>
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-6 form-password-toggle form-control-validation">
          <label class="form-label" for="password">{{ __('Пароль') }}</label>
          <div class="input-group input-group-merge">
            <input
              type="password"
              id="password"
              name="password"
              class="form-control @error('password') is-invalid @enderror"
              placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
              required>
            <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
          </div>
          @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="my-6">
          <div class="d-flex justify-content-between">
            <div class="form-check mb-0 ms-2">
              <input type="checkbox" id="remember" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
              <label class="form-check-label" for="remember">{{ __('Запомнить меня') }}</label>
            </div>
            <span class="text-muted small">{{ __('Забыли пароль? Обратитесь к администратору.') }}</span>
          </div>
        </div>

        <button type="submit" class="btn btn-primary d-grid w-100">{{ __('Войти') }}</button>
      </form>

      <p class="text-center mb-0">
        <span>{{ __('Нет аккаунта?') }}</span>
        <a href="{{ route('register') }}">
          <span>{{ __('Создать учетную запись') }}</span>
        </a>
      </p>
    </div>
  </div>
@endsection


