<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Support\Collection;

class PermissionService
{
    /**
     * Получить все группы разрешений с их разрешениями
     */
    public function getAllGroupedPermissions(): Collection
    {
        return PermissionGroup::with(['permissions' => function ($query) {
            $query->ordered();
        }])
            ->ordered()
            ->get();
    }

    /**
     * Получить все разрешения
     */
    public function getAllPermissions(): Collection
    {
        return Permission::with('group')->ordered()->get();
    }

    /**
     * Получить разрешения по slug'ам
     */
    public function getPermissionsBySlugs(array $slugs): Collection
    {
        return Permission::whereIn('slug', $slugs)->get();
    }

    /**
     * Получить разрешение по slug
     */
    public function getPermissionBySlug(string $slug): ?Permission
    {
        return Permission::where('slug', $slug)->first();
    }

    /**
     * Создать группу разрешений
     */
    public function createPermissionGroup(array $data): PermissionGroup
    {
        return PermissionGroup::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    /**
     * Создать разрешение
     */
    public function createPermission(array $data): Permission
    {
        return Permission::create([
            'permission_group_id' => $data['permission_group_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? Permission::TYPE_VIEW,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    /**
     * Получить структурированный массив разрешений для форм
     * Возвращает массив: [group_id => ['group' => PermissionGroup, 'permissions' => [Permission]]]
     */
    public function getPermissionsForForm(): array
    {
        $groups = $this->getAllGroupedPermissions();
        $result = [];

        foreach ($groups as $group) {
            $result[$group->id] = [
                'group' => $group,
                'permissions' => $group->permissions,
            ];
        }

        return $result;
    }
}

