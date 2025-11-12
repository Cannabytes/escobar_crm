<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\SystemState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (SystemState::usersExist()) {
            return redirect()->route('login');
        }

        return view('install.super-admin');
    }

    public function store(Request $request): RedirectResponse
    {
        if (SystemState::usersExist()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        SystemState::markUsersExist();

        Auth::login($user);

        return redirect()->intended(route('admin.dashboard'));
    }
}


