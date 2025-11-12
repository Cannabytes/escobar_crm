@extends('layouts.admin')

@section('title', __('users.profile_title'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <!-- Заголовок -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-1">
                        <i class="menu-icon tf-icons bx bx-user"></i>
                        {{ __('users.profile_title') }}
                    </h4>
                    <p class="mb-0">{{ __('users.profile_subtitle') }}</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <!-- Левая колонка - Аватар -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <img src="{{ $user->avatar_url }}" 
                                     alt="{{ $user->name }}" 
                                     class="rounded-circle"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                            
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-3">{{ $user->email }}</p>
                            
                            <div class="mb-3">
                                @if($user->isOnline())
                                    <span class="badge bg-success">
                                        <i class="bx bx-circle bx-xs"></i> {{ __('users.online') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        {{ $user->last_activity }}
                                    </span>
                                @endif
                            </div>

                            <!-- Форма загрузки аватара -->
                            <form action="{{ route('admin.profile.avatar.upload') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                                @csrf
                                <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">
                                <button type="button" class="btn btn-primary w-100 mb-2" onclick="document.getElementById('avatarInput').click()">
                                    <i class="bx bx-upload me-1"></i>{{ __('users.upload_avatar') }}
                                </button>
                            </form>

                            @if($user->avatar)
                                <form action="{{ route('admin.profile.avatar.delete') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-label-danger w-100" onclick="return confirm('{{ __('users.remove_avatar') }}?')">
                                        <i class="bx bx-trash me-1"></i>{{ __('users.remove_avatar') }}
                                    </button>
                                </form>
                            @endif

                            <small class="text-muted d-block mt-3">
                                {{ __('users.avatar_requirements') }}
                            </small>
                        </div>
                    </div>

                    <!-- Информация о пользователе -->
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">{{ __('users.role') }}</h6>
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-label-primary">
                                    @if($user->role === 'super_admin')
                                        Super Admin
                                    @elseif($user->role === 'moderator')
                                        Moderator
                                    @else
                                        Viewer
                                    @endif
                                </span>
                            </div>

                            <h6 class="card-title mb-3">Контакты</h6>
                            @if($user->phone)
                                <p class="mb-2">
                                    <i class="bx bx-phone me-2"></i>{{ $user->phone }}
                                </p>
                            @endif
                            @if($user->telegram)
                                <p class="mb-2">
                                    <i class="bx bxl-telegram me-2"></i>{{ $user->telegram }}
                                </p>
                            @endif
                            @if($user->whatsapp)
                                <p class="mb-2">
                                    <i class="bx bxl-whatsapp me-2"></i>{{ $user->whatsapp }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Правая колонка - Формы редактирования -->
                <div class="col-md-8">
                    <!-- Редактирование профиля -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('users.edit_profile') }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="name" class="form-label">{{ __('users.name') }}</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="email" class="form-label">{{ __('users.email') }}</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">{{ __('users.phone') }}</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="telegram" class="form-label">{{ __('users.telegram') }}</label>
                                        <input type="text" class="form-control @error('telegram') is-invalid @enderror" 
                                               id="telegram" name="telegram" value="{{ old('telegram', $user->telegram) }}">
                                        @error('telegram')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="whatsapp" class="form-label">{{ __('users.whatsapp') }}</label>
                                        <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" 
                                               id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}">
                                        @error('whatsapp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i>{{ __('users.save_changes') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Изменение пароля -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('users.change_password') }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.profile.password.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">{{ __('users.current_password') }}</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">{{ __('users.new_password') }}</label>
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                           id="new_password" name="new_password" required>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">{{ __('users.new_password_confirmation') }}</label>
                                    <input type="password" class="form-control" 
                                           id="new_password_confirmation" name="new_password_confirmation" required>
                                </div>

                                <button type="submit" class="btn btn-warning">
                                    <i class="bx bx-lock-alt me-1"></i>{{ __('users.update_password') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Автоматическая отправка формы при выборе файла
    document.getElementById('avatarInput').addEventListener('change', function() {
        if (this.files.length > 0) {
            document.getElementById('avatarForm').submit();
        }
    });
</script>
@endpush
@endsection

