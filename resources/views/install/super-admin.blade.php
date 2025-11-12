@extends('layouts.guest')

@section('title', __('Создание супер-администратора'))

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="app-brand justify-content-center mb-6">
        <span class="app-brand-text demo text-heading fw-bold text-uppercase">{{ config('app.name', 'Escobar CRM') }}</span>
      </div>

      <h4 class="mb-1">{{ __('Первичная настройка CRM') }} ⚙️</h4>
      <p class="mb-6 text-muted">{{ __('Создайте первый аккаунт супер-администратора для доступа в систему.') }}</p>

      <form method="POST" action="{{ route('install.super-admin.store') }}" class="mb-4">
        @csrf

        <div class="mb-6 form-control-validation">
          <label for="name" class="form-label">{{ __('Имя и фамилия') }}</label>
          <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name') }}"
            class="form-control @error('name') is-invalid @enderror"
            placeholder="{{ __('Иван Иванов') }}"
            required
            autofocus>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-6 form-control-validation">
          <label for="email" class="form-label">{{ __('Рабочий email') }}</label>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="admin@example.com"
            required>
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
              minlength="6"
              required>
            <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
          </div>
          @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-6 form-password-toggle form-control-validation">
          <label class="form-label" for="password_confirmation">{{ __('Повторите пароль') }}</label>
          <div class="input-group input-group-merge">
            <input
              type="password"
              id="password_confirmation"
              name="password_confirmation"
              class="form-control"
              placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
              minlength="6"
              required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary d-grid w-100">{{ __('Создать супер-администратора') }}</button>
      </form>
    </div>
  </div>
@endsection


