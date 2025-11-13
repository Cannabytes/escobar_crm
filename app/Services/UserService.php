<?php

namespace App\Services;

use App\DTO\Admin\UpdateUserData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function updateUser(User $user, Role $role, UpdateUserData $data): void
    {
        DB::transaction(function () use ($user, $role, $data) {
            $legacyRole = $role->slug === Role::ROLE_SUPER_ADMIN
                ? User::ROLE_SUPER_ADMIN
                : User::ROLE_VIEWER;

            $attributes = [
                'name' => $data->name,
                'email' => $data->email,
                'role_id' => $role->id,
                'role' => $legacyRole,
                'phone' => $data->phone,
                'operator' => $data->operator,
                'phone_comment' => $data->phoneComment,
            ];

            if ($data->password !== null && $data->password !== '') {
                $attributes['password'] = Hash::make($data->password);
            }

            $user->update($attributes);
        });
    }
}


