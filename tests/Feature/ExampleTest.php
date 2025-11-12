<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Support\SystemState;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        if (Schema::hasTable('users')) {
            User::query()->delete();
        }
        SystemState::resetUsersExistCache();

        $response = $this->get('/');

        $response->assertRedirect(route('install.super-admin.create'));
    }
}
