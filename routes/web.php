<?php

use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\CompanyAccessController;
use App\Http\Controllers\Admin\CompanyBankAccountController;
use App\Http\Controllers\Admin\CompanyBankController;
use App\Http\Controllers\Admin\CompanyBankDetailController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\PhoneContactController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Install\SuperAdminController;
use App\Http\Controllers\LocaleController;
use App\Support\SystemState;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! SystemState::usersExist()) {
        return redirect()->route('install.super-admin.create');
    }

    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('login');
});

Route::get('locale/{locale}', LocaleController::class)
    ->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('install/super-admin', [SuperAdminController::class, 'create'])
        ->name('install.super-admin.create');
    Route::post('install/super-admin', [SuperAdminController::class, 'store'])
        ->name('install.super-admin.store');
});

Route::middleware(['guest', 'ensure.installed'])->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['ensure.installed', 'auth'])->group(function () {
    Route::redirect('/', '/admin/companies')->name('dashboard');

    // Компании
    Route::resource('companies', CompanyController::class);

    // Банковские счета компании (старая структура)
    Route::post('companies/{company}/bank-accounts', [CompanyBankAccountController::class, 'store'])
        ->name('companies.bank-accounts.store');
    Route::put('companies/{company}/bank-accounts/{bankAccount}', [CompanyBankAccountController::class, 'update'])
        ->name('companies.bank-accounts.update');
    Route::delete('companies/{company}/bank-accounts/{bankAccount}', [CompanyBankAccountController::class, 'destroy'])
        ->name('companies.bank-accounts.destroy');

    // Банки компании (новая структура)
    Route::post('companies/{company}/banks', [CompanyBankController::class, 'store'])
        ->name('companies.banks.store');
    Route::put('companies/{company}/banks/{bank}', [CompanyBankController::class, 'update'])
        ->name('companies.banks.update');
    Route::delete('companies/{company}/banks/{bank}', [CompanyBankController::class, 'destroy'])
        ->name('companies.banks.destroy');

    // Реквізити банків компании
    Route::post('companies/{company}/banks/{bank}/details', [CompanyBankDetailController::class, 'store'])
        ->name('companies.bank-details.store');
    Route::put('companies/{company}/bank-details/{detail}', [CompanyBankDetailController::class, 'update'])
        ->name('companies.bank-details.update');
    Route::delete('companies/{company}/bank-details/{detail}', [CompanyBankDetailController::class, 'destroy'])
        ->name('companies.bank-details.destroy');

    // Лицензионные данные компании
    Route::put('companies/{company}/license', [CompanyController::class, 'updateLicense'])
        ->name('companies.license.update');
    
    // Управление лицензиями компании (множественные изображения)
    Route::post('companies/{company}/licenses', [\App\Http\Controllers\Admin\CompanyLicenseController::class, 'store'])
        ->name('companies.licenses.store');
    Route::delete('companies/{company}/licenses/{license}', [\App\Http\Controllers\Admin\CompanyLicenseController::class, 'destroy'])
        ->name('companies.licenses.destroy');

    // Доступ пользователей к компании
    Route::post('companies/{company}/access', [CompanyAccessController::class, 'store'])
        ->name('companies.access.store');
    Route::delete('companies/{company}/access/{access}', [CompanyAccessController::class, 'destroy'])
        ->name('companies.access.destroy');
    
    // Удаление главного модератора (только для супер-админа)
    Route::delete('companies/{company}/moderator', [CompanyAccessController::class, 'removeModerator'])
        ->name('companies.moderator.remove');

    // Пользователи
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/phones', [PhoneContactController::class, 'index'])->name('users.phones.index');
    Route::post('/users/phones', [PhoneContactController::class, 'store'])->name('users.phones.store');
    Route::put('/users/phones/{phoneContact}', [PhoneContactController::class, 'update'])->name('users.phones.update');
    Route::delete('/users/phones/{phoneContact}', [PhoneContactController::class, 'destroy'])->name('users.phones.destroy');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/phone', [UserController::class, 'updatePhone'])->name('users.update-phone');

    // Логи активности (только для супер админа)
    Route::get('/logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('logs.index');
    Route::get('/logs/{log}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('logs.show');

    // Профиль пользователя
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/avatar', [\App\Http\Controllers\Admin\ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
    Route::delete('/profile/avatar', [\App\Http\Controllers\Admin\ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

    // Роли и разрешения (только для супер админа)
    Route::resource('roles', RoleController::class);
    Route::post('/roles/{role}/toggle-active', [RoleController::class, 'toggleActive'])->name('roles.toggle-active');
    Route::post('/roles/{role}/clone', [RoleController::class, 'clone'])->name('roles.clone');

    // Чат
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/rooms', [ChatController::class, 'rooms'])->name('chat.rooms.index');
    Route::post('chat/rooms', [ChatController::class, 'storeRoom'])->name('chat.rooms.store');
    Route::put('chat/rooms/{room}', [ChatController::class, 'updateRoom'])->name('chat.rooms.update');
    Route::delete('chat/rooms/{room}', [ChatController::class, 'deleteRoom'])->name('chat.rooms.delete');
    Route::delete('chat/rooms/{room}/messages', [ChatController::class, 'clearRoomMessages'])->name('chat.rooms.messages.clear');
    Route::get('chat/rooms/{room}/messages', [ChatController::class, 'messages'])->name('chat.rooms.messages.index');
    Route::post('chat/rooms/{room}/messages', [ChatController::class, 'storeMessage'])->name('chat.rooms.messages.store');
    Route::post('chat/rooms/{room}/read', [ChatController::class, 'markRoomAsRead'])->name('chat.rooms.read');
    Route::get('chat/users', [ChatController::class, 'users'])->name('chat.users.index');
    Route::get('chat/users/search', [ChatController::class, 'searchUsers'])->name('chat.users.search');
});
