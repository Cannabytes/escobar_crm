<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Супер Администратор',
            'email' => 'admin@escobar.local',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $this->command->info('Супер-администратор создан!');
        $this->command->info('Email: admin@escobar.local');
        $this->command->info('Пароль: password');
    }
}
