<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use App\Support\SystemState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_user_profile_and_role(): void
    {
        $superAdminRole = $this->createRole(Role::ROLE_SUPER_ADMIN, 'Супер админ', true);
        $editorRole = $this->createRole('editor', 'Редактор', true);
        $auditorRole = $this->createRole('auditor', 'Аудитор', true);

        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'role_id' => $superAdminRole->id,
        ]);

        $user = User::factory()->create([
            'name' => 'Иван Петров',
            'email' => 'ivan@laravel.com',
            'role_id' => $editorRole->id,
            'role' => User::ROLE_VIEWER,
            'phone' => '+7 999 111-22-33',
            'operator' => 'МТС',
            'phone_comment' => 'Старый номер',
        ]);

        SystemState::resetUsersExistCache();

        $payload = [
            'name' => 'Иван Иванов',
            'email' => 'ivan.ivanov@laravel.com',
            'phone' => '+7 999 222-33-44',
            'operator' => 'Билайн',
            'phone_comment' => 'Рабочий номер',
            'role_id' => $auditorRole->id,
            'password' => 'NewPassw0rd!',
            'password_confirmation' => 'NewPassw0rd!',
        ];

        $response = $this->actingAs($superAdmin)
            ->from(route('admin.users.edit', $user))
            ->put(route('admin.users.update', $user), $payload);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status', __('Данные пользователя успешно обновлены.'));

        $user->refresh();

        $this->assertSame('Иван Иванов', $user->name);
        $this->assertSame('ivan.ivanov@laravel.com', $user->email);
        $this->assertSame('+7 999 222-33-44', $user->phone);
        $this->assertSame('Билайн', $user->operator);
        $this->assertSame('Рабочий номер', $user->phone_comment);
        $this->assertSame($auditorRole->id, $user->role_id);
        $this->assertSame(User::ROLE_VIEWER, $user->role);
        $this->assertTrue(Hash::check('NewPassw0rd!', $user->password));
    }

    public function test_password_is_not_required_when_empty(): void
    {
        $superAdminRole = $this->createRole(Role::ROLE_SUPER_ADMIN, 'Супер админ', true);
        $managerRole = $this->createRole('manager', 'Менеджер', true);

        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'role_id' => $superAdminRole->id,
        ]);

        $user = User::factory()->create([
            'name' => 'Ольга Сидорова',
            'email' => 'olga@laravel.com',
            'role_id' => $managerRole->id,
            'role' => User::ROLE_VIEWER,
            'password' => Hash::make('InitialPass1!'),
        ]);

        $originalPasswordHash = $user->password;

        SystemState::resetUsersExistCache();

        $payload = [
            'name' => 'Ольга Сидорова',
            'email' => 'olga@laravel.com',
            'phone' => null,
            'operator' => null,
            'phone_comment' => null,
            'role_id' => $managerRole->id,
            'password' => null,
            'password_confirmation' => null,
        ];

        $response = $this->actingAs($superAdmin)
            ->from(route('admin.users.edit', $user))
            ->put(route('admin.users.update', $user), $payload);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status', __('Данные пользователя успешно обновлены.'));

        $user->refresh();
        $this->assertSame($originalPasswordHash, $user->password);
    }

    private function createRole(string $slug, string $name, bool $isActive): Role
    {
        return Role::create([
            'name' => $name,
            'slug' => $slug,
            'description' => null,
            'is_system' => $slug === Role::ROLE_SUPER_ADMIN,
            'is_active' => $isActive,
        ]);
    }
}


