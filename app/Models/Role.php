<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    // Системные роли (нельзя удалить)
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Пользователи с этой ролью
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Разрешения роли
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withTimestamps();
    }

    /**
     * Проверить, имеет ли роль разрешение по slug
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Супер админ имеет все права
        if ($this->slug === self::ROLE_SUPER_ADMIN) {
            return true;
        }

        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Проверить, имеет ли роль любое из указанных разрешений
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        if ($this->slug === self::ROLE_SUPER_ADMIN) {
            return true;
        }

        return $this->permissions()->whereIn('slug', $permissionSlugs)->exists();
    }

    /**
     * Проверить, имеет ли роль все указанные разрешения
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        if ($this->slug === self::ROLE_SUPER_ADMIN) {
            return true;
        }

        $count = $this->permissions()->whereIn('slug', $permissionSlugs)->count();
        return $count === count($permissionSlugs);
    }

    /**
     * Синхронизировать разрешения роли
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }

    /**
     * Scope для активных ролей
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для не-системных ролей
     */
    public function scopeNotSystem($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Проверить, является ли роль системной
     */
    public function isSystem(): bool
    {
        return $this->is_system;
    }

    /**
     * Проверить, является ли роль супер админа
     */
    public function isSuperAdmin(): bool
    {
        return $this->slug === self::ROLE_SUPER_ADMIN;
    }
}

