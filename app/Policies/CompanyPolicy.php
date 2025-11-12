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
        return $user !== null;
    }

    public function view(User $user, Company $company): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Company $company): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $company->canUserEdit($user);
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, Company $company): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, Company $company): bool
    {
        return $user->isSuperAdmin();
    }
}


