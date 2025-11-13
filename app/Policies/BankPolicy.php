<?php

namespace App\Policies;

use App\Models\Bank;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user !== null;
    }

    public function view(User $user, Bank $bank): bool
    {
        return $user !== null;
    }

    public function create(User $user, Company $company): bool
    {
        return $user->can('update', $company);
    }

    public function update(User $user, Bank $bank): bool
    {
        return $bank->userCanManage($user);
    }

    public function delete(User $user, Bank $bank): bool
    {
        return $bank->userCanManage($user);
    }

    public function manageDetails(User $user, Bank $bank): bool
    {
        return $this->update($user, $bank);
    }
}



