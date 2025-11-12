<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    // Константы уровней логирования
    public const LEVEL_INFO = 'info';
    public const LEVEL_WARNING = 'warning';
    public const LEVEL_ERROR = 'error';
    public const LEVEL_CRITICAL = 'critical';

    // Константы действий
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_VIEW = 'view';
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_EXPORT = 'export';
    public const ACTION_IMPORT = 'import';
    public const ACTION_RESTORE = 'restore';

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'http_method',
        'level',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Пользователь, который выполнил действие
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Модель, с которой было взаимодействие (полиморфная связь)
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Создать лог активности
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?string $description = null,
        array $oldValues = [],
        array $newValues = [],
        string $level = self::LEVEL_INFO,
        array $metadata = []
    ): self {
        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
            'description' => $description,
            'old_values' => !empty($oldValues) ? $oldValues : null,
            'new_values' => !empty($newValues) ? $newValues : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'http_method' => Request::method(),
            'level' => $level,
            'metadata' => !empty($metadata) ? $metadata : null,
        ]);
    }

    /**
     * Scope для фильтрации по пользователю
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope для фильтрации по действию
     */
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope для фильтрации по модели
     */
    public function scopeForModel($query, string $modelType, ?int $modelId = null)
    {
        $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    /**
     * Scope для фильтрации по уровню
     */
    public function scopeForLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope для фильтрации по дате
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Получить читаемое имя действия
     */
    public function getActionNameAttribute(): string
    {
        return match($this->action) {
            self::ACTION_CREATE => __('logs.action_create'),
            self::ACTION_UPDATE => __('logs.action_update'),
            self::ACTION_DELETE => __('logs.action_delete'),
            self::ACTION_VIEW => __('logs.action_view'),
            self::ACTION_LOGIN => __('logs.action_login'),
            self::ACTION_LOGOUT => __('logs.action_logout'),
            self::ACTION_EXPORT => __('logs.action_export'),
            self::ACTION_IMPORT => __('logs.action_import'),
            self::ACTION_RESTORE => __('logs.action_restore'),
            default => $this->action,
        };
    }

    /**
     * Получить читаемое имя модели
     */
    public function getModelNameAttribute(): ?string
    {
        if (!$this->model_type) {
            return null;
        }

        return match($this->model_type) {
            'App\\Models\\User' => __('logs.model_user'),
            'App\\Models\\Company' => __('logs.model_company'),
            'App\\Models\\CompanyBankAccount' => __('logs.model_bank_account'),
            'App\\Models\\CompanyCredential' => __('logs.model_credential'),
            default => class_basename($this->model_type),
        };
    }

    /**
     * Получить цвет уровня для UI
     */
    public function getLevelColorAttribute(): string
    {
        return match($this->level) {
            self::LEVEL_INFO => 'info',
            self::LEVEL_WARNING => 'warning',
            self::LEVEL_ERROR => 'danger',
            self::LEVEL_CRITICAL => 'dark',
            default => 'secondary',
        };
    }
}
