# Инструкция по установке и запуску

## Требования

- PHP 8.4+
- MySQL
- Composer

## Установка

### 1. Клонирование и установка зависимостей

```bash
composer install
```

### 2. Настройка окружения

Скопируйте `.env.example` в `.env`:

```bash
cp .env.example .env
```

Настройте параметры подключения к базе данных в `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=escobar
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Генерация ключа приложения

```bash
php artisan key:generate
```

### 4. Запуск миграций

```bash
php artisan migrate
```

### 5. Создание супер-администратора

```bash
php artisan db:seed --class=SuperAdminSeeder
```

**Данные для входа:**
- Email: `admin@escobar.local`
- Пароль: `password`

⚠️ **ВАЖНО:** Измените пароль после первого входа!

### 6. Создание символической ссылки для файлов

```bash
php artisan storage:link
```

### 7. Запуск сервера разработки

```bash
php artisan serve
```

Приложение будет доступно по адресу: http://localhost:8000

## Структура проекта

```
escobar/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Admin/
│   │   │       ├── CompanyController.php
│   │   │       ├── CompanyBankAccountController.php
│   │   │       ├── CompanyCredentialController.php
│   │   │       ├── CompanyAccessController.php
│   │   │       └── UserController.php
│   │   └── Requests/
│   │       └── Admin/
│   │           └── StoreCompanyRequest.php
│   └── Models/
│       ├── User.php
│       ├── Company.php
│       ├── CompanyBankAccount.php
│       ├── CompanyCredential.php
│       └── CompanyAccess.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── SuperAdminSeeder.php
├── resources/
│   └── views/
│       └── admin/
│           ├── companies/
│           │   ├── index.blade.php
│           │   ├── create.blade.php
│           │   ├── show.blade.php
│           │   └── edit.blade.php
│           └── users/
│               ├── index.blade.php
│               └── create.blade.php
├── routes/
│   └── web.php
├── storage/
│   └── app/
│       └── public/
│           └── licenses/  # Здесь хранятся загруженные лицензии
└── doc/  # Документация
    ├── README.md
    ├── companies/
    │   └── company-management.md
    └── users/
        └── user-management.md
```

## Первые шаги после установки

### 1. Войти в систему
- Откройте http://localhost:8000
- Войдите как супер-администратор

### 2. Создать модераторов
- Перейдите в "Пользователи" → "Создать пользователя"
- Создайте пользователей с ролью "Модератор"

### 3. Добавить первую компанию
- Перейдите в "Компании" → "Добавить компанию"
- Заполните данные и выберите модератора

### 4. Настроить доступы
- Откройте компанию
- Перейдите на вкладку "Доступ"
- Добавьте пользователей с нужными правами

## Роли в системе

### Супер-администратор (`super_admin`)
- Полный доступ ко всем компаниям
- Управление пользователями
- Назначение модераторов
- Просмотр всех логинов/паролей

### Модератор (`moderator`)
- Управление закрепленными компаниями
- Просмотр логинов/паролей своих компаний
- Добавление банковских счетов

### Пользователь (`viewer`)
- Доступ к компаниям по назначению
- Два типа доступа:
  - **view** - только реквизиты (без логинов/паролей)
  - **edit** - полный доступ к данным компании

## Особенности для shared-хостинга

Если вы разворачиваете на shared-хостинге:

1. Загрузите все файлы в корневую директорию
2. Убедитесь, что DocumentRoot указывает на папку `/public`
3. Настройте `.env` для работы с базой данных хостинга
4. Выполните миграции через SSH или панель управления
5. Создайте символическую ссылку: `php artisan storage:link`

## Поддержка

При возникновении проблем:
1. Проверьте логи в `storage/logs/laravel.log`
2. Убедитесь, что права на папки `storage` и `bootstrap/cache` установлены корректно
3. Проверьте настройки `.env`

## Безопасность

⚠️ **Важные рекомендации:**

1. Измените пароль администратора после первого входа
2. Используйте сложные пароли для всех пользователей
3. На продакшене отключите режим отладки в `.env`: `APP_DEBUG=false`
4. Настройте регулярное резервное копирование базы данных
5. Ограничьте доступ к файлам `.env` и конфигурации

## Обновление

При получении обновлений:

```bash
# Обновить зависимости
composer install

# Запустить новые миграции
php artisan migrate

# Очистить кеш
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Лицензия

Проект разработан специально для аудиторской конторы.

