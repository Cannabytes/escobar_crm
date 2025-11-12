# Персональные настройки шаблона

## Описание

Каждый пользователь может настроить внешний вид интерфейса под себя. Все настройки сохраняются в БД и применяются автоматически при входе.

## Возможности

### Настройки темы
- **Тема (Theme)**: Светлая, Темная, Системная
- **Стиль (Style)**: Light, Dark, Bordered

### Настройки макета
- **Тип макета (Layout Type)**: Вертикальный, Горизонтальный
- **Тип навбара (Navbar Type)**: Фиксированный, Статичный, Скрытый
- **Тип футера (Footer Type)**: Фиксированный, Статичный, Скрытый

### Настройки поведения
- **Фиксированный навбар (Layout Navbar Fixed)**: Вкл/Выкл
- **Показывать выпадающее меню при наведении (Show Dropdown on Hover)**: Вкл/Выкл

### Язык интерфейса
- **Английский (English)**
- **Русский (Russian)**

## Структура

### Модель UserSettings

Таблица `user_settings` хранит настройки для каждого пользователя:

```php
Schema::create('user_settings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
    
    // Настройки шаблона
    $table->string('theme', 20)->default('light');
    $table->string('style', 20)->default('light');
    $table->string('layout_type', 20)->default('vertical');
    $table->string('navbar_type', 20)->default('fixed');
    $table->string('footer_type', 20)->default('fixed');
    $table->boolean('layout_navbar_fixed')->default(true);
    $table->boolean('show_dropdown_on_hover')->default(true);
    
    // Язык интерфейса
    $table->string('language', 10)->default('en');
    
    // Прочие настройки (расширяемо)
    $table->json('custom_settings')->nullable();
    
    $table->timestamps();
});
```

### Связь с User

```php
// В модели User
public function settings(): HasOne
{
    return $this->hasOne(UserSettings::class);
}

public function getOrCreateSettings(): UserSettings
{
    if (!$this->settings) {
        $this->settings()->create([]);
    }
    
    return $this->settings;
}
```

## API модели UserSettings

### Методы

```php
// Получить значение настройки
$settings->get('theme'); // 'light'
$settings->get('custom_key', 'default_value'); // с дефолтом

// Установить значение настройки
$settings->set('theme', 'dark');
$settings->set('custom_key', 'value'); // в custom_settings

// Сбросить все настройки к значениям по умолчанию
$settings->reset();

// Получить конфигурацию для применения к шаблону
$settings->toTemplateConfig(); // массив для JavaScript
```

### Пример использования

```php
$user = auth()->user();
$settings = $user->getOrCreateSettings();

// Получение настроек
$theme = $settings->theme;
$language = $settings->language;

// Изменение настроек
$settings->update([
    'theme' => 'dark',
    'language' => 'ru'
]);

// Применение к шаблону
$config = $settings->toTemplateConfig();
// ['theme' => 'dark', 'style' => 'light', ...]
```

## Контроллер SettingsController

**Маршруты:**
- `GET /admin/settings` - страница настроек
- `PUT /admin/settings` - сохранение настроек
- `POST /admin/settings/reset` - сброс к значениям по умолчанию

### Валидация

```php
'theme' => 'required|in:light,dark,system'
'style' => 'required|in:light,dark,bordered'
'layout_type' => 'required|in:vertical,horizontal'
'navbar_type' => 'required|in:fixed,static,hidden'
'footer_type' => 'required|in:fixed,static,hidden'
'layout_navbar_fixed' => 'boolean'
'show_dropdown_on_hover' => 'boolean'
'language' => 'required|in:en,ru'
```

## Применение настроек

### В layout (будущая реализация)

```php
@php
    $settings = auth()->user()->getOrCreateSettings();
    $config = $settings->toTemplateConfig();
@endphp

<script>
    // Применение настроек при загрузке страницы
    window.templateSettings = @json($config);
</script>
```

### Динамическое применение языка

```php
// В middleware или AppServiceProvider
app()->setLocale(auth()->user()->settings->language ?? 'en');
```

## Логирование

Все изменения настроек логируются:
- Сохранение настроек → ACTION_UPDATE (info)
- Сброс настроек → 'settings_reset' (info)

Логи включают старые и новые значения для отслеживания изменений.

## Многоязычность

Переводы находятся в:
- `lang/en/settings.php`
- `lang/ru/settings.php`

## Значения по умолчанию

```php
'theme' => 'light'
'style' => 'light'
'layout_type' => 'vertical'
'navbar_type' => 'fixed'
'footer_type' => 'fixed'
'layout_navbar_fixed' => true
'show_dropdown_on_hover' => true
'language' => 'en'
```

## Расширение настроек

### Добавление кастомных настроек

Используйте поле `custom_settings` (JSON) для хранения дополнительных настроек:

```php
$settings->set('sidebar_collapsed', true);
$settings->set('notifications_enabled', false);
$settings->save();

// Получение
$isCollapsed = $settings->get('sidebar_collapsed', false);
```

### Добавление новых полей

Если нужны часто используемые настройки, лучше добавить их как отдельные колонки:

```php
Schema::table('user_settings', function (Blueprint $table) {
    $table->boolean('notifications_enabled')->default(true);
    $table->integer('items_per_page')->default(25);
});
```

Затем добавить в `$fillable` модели:
```php
protected $fillable = [
    // ... существующие поля
    'notifications_enabled',
    'items_per_page',
];
```

## Доступ

- Страница доступна всем аутентифицированным пользователям
- Каждый пользователь может настраивать только свои параметры
- Ссылка доступна в выпадающем меню навбара

## Интеграция с Template Customizer

Для интеграции с Template Customizer шаблона Vuexy необходимо:

1. Загружать сохраненные настройки при инициализации
2. Синхронизировать изменения из Template Customizer с БД
3. Применять настройки к CSS переменным и классам
4. Обеспечить live preview изменений

Подробная реализация зависит от конкретного шаблона.

