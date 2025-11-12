# Онлайн статус пользователей

## Описание

Система отслеживания онлайн статуса позволяет в реальном времени видеть, какие пользователи активны в системе, и когда они были в последний раз.

## Как это работает

### Отслеживание активности

1. **Middleware LogUserActivity** автоматически обновляет поле `last_activity_at` при каждом запросе пользователя
2. Обновление происходит только если прошло более 5 минут с последнего обновления (оптимизация БД)
3. Middleware зарегистрирован глобально и работает для всех аутентифицированных пользователей

### Определение онлайн статуса

Пользователь считается **онлайн**, если его последняя активность была менее 5 минут назад.

```php
public function isOnline(): bool
{
    if (!$this->last_activity_at) {
        return false;
    }
    
    return $this->last_activity_at->diffInMinutes(now()) < 5;
}
```

## Отображение статуса

### В списке пользователей

```php
@if($user->isOnline())
    <span class="badge bg-success">
        <i class="bx bx-circle bx-xs"></i> {{ __('users.online') }}
    </span>
@else
    <small class="text-muted">
        {{ $user->last_activity }}
    </small>
@endif
```

### Форматы времени последней активности

- **Онлайн** - если активность < 5 минут назад
- **X мин. назад** - если < 60 минут
- **X час(а/ов) назад** - если < 24 часов
- **X дня/дней назад** - если >= 24 часов

Примеры:
- "Онлайн"
- "13 мин. назад"
- "1 час назад"
- "2 часа назад"
- "19 часов назад"
- "2 дня назад"

### Визуальные индикаторы

#### Аватар с индикатором онлайн
```html
<div class="avatar {{ $user->isOnline() ? 'avatar-online' : '' }}">
    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle">
</div>
```

Класс `avatar-online` добавляет зеленую точку в углу аватара.

## API модели User

### Методы

```php
// Проверка онлайн статуса
$user->isOnline(); // bool

// Получить читаемое время последней активности
$user->last_activity; // string (атрибут)
```

### Внутренний метод

```php
protected function getHumanReadableTime($datetime): string
{
    $diff = $datetime->diffInMinutes(now());
    
    if ($diff < 60) {
        return __('users.minutes_ago', ['count' => $diff]);
    }
    
    $hours = floor($diff / 60);
    if ($hours < 24) {
        return __('users.hours_ago', ['count' => $hours]);
    }
    
    $days = floor($hours / 24);
    return __('users.days_ago', ['count' => $days]);
}
```

## Поле в БД

```php
Schema::table('users', function (Blueprint $table) {
    $table->timestamp('last_activity_at')->nullable();
});
```

## Многоязычность

### Английский (lang/en/users.php)
```php
'online' => 'Online',
'offline' => 'Offline',
'never_active' => 'Never active',
'minutes_ago' => ':count min. ago|:count min. ago',
'hours_ago' => ':count hour ago|:count hours ago',
'days_ago' => ':count day ago|:count days ago',
```

### Русский (lang/ru/users.php)
```php
'online' => 'Онлайн',
'offline' => 'Оффлайн',
'never_active' => 'Никогда не был активен',
'minutes_ago' => ':count мин. назад|:count мин. назад|:count мин. назад',
'hours_ago' => ':count час назад|:count часа назад|:count часов назад',
'days_ago' => ':count день назад|:count дня назад|:count дней назад',
```

## Места использования

1. **Список пользователей** (`/admin/users`) - колонка "Статус"
2. **Навбар** - индикатор на аватаре текущего пользователя
3. **Профиль пользователя** - отображение статуса

## Производительность

### Оптимизация обновлений

Middleware обновляет `last_activity_at` с интервалом 5 минут:

```php
if (
    !$user->last_activity_at || 
    $user->last_activity_at->diffInMinutes(now()) >= 5
) {
    $user->update(['last_activity_at' => now()]);
}
```

Это предотвращает избыточные записи в БД при каждом запросе пользователя.

### Индексы

Для быстрых запросов по последней активности можно добавить индекс:
```php
$table->index('last_activity_at');
```

## Расширение функционала

### Возможные улучшения:
1. Добавить WebSocket для обновления статуса в реальном времени
2. Показывать количество онлайн пользователей
3. Уведомления при входе/выходе пользователей
4. История активности пользователя
5. Детальная аналитика времени работы

