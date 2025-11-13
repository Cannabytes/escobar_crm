<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'slug' => Role::ROLE_SUPER_ADMIN,
                'name' => 'Супер Администратор',
                'description' => 'Полный доступ ко всем функциям системы',
                'is_system' => true,
                'is_active' => true,
                'permissions' => [],
            ],
            [
                'slug' => Role::ROLE_ADMIN,
                'name' => 'Администратор',
                'description' => 'Управление компаниями, пользователями и основными функциями',
                'is_system' => true,
                'is_active' => true,
                'permissions' => Permission::query()
                    ->whereNotIn('slug', [
                        'roles.view',
                        'roles.show',
                        'roles.create',
                        'roles.edit',
                        'roles.delete',
                        'roles.manage',
                        'logs.view',
                        'logs.show',
                    ])
                    ->pluck('slug')
                    ->toArray(),
            ],
            [
                'slug' => 'company_moderator',
                'name' => 'Модератор компаний',
                'description' => 'Управление компаниями и их данными',
                'is_system' => false,
                'is_active' => true,
                'permissions' => [
                    'companies.view',
                    'companies.show',
                    'companies.edit',
                    'company-licenses.view',
                    'company-licenses.edit',
                    'company-bank-accounts.view',
                    'company-bank-accounts.create',
                    'company-bank-accounts.edit',
                    'company-bank-accounts.delete',
                    'company-credentials.view',
                    'company-credentials.create',
                    'company-credentials.edit',
                    'company-access.view',
                    'profile.view',
                    'profile.edit',
                ],
            ],
            [
                'slug' => 'viewer',
                'name' => 'Наблюдатель',
                'description' => 'Только просмотр информации о компаниях',
                'is_system' => false,
                'is_active' => true,
                'permissions' => [
                    'companies.view',
                    'companies.show',
                    'company-bank-accounts.view',
                    'profile.view',
                    'profile.edit',
                ],
            ],
            [
                'slug' => 'user_manager',
                'name' => 'Менеджер пользователей',
                'description' => 'Управление пользователями системы',
                'is_system' => false,
                'is_active' => true,
                'permissions' => [
                    'users.view',
                    'users.show',
                    'users.create',
                    'users.edit',
                    'user-phones.view',
                    'user-phones.create',
                    'user-phones.edit',
                    'user-phones.delete',
                    'companies.view',
                    'companies.show',
                    'company-access.view',
                    'company-access.create',
                    'company-access.delete',
                    'profile.view',
                    'profile.edit',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'is_system' => $roleData['is_system'],
                    'is_active' => $roleData['is_active'],
                ]
            );

            if (! empty($roleData['permissions'])) {
                $permissionIds = Permission::whereIn('slug', $roleData['permissions'])
                    ->pluck('id')
                    ->toArray();

                $role->syncPermissions($permissionIds);
            } elseif ($role->slug === Role::ROLE_SUPER_ADMIN) {
                // Супер администратор обладает всеми правами — синхронизируем полностью для актуальности
                $allPermissionIds = Permission::pluck('id')->toArray();
                $role->syncPermissions($allPermissionIds);
            }
        }
    }
}

