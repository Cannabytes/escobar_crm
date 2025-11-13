<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasAnyPermission([
            'companies.view',
            'companies.manage',
            'companies.create',
            'companies.edit',
        ]);
    }

    public function view(User $user, Company $company): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->hasAnyPermission(['companies.view', 'companies.manage'])) {
            return true;
        }

        return $company->canUserView($user);
    }

    public function create(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasAnyPermission(['companies.create', 'companies.manage']);
    }

    public function update(User $user, Company $company): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->hasAnyPermission(['companies.edit', 'companies.manage'])) {
            return true;
        }

        return $company->canUserEdit($user);
    }

    public function delete(User $user, Company $company): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasAnyPermission(['companies.delete', 'companies.manage']);
    }

    public function restore(User $user, Company $company): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasAnyPermission(['companies.delete', 'companies.manage']);
    }

    public function forceDelete(User $user, Company $company): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasAnyPermission(['companies.delete', 'companies.manage']);
    }
}


