<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'style',
        'layout_type',
        'navbar_type',
        'footer_type',
        'layout_navbar_fixed',
        'show_dropdown_on_hover',
        'language',
        'custom_settings',
    ];

    protected $casts = [
        'layout_navbar_fixed' => 'boolean',
        'show_dropdown_on_hover' => 'boolean',
        'custom_settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Пользователь, которому принадлежат настройки
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить значение настройки или значение по умолчанию
     */
    public function get(string $key, $default = null)
    {
        if (isset($this->$key)) {
            return $this->$key;
        }

        if (isset($this->custom_settings[$key])) {
            return $this->custom_settings[$key];
        }

        return $default;
    }

    /**
     * Установить значение настройки
     */
    public function set(string $key, $value): void
    {
        if (in_array($key, $this->fillable)) {
            $this->$key = $value;
        } else {
            $customSettings = $this->custom_settings ?? [];
            $customSettings[$key] = $value;
            $this->custom_settings = $customSettings;
        }
    }

    /**
     * Сбросить все настройки к значениям по умолчанию
     */
    public function reset(): void
    {
        $this->update([
            'theme' => 'light',
            'style' => 'light',
            'layout_type' => 'vertical',
            'navbar_type' => 'fixed',
            'footer_type' => 'fixed',
            'layout_navbar_fixed' => true,
            'show_dropdown_on_hover' => true,
            'language' => 'en',
            'custom_settings' => null,
        ]);
    }

    /**
     * Получить настройки в виде массива для применения к шаблону
     */
    public function toTemplateConfig(): array
    {
        return [
            'theme' => $this->theme,
            'style' => $this->style,
            'layoutType' => $this->layout_type,
            'navbarType' => $this->navbar_type,
            'footerType' => $this->footer_type,
            'layoutNavbarFixed' => $this->layout_navbar_fixed,
            'showDropdownOnHover' => $this->show_dropdown_on_hover,
        ];
    }
}
