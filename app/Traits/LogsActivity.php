<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Arr;

trait LogsActivity
{
    /**
     * Атрибуты, которые не нужно логировать
     */
    protected array $excludeFromLogs = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * Boot trait
     */
    protected static function bootLogsActivity(): void
    {
        // Логирование создания
        static::created(function ($model) {
            if (method_exists($model, 'shouldLogCreate') && !$model->shouldLogCreate()) {
                return;
            }

            ActivityLog::log(
                action: ActivityLog::ACTION_CREATE,
                model: $model,
                description: $model->getLogDescription(ActivityLog::ACTION_CREATE),
                newValues: $model->getLoggableAttributes()
            );
        });

        // Логирование обновления
        static::updated(function ($model) {
            if (method_exists($model, 'shouldLogUpdate') && !$model->shouldLogUpdate()) {
                return;
            }

            $changes = $model->getChanges();
            $original = $model->getOriginal();

            // Убираем не нужные атрибуты
            $excludeKeys = array_merge(
                $model->excludeFromLogs ?? [],
                ['updated_at']
            );
            
            $oldValues = Arr::only($original, array_keys($changes));
            $newValues = $changes;
            
            foreach ($excludeKeys as $key) {
                unset($oldValues[$key], $newValues[$key]);
            }

            if (empty($newValues)) {
                return;
            }

            ActivityLog::log(
                action: ActivityLog::ACTION_UPDATE,
                model: $model,
                description: $model->getLogDescription(ActivityLog::ACTION_UPDATE),
                oldValues: $oldValues,
                newValues: $newValues
            );
        });

        // Логирование удаления
        static::deleted(function ($model) {
            if (method_exists($model, 'shouldLogDelete') && !$model->shouldLogDelete()) {
                return;
            }

            ActivityLog::log(
                action: ActivityLog::ACTION_DELETE,
                model: $model,
                description: $model->getLogDescription(ActivityLog::ACTION_DELETE),
                oldValues: $model->getLoggableAttributes()
            );
        });
    }

    /**
     * Получить атрибуты для логирования
     */
    protected function getLoggableAttributes(): array
    {
        $attributes = $this->getAttributes();
        
        $excludeKeys = array_merge(
            $this->excludeFromLogs ?? [],
            ['password', 'remember_token']
        );
        
        foreach ($excludeKeys as $key) {
            unset($attributes[$key]);
        }
        
        return $attributes;
    }

    /**
     * Получить описание для лога
     */
    protected function getLogDescription(string $action): string
    {
        $modelName = class_basename($this);
        $identifier = $this->getLogIdentifier();

        return match($action) {
            ActivityLog::ACTION_CREATE => __('logs.description_created', [
                'model' => $modelName,
                'identifier' => $identifier,
            ]),
            ActivityLog::ACTION_UPDATE => __('logs.description_updated', [
                'model' => $modelName,
                'identifier' => $identifier,
            ]),
            ActivityLog::ACTION_DELETE => __('logs.description_deleted', [
                'model' => $modelName,
                'identifier' => $identifier,
            ]),
            default => __('logs.description_action', [
                'action' => $action,
                'model' => $modelName,
                'identifier' => $identifier,
            ]),
        };
    }

    /**
     * Получить идентификатор модели для лога
     */
    protected function getLogIdentifier(): string
    {
        // Попробуем получить читаемое имя
        if (isset($this->name)) {
            return $this->name;
        }
        
        if (isset($this->title)) {
            return $this->title;
        }
        
        if (isset($this->email)) {
            return $this->email;
        }
        
        return "#{$this->getKey()}";
    }

    /**
     * Логировать кастомное действие
     */
    public function logAction(
        string $action,
        ?string $description = null,
        array $metadata = [],
        string $level = ActivityLog::LEVEL_INFO
    ): ActivityLog {
        return ActivityLog::log(
            action: $action,
            model: $this,
            description: $description ?? $this->getLogDescription($action),
            level: $level,
            metadata: $metadata
        );
    }
}

