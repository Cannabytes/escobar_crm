@extends('layouts.admin')

@section('title', __('settings.page_title'))

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Заголовок -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-1">
                        <i class="menu-icon tf-icons bx bx-cog"></i>
                        {{ __('settings.page_title') }}
                    </h4>
                    <p class="mb-0">{{ __('settings.page_subtitle') }}</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Форма настроек -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Тема -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label">{{ __('settings.theme') }}</label>
                                <div class="row g-3">
                                    <div class="col-4">
                                        <div class="form-check custom-option custom-option-icon">
                                            <label class="form-check-label custom-option-content" for="theme_light">
                                                <span class="custom-option-body">
                                                    <i class="bx bx-sun bx-lg"></i>
                                                    <span class="custom-option-title">{{ __('settings.theme_light') }}</span>
                                                </span>
                                                <input class="form-check-input" type="radio" name="theme" value="light" id="theme_light" 
                                                       {{ $settings->theme === 'light' ? 'checked' : '' }}>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check custom-option custom-option-icon">
                                            <label class="form-check-label custom-option-content" for="theme_dark">
                                                <span class="custom-option-body">
                                                    <i class="bx bx-moon bx-lg"></i>
                                                    <span class="custom-option-title">{{ __('settings.theme_dark') }}</span>
                                                </span>
                                                <input class="form-check-input" type="radio" name="theme" value="dark" id="theme_dark" 
                                                       {{ $settings->theme === 'dark' ? 'checked' : '' }}>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check custom-option custom-option-icon">
                                            <label class="form-check-label custom-option-content" for="theme_system">
                                                <span class="custom-option-body">
                                                    <i class="bx bx-desktop bx-lg"></i>
                                                    <span class="custom-option-title">{{ __('settings.theme_system') }}</span>
                                                </span>
                                                <input class="form-check-input" type="radio" name="theme" value="system" id="theme_system" 
                                                       {{ $settings->theme === 'system' ? 'checked' : '' }}>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Стиль -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label">{{ __('settings.style') }}</label>
                                <select class="form-select" name="style">
                                    <option value="light" {{ $settings->style === 'light' ? 'selected' : '' }}>
                                        {{ __('settings.style_light') }}
                                    </option>
                                    <option value="dark" {{ $settings->style === 'dark' ? 'selected' : '' }}>
                                        {{ __('settings.style_dark') }}
                                    </option>
                                    <option value="bordered" {{ $settings->style === 'bordered' ? 'selected' : '' }}>
                                        {{ __('settings.style_bordered') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Тип макета -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label">{{ __('settings.layout_type') }}</label>
                                <select class="form-select" name="layout_type">
                                    <option value="vertical" {{ $settings->layout_type === 'vertical' ? 'selected' : '' }}>
                                        {{ __('settings.layout_vertical') }}
                                    </option>
                                    <option value="horizontal" {{ $settings->layout_type === 'horizontal' ? 'selected' : '' }}>
                                        {{ __('settings.layout_horizontal') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Тип навбара -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label">{{ __('settings.navbar_type') }}</label>
                                <select class="form-select" name="navbar_type">
                                    <option value="fixed" {{ $settings->navbar_type === 'fixed' ? 'selected' : '' }}>
                                        {{ __('settings.navbar_fixed') }}
                                    </option>
                                    <option value="static" {{ $settings->navbar_type === 'static' ? 'selected' : '' }}>
                                        {{ __('settings.navbar_static') }}
                                    </option>
                                    <option value="hidden" {{ $settings->navbar_type === 'hidden' ? 'selected' : '' }}>
                                        {{ __('settings.navbar_hidden') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Тип футера -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label">{{ __('settings.footer_type') }}</label>
                                <select class="form-select" name="footer_type">
                                    <option value="fixed" {{ $settings->footer_type === 'fixed' ? 'selected' : '' }}>
                                        {{ __('settings.footer_fixed') }}
                                    </option>
                                    <option value="static" {{ $settings->footer_type === 'static' ? 'selected' : '' }}>
                                        {{ __('settings.footer_static') }}
                                    </option>
                                    <option value="hidden" {{ $settings->footer_type === 'hidden' ? 'selected' : '' }}>
                                        {{ __('settings.footer_hidden') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Язык -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label">{{ __('settings.language') }}</label>
                                <select class="form-select" name="language">
                                    <option value="en" {{ $settings->language === 'en' ? 'selected' : '' }}>
                                        {{ __('settings.language_en') }}
                                    </option>
                                    <option value="ru" {{ $settings->language === 'ru' ? 'selected' : '' }}>
                                        {{ __('settings.language_ru') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Чекбоксы -->
                            <div class="col-12 mb-4">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="layout_navbar_fixed" 
                                           id="layout_navbar_fixed" {{ $settings->layout_navbar_fixed ? 'checked' : '' }}>
                                    <label class="form-check-label" for="layout_navbar_fixed">
                                        {{ __('settings.layout_navbar_fixed') }}
                                    </label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="show_dropdown_on_hover" 
                                           id="show_dropdown_on_hover" {{ $settings->show_dropdown_on_hover ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_dropdown_on_hover">
                                        {{ __('settings.show_dropdown_on_hover') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>{{ __('settings.save_settings') }}
                            </button>
                            
                            <button type="button" class="btn btn-label-danger" onclick="resetSettings()">
                                <i class="bx bx-reset me-1"></i>{{ __('settings.reset_settings') }}
                            </button>
                        </div>
                    </form>

                    <!-- Скрытая форма для сброса -->
                    <form id="resetForm" action="{{ route('admin.settings.reset') }}" method="POST" class="d-none">
                        @csrf
                        @method('POST')
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    function resetSettings() {
        if (confirm('{{ __('settings.reset_settings') }}?')) {
            document.getElementById('resetForm').submit();
        }
    }
</script>
@endpush
@endsection

