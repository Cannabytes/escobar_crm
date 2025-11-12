# Управление компаниями

## Описание
Система управления компаниями аудиторской конторы с гибкими правами доступа.

## Структура БД

### Таблица `companies`
Основная информация о компаниях:
- `id` - ID компании
- `name` - Название компании
- `country` - Страна регистрации
- `moderator_id` - ID модератора компании (FK на users)
- `license_file` - Путь к файлу лицензии
- `created_at`, `updated_at` - Временные метки

### Таблица `company_credentials`
Конфиденциальные учетные данные (логины/пароли):
- `id` - ID записи
- `company_id` - ID компании (FK)
- `login` - Логин
- `login_id` - ID логина
- `password` - Пароль
- `email` - Email
- `email_password` - Пароль от email
- `online_banking_url` - Ссылка на онлайн-банкинг
- `manager_name` - Имя менеджера
- `manager_phone` - Телефон менеджера

**Доступ:** Только модератор компании и супер-админ

### Таблица `company_bank_accounts` (СТАРАЯ СТРУКТУРА)
Банковские реквизиты компании (используется для обратной совместимости):
- `id` - ID счета
- `company_id` - ID компании (FK)
- `bank_name` - Название банка
- `country` - Страна
- `company_name` - Название компании в банке
- `currency` - Валюта счета
- `account_number` - Номер счета
- `iban` - IBAN
- `swift` - SWIFT код
- `sort_order` - Порядок сортировки

**Доступ:** Все пользователи с доступом к компании

### Таблица `banks` (НОВАЯ СТРУКТУРА)
Информация о банках компании:
- `id` - ID банка
- `company_id` - ID компании (FK)
- `name` - Название банка (Монобанк, ПриватБанк и т.д.)
- `country` - Страна банка
- `bank_code` - Код банка (MFI, SWIFT и т.д.)
- `notes` - Дополнительные примечания
- `sort_order` - Порядок сортировки
- `created_at`, `updated_at` - Временные метки

**Доступ:** Модератор и пользователи с правом `edit`

### Таблица `bank_details` (НОВАЯ СТРУКТУРА)
Реквизиты банков (один банк может иметь несколько реквизитов):
- `id` - ID реквизита
- `bank_id` - ID банка (FK)
- `detail_type` - Тип реквизита: `account_number`, `iban`, `swift`, `recipient_name`, `recipient_address`, `bank_address`, `reference_code`, `custom`
- `detail_key` - Ключ/название реквизита (напр. "Счет", "Получатель", "Адрес")
- `detail_value` - Значение реквизита
- `currency` - Валюта (опционально, для счетов)
- `notes` - Примечания
- `is_primary` - Является ли основным реквизитом (boolean)
- `sort_order` - Порядок сортировки
- `created_at`, `updated_at` - Временные метки

**Доступ:** 
- Просмотр: Все пользователи с доступом к компании
- Редактирование: Модератор и пользователи с правом `edit`

### Таблица `company_user_access`
Права доступа пользователей к компаниям:
- `id` - ID записи
- `company_id` - ID компании (FK)
- `user_id` - ID пользователя (FK)
- `access_type` - Тип доступа: `view` (просмотр) или `edit` (редактирование)

## Модели

### Company
Модель компании с методами проверки прав:
- `canUserEdit(User $user)` - Может ли пользователь редактировать
- `canUserView(User $user)` - Может ли пользователь просматривать
- `canUserViewCredentials(User $user)` - Может ли видеть логины/пароли

Связи:
- `moderator()` - Модератор компании
- `bankAccounts()` - Банковские счета (старая структура)
- `banks()` - Банки (новая структура)
- `credentials()` - Учетные данные
- `accessUsers()` - Пользователи с доступом

### Bank
Модель банка компании. Каждый банк может содержать несколько реквизитов.

Методы:
- `details()` - Получить реквизиты банка

### BankDetail
Модель реквизита банка. Хранит информацию о конкретных реквизитах (счета, IBAN, SWIFT и т.д.).

Константы типов:
- `TYPE_ACCOUNT_NUMBER` - Номер счета
- `TYPE_IBAN` - IBAN
- `TYPE_SWIFT` - SWIFT код
- `TYPE_RECIPIENT_NAME` - Имя получателя
- `TYPE_RECIPIENT_ADDRESS` - Адрес получателя
- `TYPE_BANK_ADDRESS` - Адрес банка
- `TYPE_REFERENCE_CODE` - Код ссылки
- `TYPE_CUSTOM` - Пользовательский тип

Методы:
- `getTypes()` - Получить все доступные типы реквизитов

### CompanyBankAccount
Модель банковского счета (старая структура для обратной совместимости).

### CompanyCredential
Модель учетных данных (логины/пароли).

### CompanyAccess
Модель доступа пользователя к компании.

## Роли и права доступа

