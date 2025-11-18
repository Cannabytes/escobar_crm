<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Определяем группы разрешений и их разрешения
        $permissionsStructure = [
            [
                'group' => [
                    'name' => 'Компании',
                    'slug' => 'companies',
                    'description' => 'Управление компаниями',
                    'sort_order' => 10,
                ],
                'permissions' => [
                    ['name' => 'Просмотр списка компаний', 'slug' => 'companies.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Просмотр карточки компании', 'slug' => 'companies.show', 'type' => 'view', 'sort_order' => 2],
                    ['name' => 'Создание компании', 'slug' => 'companies.create', 'type' => 'create', 'sort_order' => 3],
                    ['name' => 'Редактирование компании', 'slug' => 'companies.edit', 'type' => 'edit', 'sort_order' => 4],
                    ['name' => 'Удаление компании', 'slug' => 'companies.delete', 'type' => 'delete', 'sort_order' => 5],
                    ['name' => 'Полное управление компаниями', 'slug' => 'companies.manage', 'type' => 'manage', 'sort_order' => 6],
                ],
            ],
            [
                'group' => [
                    'name' => 'Лицензии компаний',
                    'slug' => 'company-licenses',
                    'description' => 'Управление лицензиями компаний',
                    'sort_order' => 20,
                ],
                'permissions' => [
                    ['name' => 'Просмотр лицензий', 'slug' => 'company-licenses.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Редактирование лицензий', 'slug' => 'company-licenses.edit', 'type' => 'edit', 'sort_order' => 2],
                ],
            ],
            [
                'group' => [
                    'name' => 'Банковские счета компаний',
                    'slug' => 'company-bank-accounts',
                    'description' => 'Управление банковскими счетами компаний',
                    'sort_order' => 30,
                ],
                'permissions' => [
                    ['name' => 'Просмотр банковских счетов', 'slug' => 'company-bank-accounts.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Создание банковских счетов', 'slug' => 'company-bank-accounts.create', 'type' => 'create', 'sort_order' => 2],
                    ['name' => 'Редактирование банковских счетов', 'slug' => 'company-bank-accounts.edit', 'type' => 'edit', 'sort_order' => 3],
                    ['name' => 'Удаление банковских счетов', 'slug' => 'company-bank-accounts.delete', 'type' => 'delete', 'sort_order' => 4],
                ],
            ],
            [
                'group' => [
                    'name' => 'Учётные данные компаний',
                    'slug' => 'company-credentials',
                    'description' => 'Управление учётными данными (логины, пароли)',
                    'sort_order' => 40,
                ],
                'permissions' => [
                    ['name' => 'Просмотр учётных данных', 'slug' => 'company-credentials.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Создание учётных данных', 'slug' => 'company-credentials.create', 'type' => 'create', 'sort_order' => 2],
                    ['name' => 'Редактирование учётных данных', 'slug' => 'company-credentials.edit', 'type' => 'edit', 'sort_order' => 3],
                    ['name' => 'Удаление учётных данных', 'slug' => 'company-credentials.delete', 'type' => 'delete', 'sort_order' => 4],
                ],
            ],
            [
                'group' => [
                    'name' => 'Доступ пользователей к компаниям',
                    'slug' => 'company-access',
                    'description' => 'Управление доступом пользователей к компаниям',
                    'sort_order' => 50,
                ],
                'permissions' => [
                    ['name' => 'Просмотр доступа', 'slug' => 'company-access.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Предоставление доступа', 'slug' => 'company-access.create', 'type' => 'create', 'sort_order' => 2],
                    ['name' => 'Удаление доступа', 'slug' => 'company-access.delete', 'type' => 'delete', 'sort_order' => 3],
                ],
            ],
            [
                'group' => [
                    'name' => 'Пользователи',
                    'slug' => 'users',
                    'description' => 'Управление пользователями системы',
                    'sort_order' => 60,
                ],
                'permissions' => [
                    ['name' => 'Просмотр списка пользователей', 'slug' => 'users.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Просмотр профиля пользователя', 'slug' => 'users.show', 'type' => 'view', 'sort_order' => 2],
                    ['name' => 'Создание пользователя', 'slug' => 'users.create', 'type' => 'create', 'sort_order' => 3],
                    ['name' => 'Редактирование пользователя', 'slug' => 'users.edit', 'type' => 'edit', 'sort_order' => 4],
                    ['name' => 'Удаление пользователя', 'slug' => 'users.delete', 'type' => 'delete', 'sort_order' => 5],
                    ['name' => 'Полное управление пользователями', 'slug' => 'users.manage', 'type' => 'manage', 'sort_order' => 6],
                ],
            ],
            [
                'group' => [
                    'name' => 'Телефонный справочник',
                    'slug' => 'user-phones',
                    'description' => 'Управление телефонными контактами сотрудников',
                    'sort_order' => 65,
                ],
                'permissions' => [
                    ['name' => 'Просмотр телефонного справочника', 'slug' => 'user-phones.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Создание записи телефонного справочника', 'slug' => 'user-phones.create', 'type' => 'create', 'sort_order' => 2],
                    ['name' => 'Редактирование записи телефонного справочника', 'slug' => 'user-phones.edit', 'type' => 'edit', 'sort_order' => 3],
                    ['name' => 'Удаление записи телефонного справочника', 'slug' => 'user-phones.delete', 'type' => 'delete', 'sort_order' => 4],
                    ['name' => 'Полное управление телефонным справочником', 'slug' => 'user-phones.manage', 'type' => 'manage', 'sort_order' => 5],
                ],
            ],
            [
                'group' => [
                    'name' => 'Ledger',
                    'slug' => 'ledger',
                    'description' => 'Управление кошельками и сетями',
                    'sort_order' => 66,
                ],
                'permissions' => [
                    ['name' => 'Просмотр Ledger', 'slug' => 'ledger.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Управление Ledger', 'slug' => 'ledger.manage', 'type' => 'manage', 'sort_order' => 2],
                ],
            ],
            [
                'group' => [
                    'name' => 'Роли и разрешения',
                    'slug' => 'roles',
                    'description' => 'Управление ролями и разрешениями',
                    'sort_order' => 70,
                ],
                'permissions' => [
                    ['name' => 'Просмотр списка ролей', 'slug' => 'roles.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Просмотр роли', 'slug' => 'roles.show', 'type' => 'view', 'sort_order' => 2],
                    ['name' => 'Создание роли', 'slug' => 'roles.create', 'type' => 'create', 'sort_order' => 3],
                    ['name' => 'Редактирование роли', 'slug' => 'roles.edit', 'type' => 'edit', 'sort_order' => 4],
                    ['name' => 'Удаление роли', 'slug' => 'roles.delete', 'type' => 'delete', 'sort_order' => 5],
                    ['name' => 'Полное управление ролями', 'slug' => 'roles.manage', 'type' => 'manage', 'sort_order' => 6],
                ],
            ],
            [
                'group' => [
                    'name' => 'Логи активности',
                    'slug' => 'logs',
                    'description' => 'Просмотр логов и аудита системы',
                    'sort_order' => 80,
                ],
                'permissions' => [
                    ['name' => 'Просмотр логов активности', 'slug' => 'logs.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Просмотр деталей лога', 'slug' => 'logs.show', 'type' => 'view', 'sort_order' => 2],
                ],
            ],
            [
                'group' => [
                    'name' => 'Настройки системы',
                    'slug' => 'settings',
                    'description' => 'Управление настройками системы',
                    'sort_order' => 90,
                ],
                'permissions' => [
                    ['name' => 'Просмотр настроек', 'slug' => 'settings.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Редактирование настроек', 'slug' => 'settings.edit', 'type' => 'edit', 'sort_order' => 2],
                ],
            ],
            [
                'group' => [
                    'name' => 'Профиль',
                    'slug' => 'profile',
                    'description' => 'Управление собственным профилем',
                    'sort_order' => 100,
                ],
                'permissions' => [
                    ['name' => 'Просмотр собственного профиля', 'slug' => 'profile.view', 'type' => 'view', 'sort_order' => 1],
                    ['name' => 'Редактирование собственного профиля', 'slug' => 'profile.edit', 'type' => 'edit', 'sort_order' => 2],
                ],
            ],
        ];

        // Создаём группы и разрешения
        foreach ($permissionsStructure as $item) {
            $groupData = $item['group'];

            $group = PermissionGroup::updateOrCreate(
                ['slug' => $groupData['slug']],
                [
                    'name' => $groupData['name'],
                    'description' => $groupData['description'] ?? null,
                    'sort_order' => $groupData['sort_order'] ?? 0,
                ]
            );

            foreach ($item['permissions'] as $permission) {
                Permission::updateOrCreate(
                    ['slug' => $permission['slug']],
                    [
                        'permission_group_id' => $group->id,
                        'name' => $permission['name'],
                        'description' => $permission['description'] ?? null,
                        'type' => $permission['type'],
                        'sort_order' => $permission['sort_order'] ?? 0,
                    ]
                );
            }
        }
    }
}

