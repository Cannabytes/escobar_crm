<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly UserService $userService,
    )
    {
        $this->middleware('permission:users.view,users.manage')->only('index');
        $this->middleware('permission:users.create,users.manage')->only(['create', 'store']);
        $this->middleware('permission:users.edit,users.manage')->only(['edit', 'update', 'updatePhone']);
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $roleId = $request->integer('role_id');

        $users = User::query()
            ->with([
                'moderatedCompanies',
                'accessibleCompanies',
                'roleModel',
            ])
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($roleId !== null && $roleId > 0, function ($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $availableRoles = $this->roleService->getActiveRoles()
            ->mapWithKeys(fn (Role $role) => [$role->id => $role->name]);

        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role_id' => $roleId,
            ],
            'availableRoles' => $availableRoles,
        ]);
    }

    public function create(): View
    {
        $roles = $this->roleService->getActiveRoles();

        return view('admin.users.create', [
            'roles' => $roles,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $roleId = $request->integer('role_id');
            $role = $this->roleService->getRoleById($roleId);

            if (! $role || ! $role->is_active) {
                abort(400, __('Невозможно назначить выбранную роль.'));
            }

            $legacyRole = $role->slug === Role::ROLE_SUPER_ADMIN
                ? User::ROLE_SUPER_ADMIN
                : User::ROLE_VIEWER;

            User::create([
                'name' => $request->string('name')->value(),
                'email' => $request->string('email')->value(),
                'password' => Hash::make($request->string('password')->value()),
                'role' => $legacyRole,
                'role_id' => $role->id,
                'phone' => $request->string('phone')->trim()->value() ?: null,
                'operator' => $request->string('operator')->trim()->value() ?: null,
            ]);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('Пользователь успешно создан и добавлен в систему.'));
    }

    public function edit(User $user): View
    {
        $roles = $this->roleService->getActiveRoles();

        return view('admin.users.edit', [
            'user' => $user->load('roleModel'),
            'roles' => $roles,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->toDto();
        $role = $this->roleService->getRoleById($data->roleId);

        if (! $role || ! $role->is_active) {
            abort(400, __('Невозможно назначить выбранную роль.'));
        }

        $this->userService->updateUser($user, $role, $data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('Данные пользователя успешно обновлены.'));
    }

    public function updatePhone(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:64',
            'operator' => 'nullable|string|max:50',
            'phone_comment' => 'nullable|string',
        ]);

        $updateData = [];

        foreach (['phone', 'operator', 'phone_comment', 'name'] as $attribute) {
            if (array_key_exists($attribute, $validated)) {
                $updateData[$attribute] = $validated[$attribute];
            }
        }

        $user->update($updateData);

        return back()->with('status', __('Телефонные данные пользователя успешно обновлены.'));
    }
}