### Супер-администратор (`super_admin`)
- Видит все компании
- Редактирует любые данные
- Видит логины/пароли всех компаний
- Управляет доступами

### Модератор компании
- Видит и редактирует свои компании
- Видит логины/пароли своих компаний
- Может добавлять банковские счета
- НЕ может управлять доступами (только админ)

### Пользователь с правом `edit`
- Может редактировать данные компании
- Видит логины/пароли
- Может добавлять банковские счета

### Пользователь с правом `view`
- Может только просматривать реквизиты
- НЕ видит логины/пароли
- НЕ может редактировать

## Контроллеры

### CompanyController
Основной контроллер управления компаниями:
- `index()` - Список компаний
- `create()` - Форма создания
- `store()` - Сохранение новой компании
- `show()` - Просмотр компании
- `edit()` - Форма редактирования
- `update()` - Обновление данных
- `destroy()` - Удаление компании

### CompanyBankAccountController (СТАРАЯ СТРУКТУРА)
Управление банковскими счетами (для обратной совместимости):
- `store()` - Добавить счет
- `update()` - Обновить счет
- `destroy()` - Удалить счет

### CompanyBankController (НОВАЯ СТРУКТУРА)
Управление банками компании:
- `store()` - Добавить новый банк
- `update()` - Обновить данные банка
- `destroy()` - Удалить банк (со всеми реквизитами)

### CompanyBankDetailController (НОВАЯ СТРУКТУРА)
Управление реквизитами банков:
- `store()` - Добавить реквизит для банка
- `update()` - Обновить реквизит
- `destroy()` - Удалить реквизит

### CompanyCredentialController
Управление учетными данными:
- `store()` - Сохранить/обновить учетные данные

### CompanyAccessController
Управление доступами:
- `store()` - Добавить доступ пользователю
- `destroy()` - Удалить доступ

## Маршруты

### Управление компаниями
```php
Route::resource('companies', CompanyController::class);
```

### Старая структура банков (для совместимости)
```php
Route::post('companies/{company}/bank-accounts', [CompanyBankAccountController::class, 'store']);
Route::put('companies/{company}/bank-accounts/{bankAccount}', [CompanyBankAccountController::class, 'update']);
Route::delete('companies/{company}/bank-accounts/{bankAccount}', [CompanyBankAccountController::class, 'destroy']);
```

### Новая структура банков и реквизитов
```php
// Управление банками
Route::post('companies/{company}/banks', [CompanyBankController::class, 'store']);
Route::put('companies/{company}/banks/{bank}', [CompanyBankController::class, 'update']);
Route::delete('companies/{company}/banks/{bank}', [CompanyBankController::class, 'destroy']);

// Управление реквизитами банков
Route::post('companies/{company}/banks/{bank}/details', [CompanyBankDetailController::class, 'store']);
Route::put('companies/{company}/bank-details/{detail}', [CompanyBankDetailController::class, 'update']);
Route::delete('companies/{company}/bank-details/{detail}', [CompanyBankDetailController::class, 'destroy']);
```

### Другие данные
```php
Route::post('companies/{company}/credentials', [CompanyCredentialController::class, 'store']);
Route::put('companies/{company}/license', [CompanyController::class, 'updateLicense']);
Route::post('companies/{company}/access', [CompanyAccessController::class, 'store']);
Route::delete('companies/{company}/access/{access}', [CompanyAccessController::class, 'destroy']);
```

## Представления

- `admin/companies/index.blade.php` - Список компаний
- `admin/companies/create.blade.php` - Форма создания
- `admin/companies/show.blade.php` - Просмотр с вкладками
- `admin/companies/edit.blade.php` - Редактирование

## Особенности

1. **Загрузка лицензий**: Файлы хранятся в `storage/app/public/licenses/`

2. **Вкладки на странице компании**:
   - **Логины и пароли** (конфиденциально, только для модератора и супер-админа)
   - **Реквизиты** (новая структура: Банк → Реквизиты)
   - **Управление доступом** (назначение прав пользователям)

3. **Новая архитектура банков и реквизитов**:
   - Один банк может содержать несколько реквизитов
   - Реквизиты могут быть разных типов: счета, IBAN, SWIFT, данные получателя и т.д.
   - Каждый реквизит может быть отмечен как "основной"
   - Полная иерархия: Компания → Банки → Реквизиты

4. **Интерфейс**:
   - Разворачиваемые/сворачиваемые блоки банков
   - Таблица реквизитов для каждого банка
   - Быстрые действия (добавить, редактировать, удалить)
   - Визуальная иерархия с бейджами и иконками

5. **Валидация**:
   - Все формы валидируются на стороне сервера
   - Валидация типов реквизитов
   - Защита от несанкционированного доступа

6. **Модальные окна**: 
   - Добавление/редактирование банков
   - Добавление/редактирование реквизитов банков
   - Управление доступом пользователей

7. **Каскадное удаление**: При удалении банка автоматически удаляются все его реквизиты
