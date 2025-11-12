<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use App\Services\PermissionService;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly PermissionService $permissionService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $status = $request->string('status')->trim()->value();

        $roles = Role::query()
            ->withCount('users')
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($status === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->when($status === 'system', function ($query) {
                $query->where('is_system', true);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.roles.index', [
            'roles' => $roles,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $permissionGroups = $this->permissionService->getPermissionsForForm();

        return view('admin.roles.create', [
            'permissionGroups' => $permissionGroups,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        try {
            $role = $this->roleService->createRole([
                'name' => $request->string('name')->value(),
                'slug' => $request->string('slug')->value(),
                'description' => $request->string('description')->value() ?: null,
                'is_active' => $request->boolean('is_active', true),
                'permissions' => $request->input('permissions', []),
            ]);

            return redirect()
                ->route('admin.roles.show', $role)
                ->with('status', 'Роль успешно создана.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ошибка при создании роли: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): View
    {
        $role->load(['permissions.group', 'users']);
        $usersCount = $role->users()->count();

        // Группируем разрешения по группам
        $permissionsByGroup = $role->permissions->groupBy('permission_group_id');

        return view('admin.roles.show', [
            'role' => $role,
            'usersCount' => $usersCount,
            'permissionsByGroup' => $permissionsByGroup,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $permissionGroups = $this->permissionService->getPermissionsForForm();
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', [
            'role' => $role,
            'permissionGroups' => $permissionGroups,
            'rolePermissionIds' => $rolePermissionIds,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        try {
            // Проверка, что не пытаемся отредактировать системную роль (кроме разрешений)
            if ($role->isSystem()) {
                // Для системных ролей обновляем только разрешения
                $this->roleService->updateRole($role, [
                    'permissions' => $request->input('permissions', []),
                ]);
            } else {
                $this->roleService->updateRole($role, [
                    'name' => $request->string('name')->value(),
                    'slug' => $request->string('slug')->value(),
                    'description' => $request->string('description')->value() ?: null,
                    'is_active' => $request->boolean('is_active', true),
                    'permissions' => $request->input('permissions', []),
                ]);
            }

            return redirect()
                ->route('admin.roles.show', $role)
                ->with('status', 'Роль успешно обновлена.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении роли: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        try {
            $this->roleService->deleteRole($role);

            return redirect()
                ->route('admin.roles.index')
                ->with('status', 'Роль успешно удалена.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ошибка при удалении роли: ' . $e->getMessage());
        }
    }

    /**
     * Toggle role active status
     */
    public function toggleActive(Role $role): RedirectResponse
    {
        try {
            $this->roleService->toggleActive($role);

            $status = $role->is_active ? 'активирована' : 'деактивирована';

            return redirect()
                ->back()
                ->with('status', "Роль успешно {$status}.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ошибка при изменении статуса роли: ' . $e->getMessage());
        }
    }

    /**
     * Clone role
     */
    public function clone(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        try {
            $newRole = $this->roleService->cloneRole($role, $request->string('name')->value());

            return redirect()
                ->route('admin.roles.edit', $newRole)
                ->with('status', 'Роль успешно клонирована.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ошибка при клонировании роли: ' . $e->getMessage());
        }
    }
}

