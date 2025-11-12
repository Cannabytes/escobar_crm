<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    public const TYPE_VIEW = 'view';
    public const TYPE_CREATE = 'create';
    public const TYPE_EDIT = 'edit';
    public const TYPE_DELETE = 'delete';
    public const TYPE_MANAGE = 'manage';

    protected $fillable = [
        'permission_group_id',
        'name',
        'slug',
        'description',
        'type',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'permission_group_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Группа разрешений
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class, 'permission_group_id');
    }

    /**
     * Роли, имеющие это разрешение
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Scope для фильтрации по типу
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope для сортировки по порядку
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Получить все типы разрешений
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_VIEW => 'Просмотр',
            self::TYPE_CREATE => 'Создание',
            self::TYPE_EDIT => 'Редактирование',
            self::TYPE_DELETE => 'Удаление',
            self::TYPE_MANAGE => 'Полное управление',
        ];
    }

    /**
     * Получить локализованное название типа
     */
    public function getTypeLabel(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }
}

