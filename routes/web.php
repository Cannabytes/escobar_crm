<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/users/create');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/users/create')->name('dashboard');

    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});
