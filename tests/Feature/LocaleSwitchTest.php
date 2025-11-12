<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\SystemState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_locale_and_see_translated_interface(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        SystemState::resetUsersExistCache();

        $this->actingAs($user)
            ->from(route('admin.companies.index'))
            ->get(route('locale.switch', ['locale' => 'ru']))
            ->assertRedirect(route('admin.companies.index'))
            ->assertSessionHas('locale', 'ru');

        $responseRu = $this->actingAs($user)
            ->withSession(['locale' => 'ru'])
            ->get(route('admin.companies.index'));

        $responseRu->assertSeeText(__('Компании', [], 'ru'));

        $responseEn = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('admin.companies.index'));

        $responseEn->assertSeeText(__('Компании'));
    }

    public function test_switching_to_unsupported_locale_returns_404(): void
    {
        $this->get(route('locale.switch', ['locale' => 'de']))
            ->assertNotFound();
    }
}

