@extends('layouts.admin')

@section('title', __('Создание новой роли'))

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-icon btn-label-secondary me-3">
              <i class="icon-base ti tabler-arrow-left"></i>
            </a>
            <div>
              <h5 class="card-title mb-1">{{ __('Создание новой роли') }}</h5>
              <small class="text-muted">{{ __('Настройте название роли и выберите разрешения') }}</small>
            </div>
          </div>
        </div>
      </div>

      <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="row">
          <div class="col-lg-4 col-md-12 mb-4">
            <div class="card">
              <div class="card-body">
                <h6 class="card-title mb-3">{{ __('Основная информация') }}</h6>

                <div class="mb-3">
                  <label for="name" class="form-label">{{ __('Название роли') }} <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    placeholder="{{ __('Например: Менеджер продаж') }}"
                    required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="text-muted">{{ __('Понятное название для роли') }}</small>
                </div>

                <div class="mb-3">
                  <label for="slug" class="form-label">{{ __('Slug') }} <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="slug"
                    name="slug"
                    class="form-control @error('slug') is-invalid @enderror"
                    value="{{ old('slug') }}"
                    placeholder="{{ __('manager-prodazh') }}"
                    required>
                  @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="text-muted">{{ __('Уникальный идентификатор (латиницей)') }}</small>
                </div>

                <div class="mb-3">
                  <label for="description" class="form-label">{{ __('Описание') }}</label>
                  <textarea
                    id="description"
                    name="description"
                    class="form-control @error('description') is-invalid @enderror"
                    rows="4"
                    placeholder="{{ __('Краткое описание роли и её назначения') }}">{{ old('description') }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <div class="form-check form-switch">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="is_active"
                      name="is_active"
                      value="1"
                      {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                      {{ __('Активная роль') }}
                    </label>
                  </div>
                  <small class="text-muted">{{ __('Неактивные роли нельзя назначить пользователям') }}</small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-8 col-md-12 mb-4">
            <div class="card">
              <div class="card-body">
                <h6 class="card-title mb-3">
                  <i class="icon-base ti tabler-lock me-2"></i>{{ __('Разрешения роли') }}
                </h6>
                <p class="text-muted mb-4">{{ __('Выберите разрешения, которые будут доступны пользователям с этой ролью') }}</p>

                @if (empty($permissionGroups))
                  <div class="alert alert-warning">
                    <i class="icon-base ti tabler-alert-triangle me-2"></i>
                    {{ __('Разрешения не найдены. Выполните сидер PermissionSeeder.') }}
                  </div>
                @else
                  <div class="accordion" id="permissionsAccordion">
                    @foreach ($permissionGroups as $groupId => $data)
                      @php
                        $group = $data['group'];
                        $permissions = $data['permissions'];
                      @endphp
                      <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $groupId }}">
                          <button
                            class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $groupId }}"
                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $groupId }}">
                            <i class="icon-base ti tabler-shield me-2"></i>
                            <strong>{{ $group->name }}</strong>
                            @if ($group->description)
                              <span class="text-muted ms-2 small">{{ $group->description }}</span>
                            @endif
                            <span class="badge bg-label-secondary ms-auto me-2">{{ $permissions->count() }}</span>
                          </button>
                        </h2>
                        <div
                          id="collapse{{ $groupId }}"
                          class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                          aria-labelledby="heading{{ $groupId }}"
                          data-bs-parent="#permissionsAccordion">
                          <div class="accordion-body">
                            <div class="row g-3">
                              @foreach ($permissions as $permission)
                                <div class="col-md-6">
                                  <div class="form-check">
                                    <input
                                      class="form-check-input"
                                      type="checkbox"
                                      name="permissions[]"
                                      value="{{ $permission->id }}"
                                      id="permission{{ $permission->id }}"
                                      {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission{{ $permission->id }}">
                                      <strong>{{ $permission->name }}</strong>
                                      <span class="badge bg-label-{{ $permission->type === 'view' ? 'info' : ($permission->type === 'create' ? 'success' : ($permission->type === 'edit' ? 'warning' : ($permission->type === 'delete' ? 'danger' : 'primary'))) }} ms-1">
                                        {{ $permission->getTypeLabel() }}
                                      </span>
                                      @if ($permission->description)
                                        <div class="text-muted small">{{ $permission->description }}</div>
                                      @endif
                                    </label>
                                  </div>
                                </div>
                              @endforeach
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body d-flex justify-content-between">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-label-secondary">
                  <i class="icon-base ti tabler-x me-1"></i>{{ __('Отмена') }}
                </a>
                <button type="submit" class="btn btn-primary">
                  <i class="icon-base ti tabler-check me-1"></i>{{ __('Создать роль') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Автоматическая генерация slug из названия
    document.getElementById('name').addEventListener('input', function(e) {
      const slug = e.target.value
        .toLowerCase()
        .replace(/[а-я]/g, function(char) {
          const cyr2lat = {
            'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'e', 'ж': 'zh', 'з': 'z',
            'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r',
            'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh',
            'щ': 'shch', 'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya'
          };
          return cyr2lat[char] || char;
        })
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
      
      // Обновляем только если поле slug пустое
      const slugField = document.getElementById('slug');
      if (!slugField.dataset.manuallyEdited) {
        slugField.value = slug;
      }
    });

    // Отмечаем, что slug редактировался вручную
    document.getElementById('slug').addEventListener('input', function() {
      this.dataset.manuallyEdited = 'true';
    });
  </script>
@endsection

