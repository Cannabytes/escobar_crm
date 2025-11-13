# 📋 Шпаргалка: Использование системы прав доступа

## 🔍 Проверка прав в коде

### В контроллерах

```php
// Проверить одно разрешение
if (auth()->user()->hasPermission('companies.create')) {
    // Пользователь может создавать компании
}

// Проверить любое из разрешений (ИЛИ)
if (auth()->user()->hasAnyPermission(['companies.edit', 'companies.manage'])) {
    // Пользователь может редактировать ИЛИ полностью управлять компаниями
}

// Проверить все разрешения (И)
if (auth()->user()->hasAllPermissions(['companies.view', 'companies.edit'])) {
    // Пользователь может И просматривать И редактировать компании
}

// Проверить супер-админа
if (auth()->user()->isSuperAdmin()) {
    // Супер-админ имеет все права
}
```

### В Blade шаблонах

```blade
{{-- Проверка одного разрешения --}}
@if(auth()->user()->hasPermission('companies.create'))
    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
        Создать компанию
    </a>
@endif

{{-- Проверка любого из разрешений --}}
@if(auth()->user()->hasAnyPermission(['users.view', 'users.manage']))
    <li class="menu-item">
        <a href="{{ route('admin.users.index') }}">Пользователи</a>
    </li>
@endif

{{-- Проверка наличия прав на управление ролями --}}
@if(auth()->user()->hasAnyPermission(['roles.view', 'roles.manage']))
    <li class="menu-item">
        <a href="{{ route('admin.roles.index') }}">Роли</a>
    </li>
@endif
```

### Middleware в роутах

```php
// Требуется любое из разрешений
Route::get('/companies', [CompanyController::class, 'index'])
    ->middleware('permission:companies.view,companies.manage');

// Требуются ВСЕ разрешения
Route::post('/companies/export', [CompanyController::class, 'export'])
    ->middleware('permission.all:companies.view,companies.export');

// Группа роутов с одним разрешением
Route::middleware(['permission:companies.manage'])->group(function () {
    Route::get('/companies/create', [CompanyController::class, 'create']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);
});
```

## 📝 Работа с ролями в коде

### Получить роль пользователя

```php
$user = auth()->user();

// Получить модель роли
$role = $user->roleModel;

// Название роли
$roleName = $user->roleModel->name;

// Slug роли
$roleSlug = $user->roleModel->slug;

// Все разрешения пользователя
$permissions = $user->getAllPermissions();
```

### Работа с RoleService

```php
use App\Services\RoleService;

// Внедрение через конструктор
public function __construct(
    private readonly RoleService $roleService
) {}

// Получить все роли
$roles = $this->roleService->getAllRoles();

// Получить только активные роли
$activeRoles = $this->roleService->getActiveRoles();

// Создать роль
$role = $this->roleService->createRole([
    'name' => 'Менеджер',
    'slug' => 'manager',
    'description' => 'Управление продажами',
    'is_active' => true,
    'permissions' => [1, 2, 3, 5, 8], // ID разрешений
]);

// Обновить роль
$role = $this->roleService->updateRole($role, [
    'name' => 'Старший менеджер',
    'permissions' => [1, 2, 3, 4, 5, 8, 10],
]);

// Клонировать роль
$newRole = $this->roleService->cloneRole($role, 'Новая роль');

// Удалить роль
$this->roleService->deleteRole($role);
```

### Работа с PermissionService

```php
use App\Services\PermissionService;

// Внедрение через конструктор
public function __construct(
    private readonly PermissionService $permissionService
) {}

// Получить все разрешения с группами
$groupedPermissions = $this->permissionService->getAllGroupedPermissions();

// Получить разрешения для форм
$permissionsForForm = $this->permissionService->getPermissionsForForm();

// Получить разрешение по slug
$permission = $this->permissionService->getPermissionBySlug('companies.create');
```

## 🎨 Примеры использования

### Пример 1: Защита действия в контроллере

```php
public function destroy(Company $company)
{
    // Проверяем право на удаление
    if (!auth()->user()->hasPermission('companies.delete')) {
        abort(403, 'У вас нет прав на удаление компаний.');
    }

    $company->delete();

    return redirect()
        ->route('admin.companies.index')
        ->with('status', 'Компания удалена.');
}
```

### Пример 2: Условная кнопка в шаблоне

```blade
<div class="card-footer d-flex justify-content-between">
    <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
        Назад
    </a>
    
    @if(auth()->user()->hasAnyPermission(['companies.edit', 'companies.manage']))
        <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-primary">
            Редактировать
        </a>
    @endif
    
    @if(auth()->user()->hasAnyPermission(['companies.delete', 'companies.manage']))
        <form action="{{ route('admin.companies.destroy', $company) }}" 
              method="POST" 
              onsubmit="return confirm('Удалить компанию?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Удалить</button>
        </form>
    @endif
</div>
```

