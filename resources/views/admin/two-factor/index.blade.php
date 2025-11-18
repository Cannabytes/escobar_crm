@extends('layouts.admin')

@section('title', __('twofactor.title'))

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Заголовок -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-1">
                        <i class="menu-icon tf-icons bx bx-shield-quarter"></i>
                        {{ __('twofactor.title') }}
                    </h4>
                    <p class="mb-0">{{ __('twofactor.subtitle') }}</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($enabled)
                <!-- 2FA включена -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-check-circle text-success me-2"></i>
                            {{ __('twofactor.enabled') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <i class="bx bx-shield-check me-2"></i>
                            {{ __('twofactor.enabled_message') }}
                        </div>

                        <p class="text-muted mb-4">
                            {{ __('twofactor.enabled_description') }}
                        </p>

                        <!-- Форма отключения -->
                        <div class="card border-warning">
                            <div class="card-header bg-label-warning">
                                <h6 class="mb-0">{{ __('twofactor.disable_title') }}</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">{{ __('twofactor.disable_warning') }}</p>
                                <form action="{{ route('admin.two-factor.disable') }}" method="POST" id="disableForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="password" class="form-label">{{ __('twofactor.confirm_password') }}</label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('{{ __('twofactor.disable_confirm') }}')">
                                        <i class="bx bx-shield-x me-1"></i>
                                        {{ __('twofactor.disable_button') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Настройка 2FA -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-shield-quarter me-2"></i>
                            {{ __('twofactor.setup_title') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            {{ __('twofactor.setup_instructions') }}
                        </div>

                        @if(isset($error))
                            <div class="alert alert-danger">
                                <i class="bx bx-error-circle me-2"></i>
                                {{ $error }}
                            </div>
                        @endif

                        <div class="row">
                            <!-- QR-код -->
                            <div class="col-md-6 mb-4">
                                <h6 class="mb-3">{{ __('twofactor.scan_qr') }}</h6>
                                <div class="text-center">
                                    <div class="bg-light p-4 rounded d-inline-block">
                                        @if(isset($qrCodeUrl) && !empty($qrCodeUrl))
                                            <img src="{{ $qrCodeUrl }}" 
                                                 alt="QR Code" 
                                                 class="img-fluid"
                                                 style="max-width: 250px; min-width: 200px; min-height: 200px;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                            <div style="display: none;" class="text-danger">
                                                <i class="bx bx-error-circle"></i>
                                                {{ __('Ошибка загрузки QR кода') }}
                                            </div>
                                        @else
                                            <div class="text-muted p-4">
                                                <i class="bx bx-error-circle"></i>
                                                {{ __('QR код не может быть сгенерирован') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Секретный ключ -->
                            <div class="col-md-6 mb-4">
                                <h6 class="mb-3">{{ __('twofactor.manual_entry') }}</h6>
                                <div class="input-group mb-3">
                                    <input type="text" 
                                           class="form-control" 
                                           id="secret-key" 
                                           value="{{ $secret }}" 
                                           readonly>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="copySecretKey()">
                                        <i class="bx bx-copy"></i>
                                    </button>
                                </div>
                                <small class="text-muted">{{ __('twofactor.secret_key_hint') }}</small>
                            </div>
                        </div>

                        <!-- Инструкции -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="mb-3">{{ __('twofactor.steps_title') }}</h6>
                                <ol class="mb-0">
                                    <li>{{ __('twofactor.step_1') }}</li>
                                    <li>{{ __('twofactor.step_2') }}</li>
                                    <li>{{ __('twofactor.step_3') }}</li>
                                    <li>{{ __('twofactor.step_4') }}</li>
                                </ol>
                            </div>
                        </div>

                        <!-- Форма подтверждения -->
                        <div class="card border-primary">
                            <div class="card-header bg-label-primary">
                                <h6 class="mb-0">{{ __('twofactor.verify_title') }}</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.two-factor.enable') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="code" class="form-label">{{ __('twofactor.enter_code') }}</label>
                                        <input type="text" 
                                               class="form-control text-center @error('code') is-invalid @enderror" 
                                               id="code" 
                                               name="code" 
                                               placeholder="000000"
                                               maxlength="6"
                                               pattern="[0-9]{6}"
                                               inputmode="numeric"
                                               required
                                               autofocus>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">{{ __('twofactor.code_hint') }}</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-check me-1"></i>
                                        {{ __('twofactor.enable_button') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@push('scripts')
<script>
    function copySecretKey() {
        const secretKeyInput = document.getElementById('secret-key');
        secretKeyInput.select();
        secretKeyInput.setSelectionRange(0, 99999); // Для мобильных устройств
        
        try {
            document.execCommand('copy');
            // Показываем уведомление
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="bx bx-check"></i>';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-outline-secondary');
            
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }, 2000);
        } catch (err) {
            console.error('Ошибка копирования:', err);
        }
    }

    // Автоматическая отправка формы при вводе 6 цифр (опционально)
    document.addEventListener('DOMContentLoaded', function() {
        const codeInput = document.getElementById('code');
        if (codeInput) {
            codeInput.addEventListener('input', function(e) {
                // Удаляем все нецифровые символы
                this.value = this.value.replace(/\D/g, '');
            });
        }
    });
</script>
@endpush
@endsection

