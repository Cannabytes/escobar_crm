# Управление профилем пользователя

## Описание

Модуль позволяет каждому пользователю управлять своим профилем, включая редактирование личных данных, изменение пароля и загрузку аватара.

## Возможности

### Редактирование профиля

Пользователь может изменять:
- **Полное имя** (name)
- **Email** (с проверкой уникальности)
- **Телефон** (phone)
- **Telegram** (telegram username)
- **WhatsApp** (whatsapp номер)

### Изменение пароля

- Требуется текущий пароль для подтверждения
- Новый пароль должен соответствовать политике безопасности Laravel
- Подтверждение нового пароля
- Логирование изменения пароля с уровнем WARNING

### Управление аватаром

#### Загрузка аватара
- Поддерживаемые форматы: JPG, JPEG, PNG, GIF
- Максимальный размер: 2MB
- Автоматическое удаление старого аватара при загрузке нового
- Хранение в `storage/app/public/avatars/`

#### Удаление аватара
- Полное удаление файла из хранилища
- Возврат к аватару по умолчанию

#### Аватар по умолчанию
Если у пользователя нет загруженного аватара, используется сервис UI Avatars для генерации аватара с инициалами:
- URL формат: `https://ui-avatars.com/api/?name={name}&color=7F9CF5&background=EBF4FF&size=128`

## Структура

### Контроллер: ProfileController

**Маршруты:**
- `GET /admin/profile` - отображение страницы профиля
- `PUT /admin/profile` - обновление информации профиля
- `PUT /admin/profile/password` - изменение пароля
- `POST /admin/profile/avatar` - загрузка аватара
- `DELETE /admin/profile/avatar` - удаление аватара

### Поля в таблице users

```php
'avatar' => 'nullable|string' // путь к файлу аватара
'last_activity_at' => 'nullable|timestamp' // время последней активности
```

### Методы модели User

```php
// Получить URL аватара
$user->avatar_url; 

// Проверить онлайн статус
$user->isOnline(); // true если активность < 5 минут назад

// Получить читаемое время последней активности
$user->last_activity; // "Онлайн" / "15 мин. назад" / "2 часа назад"
```

## Валидация

### Обновление профиля
```php
'name' => 'required|string|max:255'
'email' => 'required|email|max:255|unique:users,email,{user_id}'
'phone' => 'nullable|string|max:20'
'telegram' => 'nullable|string|max:255'
'whatsapp' => 'nullable|string|max:255'
```

### Изменение пароля
```php
'current_password' => 'required|string'
'new_password' => 'required|confirmed|Password::defaults()'
```

### Загрузка аватара
```php
'avatar' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048'
```

## Логирование

Все изменения профиля логируются:
- Обновление профиля → ACTION_UPDATE (info)
- Изменение пароля → 'password_changed' (warning)
- Загрузка аватара → 'avatar_uploaded' (info)
- Удаление аватара → 'avatar_removed' (info)

## Многоязычность

Переводы находятся в:
- `lang/en/users.php`
- `lang/ru/users.php`

Доступные ключи:
- `profile_title`, `profile_subtitle`
- `profile_updated`, `password_updated`
- `avatar_uploaded`, `avatar_removed`
- `invalid_current_password`
- `online`, `offline`, `never_active`
- `minutes_ago`, `hours_ago`, `days_ago`

## Доступ

- Страница доступна всем аутентифицированным пользователям
- Пользователь может редактировать только свой профиль
- Ссылка на профиль доступна в выпадающем меню навбара

## Особенности реализации

### Автозагрузка аватара
При выборе файла форма автоматически отправляется через JavaScript:
```javascript
document.getElementById('avatarInput').addEventListener('change', function() {
    if (this.files.length > 0) {
        document.getElementById('avatarForm').submit();
    }
});
```

### Symlink для storage
Для доступа к загруженным аватарам необходимо создать symlink:
```bash
php artisan storage:link
```

### Оптимизация обновления активности
Middleware обновляет `last_activity_at` только если прошло более 5 минут с последнего обновления, чтобы избежать избыточных запросов к БД.