### Пример 3: Условное меню в sidebar

```blade
@if(auth()->user()->hasAnyPermission(['companies.view', 'companies.manage']))
    <li class="menu-header">
        <span class="menu-header-text">Компании</span>
    </li>
    
    <li class="menu-item {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
        <a href="{{ route('admin.companies.index') }}" class="menu-link">
            <i class="menu-icon ti ti-building"></i>
            <div>Список компаний</div>
        </a>
    </li>
    
    @if(auth()->user()->hasAnyPermission(['companies.create', 'companies.manage']))
        <li class="menu-item {{ request()->routeIs('admin.companies.create') ? 'active' : '' }}">
            <a href="{{ route('admin.companies.create') }}" class="menu-link">
                <i class="menu-icon ti ti-plus"></i>
                <div>Добавить компанию</div>
            </a>
        </li>
    @endif
@endif
```

### Пример 4: Policy с проверкой прав

```php
<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['companies.view', 'companies.manage']);
    }

    public function view(User $user, Company $company): bool
    {
        return $user->hasAnyPermission(['companies.view', 'companies.manage']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['companies.create', 'companies.manage']);
    }

    public function update(User $user, Company $company): bool
    {
        return $user->hasAnyPermission(['companies.edit', 'companies.manage']);
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->hasAnyPermission(['companies.delete', 'companies.manage']);
    }
}
```

## 🔑 Список slug'ов разрешений

### Компании
```
companies.view
companies.show
companies.create
companies.edit
companies.delete
companies.manage
```

### Лицензии
```
company-licenses.view
company-licenses.edit
```

### Банковские счета
```
company-bank-accounts.view
company-bank-accounts.create
company-bank-accounts.edit
company-bank-accounts.delete
```

### Учётные данные
```
company-credentials.view
company-credentials.create
company-credentials.edit
company-credentials.delete
```

### Доступ к компаниям
```
company-access.view
company-access.create
company-access.delete
```

### Пользователи
```
users.view
users.show
users.create
users.edit
users.delete
users.manage
```

### Роли
```
roles.view
roles.show
roles.create
roles.edit
roles.delete
roles.manage
```

### Логи
```
logs.view
logs.show
```

### Настройки
```
settings.view
settings.edit
```
> Эти разрешения контролируют доступ к модальному окну настроек шаблона. Отдельной страницы настроек нет.

### Профиль
```
profile.view
profile.edit
```

## 🛠️ Полезные Artisan команды

```bash
# Просмотреть все роли
php artisan tinker
>>> App\Models\Role::all()->pluck('name', 'slug');

# Просмотреть разрешения роли
>>> $role = App\Models\Role::find(1);
>>> $role->permissions->pluck('name');

# Проверить права пользователя
>>> $user = App\Models\User::find(1);
>>> $user->getAllPermissions()->pluck('slug');
>>> $user->hasPermission('companies.create');

# Назначить разрешения роли
>>> $role = App\Models\Role::find(2);
>>> $role->syncPermissions([1, 2, 3, 5, 8]);

# Назначить роль пользователю
>>> $user = App\Models\User::find(1);
>>> $user->role_id = 3;
>>> $user->save();
```

## 📌 Лучшие практики

1. **Всегда проверяйте права на стороне сервера**, даже если элементы скрыты в UI
2. **Используйте middleware** для защиты роутов
3. **Проверяйте права в контроллерах** перед выполнением действий
4. **Скрывайте элементы UI**, если у пользователя нет прав
5. **Используйте `hasAnyPermission()`** для гибкости (например, `edit` ИЛИ `manage`)
6. **Логируйте попытки** несанкционированного доступа
7. **Регулярно проверяйте** назначенные права пользователей

## 🚨 Частые ошибки

### ❌ Неправильно
```php
// Проверка только в шаблоне (можно обойти через прямой URL)
@if($user->role === 'admin')
    <a href="/admin/delete">Удалить</a>
@endif
```

### ✅ Правильно
```blade
{{-- Проверка в шаблоне --}}
@if(auth()->user()->hasPermission('companies.delete'))
    <a href="{{ route('admin.companies.destroy', $company) }}">Удалить</a>
@endif
```

```php
// И проверка в контроллере
public function destroy(Company $company)
{
    if (!auth()->user()->hasPermission('companies.delete')) {
        abort(403);
    }
    
    $company->delete();
    // ...
}
```

---

**💡 Совет:** Добавьте эту шпаргалку в закладки для быстрого доступа!

