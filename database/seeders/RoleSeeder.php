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
        // Создаём роль супер-админа (имеет все права автоматически)
        $superAdmin = Role::create([
            'name' => 'Супер Администратор',
            'slug' => Role::ROLE_SUPER_ADMIN,
            'description' => 'Полный доступ ко всем функциям системы',
            'is_system' => true,
            'is_active' => true,
        ]);

        // Создаём роль администратора
        $admin = Role::create([
            'name' => 'Администратор',
            'slug' => Role::ROLE_ADMIN,
            'description' => 'Управление компаниями, пользователями и основными функциями',
            'is_system' => true,
            'is_active' => true,
        ]);

        // Назначаем права администратору (все, кроме управления ролями)
        $adminPermissions = Permission::whereNotIn('slug', [
            'roles.view',
            'roles.show',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.manage',
            'logs.view',
            'logs.show',
        ])->pluck('id')->toArray();

        $admin->syncPermissions($adminPermissions);

        // Создаём роль модератора компаний
        $moderator = Role::create([
            'name' => 'Модератор компаний',
            'slug' => 'company_moderator',
            'description' => 'Управление компаниями и их данными',
            'is_system' => false,
            'is_active' => true,
        ]);

        // Назначаем права модератору
        $moderatorPermissions = Permission::whereIn('slug', [
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
        ])->pluck('id')->toArray();

        $moderator->syncPermissions($moderatorPermissions);

        // Создаём роль наблюдателя
        $viewer = Role::create([
            'name' => 'Наблюдатель',
            'slug' => 'viewer',
            'description' => 'Только просмотр информации о компаниях',
            'is_system' => false,
            'is_active' => true,
        ]);

        // Назначаем права наблюдателю (только просмотр)
        $viewerPermissions = Permission::whereIn('slug', [
            'companies.view',
            'companies.show',
            'company-bank-accounts.view',
            'profile.view',
            'profile.edit',
        ])->pluck('id')->toArray();

        $viewer->syncPermissions($viewerPermissions);

        // Создаём роль менеджера пользователей
        $userManager = Role::create([
            'name' => 'Менеджер пользователей',
            'slug' => 'user_manager',
            'description' => 'Управление пользователями системы',
            'is_system' => false,
            'is_active' => true,
        ]);

        // Назначаем права менеджеру пользователей
        $userManagerPermissions = Permission::whereIn('slug', [
            'users.view',
            'users.show',
            'users.create',
            'users.edit',
            'companies.view',
            'companies.show',
            'company-access.view',
            'company-access.create',
            'company-access.delete',
            'profile.view',
            'profile.edit',
        ])->pluck('id')->toArray();

        $userManager->syncPermissions($userManagerPermissions);
    }
}

