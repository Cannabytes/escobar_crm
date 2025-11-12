<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleService
{
    public function __construct(
        private readonly PermissionService $permissionService
    ) {}

    /**
     * Получить все роли
     */
    public function getAllRoles(): Collection
    {
        return Role::with(['permissions'])->latest()->get();
    }

    /**
     * Получить активные роли
     */
    public function getActiveRoles(): Collection
    {
        return Role::active()->latest()->get();
    }

    /**
     * Получить роль по ID
     */
    public function getRoleById(int $id): ?Role
    {
        return Role::with(['permissions.group'])->find($id);
    }

    /**
     * Получить роль по slug
     */
    public function getRoleBySlug(string $slug): ?Role
    {
        return Role::with(['permissions'])->where('slug', $slug)->first();
    }

    /**
     * Создать роль
     */
    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'is_system' => $data['is_system'] ?? false,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Привязываем разрешения
            if (! empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role;
        });
    }

    /**
     * Обновить роль
     */
    public function updateRole(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            // Обновляем основные данные
            $role->update([
                'name' => $data['name'] ?? $role->name,
                'slug' => $data['slug'] ?? $role->slug,
                'description' => $data['description'] ?? $role->description,
                'is_active' => $data['is_active'] ?? $role->is_active,
            ]);

            // Обновляем разрешения
            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role->fresh(['permissions']);
        });
    }

    /**
     * Удалить роль
     */
    public function deleteRole(Role $role): bool
    {
        // Нельзя удалить системную роль
        if ($role->isSystem()) {
            throw new \RuntimeException('Невозможно удалить системную роль.');
        }

        // Проверяем, есть ли пользователи с этой ролью
        if ($role->users()->count() > 0) {
            throw new \RuntimeException('Невозможно удалить роль, так как она назначена пользователям.');
        }

        return $role->delete();
    }

    /**
     * Переключить активность роли
     */
    public function toggleActive(Role $role): Role
    {
        $role->update(['is_active' => ! $role->is_active]);
        return $role->fresh();
    }

    /**
     * Получить количество пользователей с ролью
     */
    public function getUsersCount(Role $role): int
    {
        return $role->users()->count();
    }

    /**
     * Клонировать роль
     */
    public function cloneRole(Role $originalRole, string $newName): Role
    {
        return DB::transaction(function () use ($originalRole, $newName) {
            $newRole = Role::create([
                'name' => $newName,
                'slug' => Str::slug($newName),
                'description' => $originalRole->description,
                'is_system' => false,
                'is_active' => true,
            ]);

            // Копируем разрешения
            $permissionIds = $originalRole->permissions->pluck('id')->toArray();
            $newRole->syncPermissions($permissionIds);

            return $newRole;
        });
    }
}

