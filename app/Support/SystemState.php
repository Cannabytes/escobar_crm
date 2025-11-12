<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SystemState
{
    private const CACHE_KEY_USERS_EXIST = 'system.users_exist';

    public static function usersExist(): bool
    {
        $cached = Cache::get(self::CACHE_KEY_USERS_EXIST);

        if ($cached === true) {
            if (self::databaseHasUsers()) {
                return true;
            }

            Cache::forget(self::CACHE_KEY_USERS_EXIST);
            $cached = null;
        }

        if ($cached === false) {
            return false;
        }

        return self::refreshUsersExist();
    }

    public static function markUsersExist(): void
    {
        Cache::forever(self::CACHE_KEY_USERS_EXIST, true);
    }

    public static function resetUsersExistCache(): void
    {
        Cache::forget(self::CACHE_KEY_USERS_EXIST);
    }

    private static function refreshUsersExist(): bool
    {
        $exists = self::databaseHasUsers();
        Cache::forever(self::CACHE_KEY_USERS_EXIST, $exists);

        return $exists;
    }

    private static function databaseHasUsers(): bool
    {
        return User::query()->exists();
    }
}


