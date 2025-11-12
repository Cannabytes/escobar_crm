<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // Старые роли (для обратной совместимости)
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_MODERATOR = 'moderator';
    public const ROLE_VIEWER = 'viewer';

    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'phone',
        'operator',
        'telegram',
        'whatsapp',
        'last_activity_at',
        'avatar',
    ];

    protected $attributes = [
        'role' => self::ROLE_VIEWER,
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_activity_at' => 'datetime',
        ];
    }

    // Компании, которые модерирует пользователь
    public function moderatedCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'moderator_id');
    }

    // Компании, к которым у пользователя есть доступ
    public function accessibleCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user_access')
            ->withPivot('access_type')
            ->withTimestamps();
    }

    // Все компании, доступные пользователю (модерируемые + с доступом)
    public function allAccessibleCompanies()
    {
        if ($this->role === self::ROLE_SUPER_ADMIN) {
            return Company::query();
        }

        return Company::query()->where(function ($query) {
            $query->where('moderator_id', $this->id)
                ->orWhereHas('accessUsers', function ($q) {
                    $q->where('user_id', $this->id);
                });
        });
    }

    /**
     * Роль пользователя (RBAC)
     */
    public function roleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function isSuperAdmin(): bool
    {
        // Проверяем и старую систему ролей и новую RBAC
        if ($this->role === self::ROLE_SUPER_ADMIN) {
            return true;
        }

        if ($this->roleModel && $this->roleModel->isSuperAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Проверить, имеет ли пользователь разрешение
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Супер админ имеет все права
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Проверка через RBAC роль
        if ($this->roleModel) {
            return $this->roleModel->hasPermission($permissionSlug);
        }

        return false;
    }

    /**
     * Проверить, имеет ли пользователь любое из разрешений
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->roleModel) {
            return $this->roleModel->hasAnyPermission($permissionSlugs);
        }

        return false;
    }

    /**
     * Проверить, имеет ли пользователь все разрешения
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->roleModel) {
            return $this->roleModel->hasAllPermissions($permissionSlugs);
        }

        return false;
    }

    /**
     * Получить все разрешения пользователя
     */
    public function getAllPermissions()
    {
        if ($this->isSuperAdmin()) {
            return Permission::all();
        }

        if ($this->roleModel) {
            return $this->roleModel->permissions;
        }

        return collect();
    }

    // Логи активности пользователя
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Настройки пользователя
    public function settings(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserSettings::class);
    }

    /**
     * Получить или создать настройки пользователя
     */
    public function getOrCreateSettings(): UserSettings
    {
        $settings = $this->settings;

        if (! $settings) {
            $settings = $this->settings()->create([]);
            $this->setRelation('settings', $settings);
        }

        return $settings;
    }

    /**
     * Проверить, находится ли пользователь онлайн
     * Считаем пользователя онлайн, если активность была в последние 5 минут
     */
    public function isOnline(): bool
    {
        if (!$this->last_activity_at) {
            return false;
        }

        return $this->last_activity_at->diffInMinutes(now()) < 5;
    }

    /**
     * Получить читаемое время последней активности
     */
    public function getLastActivityAttribute(): string
    {
        if (!$this->last_activity_at) {
            return __('users.never_active');
        }

        if ($this->isOnline()) {
            return __('users.online');
        }

        return $this->getHumanReadableTime($this->last_activity_at);
    }

    /**
     * Получить человекочитаемое время
     */
    protected function getHumanReadableTime($datetime): string
    {
        $diff = $datetime->diffInMinutes(now());

        if ($diff < 60) {
            return __('users.minutes_ago', ['count' => $diff]);
        }

        $hours = floor($diff / 60);
        if ($hours < 24) {
            return __('users.hours_ago', ['count' => $hours]);
        }

        $days = floor($hours / 24);
        return __('users.days_ago', ['count' => $days]);
    }

    /**
     * Получить URL аватара или дефолтный
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return url('public/storage/' . $this->avatar);
        }

        // Генерируем аватар с инициалами через UI Avatars
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF&size=128";
    }
}
