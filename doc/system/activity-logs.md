# Система логирования активности

## Описание

Система логирования позволяет отслеживать все действия пользователей в CRM, включая создание, обновление и удаление записей, изменение настроек, входы и выходы из системы.

## Структура

### Модель ActivityLog

Модель `ActivityLog` хранит информацию о всех действиях в системе:

- **user_id** - ID пользователя, выполнившего действие
- **action** - тип действия (create, update, delete, login, logout и т.д.)
- **model_type** - тип модели, с которой взаимодействовали
- **model_id** - ID записи модели
- **description** - текстовое описание действия
- **old_values** - значения до изменения (JSON)
- **new_values** - новые значения (JSON)
- **ip_address** - IP адрес пользователя
- **user_agent** - User Agent браузера
- **url** - URL запроса
- **http_method** - HTTP метод (GET, POST, PUT, DELETE)
- **level** - уровень важности (info, warning, error, critical)
- **metadata** - дополнительные метаданные (JSON)

### Trait LogsActivity

Trait `LogsActivity` автоматически логирует действия для моделей:

```php
use App\Traits\LogsActivity;

class Company extends Model
{
    use LogsActivity;
    
    // Настройка исключений из логов
    protected array $excludeFromLogs = ['password', 'remember_token'];
}
```

### Middleware LogUserActivity

Middleware автоматически обновляет время последней активности пользователя при каждом запросе (с интервалом 5 минут).

## Использование

### Просмотр логов

Доступно только для супер администраторов:
- **URL**: `/admin/logs`
- **Фильтры**: по пользователю, действию, модели, уровню, датам
- **Поиск**: по описанию, IP адресу, URL

### Создание логов вручную

```php
use App\Models\ActivityLog;

// Простой лог
ActivityLog::log(
    action: ActivityLog::ACTION_CREATE,
    model: $company,
    description: 'Компания создана'
);

// Лог с дополнительными данными
ActivityLog::log(
    action: 'custom_action',
    model: $user,
    description: 'Специальное действие',
    level: ActivityLog::LEVEL_WARNING,
    metadata: ['key' => 'value']
);

// Через модель
$user->logAction(
    action: 'profile_viewed',
    description: 'Пользователь просмотрел профиль'
);
```

## Уровни логирования

- **info** - информационные сообщения (обычные действия)
- **warning** - предупреждения (изменение пароля, удаление)
- **error** - ошибки
- **critical** - критические события

## Константы действий

- `ACTION_CREATE` - создание
- `ACTION_UPDATE` - обновление
- `ACTION_DELETE` - удаление
- `ACTION_VIEW` - просмотр
- `ACTION_LOGIN` - вход в систему
- `ACTION_LOGOUT` - выход из системы
- `ACTION_EXPORT` - экспорт данных
- `ACTION_IMPORT` - импорт данных
- `ACTION_RESTORE` - восстановление

## Многоязычность

Система поддерживает английский и русский языки. Переводы находятся в:
- `lang/en/logs.php`
- `lang/ru/logs.php`

## Индексы БД

Для быстрой работы созданы индексы:
- `user_id, created_at`
- `action, created_at`
- `model_type, model_id`
- `level`

## API методы модели

### Scopes

```php
// Логи конкретного пользователя
$logs = ActivityLog::forUser($userId)->get();

// Логи по действию
$logs = ActivityLog::forAction('create')->get();

// Логи по модели
$logs = ActivityLog::forModel('App\\Models\\Company', $companyId)->get();

// Логи по уровню
$logs = ActivityLog::forLevel('warning')->get();

// Логи за период
$logs = ActivityLog::inDateRange($startDate, $endDate)->get();
```

### Атрибуты

- `action_name` - читаемое имя действия
- `model_name` - читаемое имя модели
- `level_color` - цвет уровня для UI (info, warning, danger, dark)

