<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Показать форму настроек шаблона
     */
    public function edit()
    {
        $user = Auth::user();
        $settings = $user->getOrCreateSettings();

        return view('admin.settings.edit', compact('settings'));
    }

    /**
     * Обновить настройки шаблона
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $settings = $user->getOrCreateSettings();

        $validated = $request->validate([
            'theme' => ['required', 'string', 'in:light,dark,system'],
            'style' => ['required', 'string', 'in:light,dark,bordered'],
            'layout_type' => ['required', 'string', 'in:vertical,horizontal'],
            'navbar_type' => ['required', 'string', 'in:fixed,static,hidden'],
            'footer_type' => ['required', 'string', 'in:fixed,static,hidden'],
            'layout_navbar_fixed' => ['boolean'],
            'show_dropdown_on_hover' => ['boolean'],
            'language' => ['required', 'string', 'in:en,ru'],
        ]);

        // Преобразуем чекбоксы в boolean
        $validated['layout_navbar_fixed'] = $request->has('layout_navbar_fixed');
        $validated['show_dropdown_on_hover'] = $request->has('show_dropdown_on_hover');

        $oldSettings = $settings->toArray();
        $settings->update($validated);

        // Логируем изменение настроек
        ActivityLog::log(
            action: ActivityLog::ACTION_UPDATE,
            model: $settings,
            description: 'Settings updated',
            oldValues: $oldSettings,
            newValues: $validated
        );

        return redirect()->route('admin.settings.edit')
            ->with('success', __('settings.settings_saved'));
    }

    /**
     * Сбросить настройки к значениям по умолчанию
     */
    public function reset()
    {
        $user = Auth::user();
        $settings = $user->getOrCreateSettings();

        $settings->reset();

        // Логируем сброс настроек
        ActivityLog::log(
            action: 'settings_reset',
            model: $settings,
            description: 'Settings reset to default'
        );

        return redirect()->route('admin.settings.edit')
            ->with('success', __('settings.settings_reset'));
    }
}
