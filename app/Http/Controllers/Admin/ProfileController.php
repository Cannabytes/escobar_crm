<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Показать форму редактирования профиля
     */
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Обновить информацию профиля
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
        ]);

        $oldValues = $user->only(['name', 'email', 'phone', 'telegram', 'whatsapp']);
        $user->update($validated);

        // Логируем изменение профиля
        ActivityLog::log(
            action: ActivityLog::ACTION_UPDATE,
            model: $user,
            description: __('users.profile_updated'),
            oldValues: $oldValues,
            newValues: $validated
        );

        return redirect()->route('admin.profile.edit')
            ->with('success', __('users.profile_updated'));
    }

    /**
     * Обновить пароль
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Проверяем текущий пароль
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => __('users.invalid_current_password')
            ]);
        }

        // Обновляем пароль
        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        // Логируем изменение пароля
        ActivityLog::log(
            action: 'password_changed',
            model: $user,
            description: 'Password was changed',
            level: ActivityLog::LEVEL_WARNING
        );

        return redirect()->route('admin.profile.edit')
            ->with('success', __('users.password_updated'));
    }

    /**
     * Загрузить аватар
     */
    public function uploadAvatar(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'], // 2MB max
        ]);

        // Удаляем старый аватар, если он существует
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Сохраняем новый аватар
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        // Логируем загрузку аватара
        ActivityLog::log(
            action: 'avatar_uploaded',
            model: $user,
            description: 'Avatar was uploaded'
        );

        return redirect()->route('admin.profile.edit')
            ->with('success', __('users.avatar_uploaded'));
    }

    /**
     * Удалить аватар
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);

            // Логируем удаление аватара
            ActivityLog::log(
                action: 'avatar_removed',
                model: $user,
                description: 'Avatar was removed'
            );

            return redirect()->route('admin.profile.edit')
                ->with('success', __('users.avatar_removed'));
        }

        return redirect()->route('admin.profile.edit');
    }
}
