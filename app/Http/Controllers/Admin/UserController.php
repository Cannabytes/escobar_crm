<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $role = $request->string('role')->trim()->value();

        $users = User::query()
            ->with([
                'moderatedCompanies',
                'accessibleCompanies',
            ])
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($role !== null && $role !== '', function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roleLabels = [
            User::ROLE_SUPER_ADMIN => __('Супер админ'),
            User::ROLE_MODERATOR => __('Модератор'),
            User::ROLE_VIEWER => __('Пользователь'),
        ];

        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role' => $role,
            ],
            'roleLabels' => $roleLabels,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            User::create([
                'name' => $request->string('name')->value(),
                'email' => $request->string('email')->value(),
                'password' => Hash::make($request->string('password')->value()),
                'role' => $request->string('role')->value(),
            ]);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('Пользователь успешно создан и добавлен в систему.'));
    }
}

