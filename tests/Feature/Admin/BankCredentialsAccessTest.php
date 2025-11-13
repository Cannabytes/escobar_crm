<?php

namespace Tests\Feature\Admin;

use App\Models\Bank;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankCredentialsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_store_bank_with_credentials(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $company = Company::factory()->create();

        $response = $this->actingAs($superAdmin)
            ->post(route('admin.companies.banks.store', $company), [
                'name' => 'Test Bank',
                'country' => 'UA',
                'bank_code' => 'MFI123',
                'notes' => 'Primary bank',
                'login' => 'bank-user',
                'login_id' => 'user123',
                'password' => 'secret',
                'email' => 'bank@example.com',
                'email_password' => 'mail-secret',
                'online_banking_url' => 'https://bank.example.com',
                'manager_name' => 'John Doe',
                'manager_phone' => '+380501112233',
            ]);

        $response->assertRedirect(route('admin.companies.show', $company));

        $bank = Bank::first();

        $this->assertNotNull($bank);
        $this->assertSame('bank-user', $bank->login);
        $this->assertSame('https://bank.example.com', $bank->online_banking_url);
    }

    public function test_user_with_edit_access_can_update_bank(): void
    {
        $moderator = User::factory()->create();
        $company = Company::factory()->withModerator($moderator)->create();

        $editor = User::factory()->create();
        $company->accessUsers()->attach($editor->id, ['access_type' => 'edit']);

        $bank = Bank::factory()->for($company)->create([
            'name' => 'Managed Bank',
        ]);

        $response = $this->actingAs($editor)
            ->put(route('admin.companies.banks.update', [$company, $bank]), [
                'name' => 'Managed Bank',
                'country' => 'PL',
                'bank_code' => 'POL001',
                'notes' => 'Updated note',
            ]);

        $response->assertRedirect(route('admin.companies.show', $company));
        $bank->refresh();

        $this->assertSame('PL', $bank->country);
        $this->assertEquals('Updated note', $bank->notes);
    }

    public function test_user_with_view_access_cannot_update_bank(): void
    {
        $moderator = User::factory()->create();
        $company = Company::factory()->withModerator($moderator)->create();

        $viewer = User::factory()->create();
        $company->accessUsers()->attach($viewer->id, ['access_type' => 'view']);

        $bank = Bank::factory()->for($company)->create([
            'name' => 'View Only Bank',
        ]);

        $response = $this->actingAs($viewer)
            ->put(route('admin.companies.banks.update', [$company, $bank]), [
                'name' => 'Updated Bank',
                'country' => 'DE',
                'bank_code' => 'DE123',
                'notes' => 'Should not update',
            ]);

        $response->assertForbidden();
        $bank->refresh();

        $this->assertSame('View Only Bank', $bank->name);
        $this->assertNotSame('DE', $bank->country);
    }

    public function test_user_with_view_access_can_see_online_banking_credentials(): void
    {
        $moderator = User::factory()->create();
        $company = Company::factory()->withModerator($moderator)->create();

        $viewer = User::factory()->create();
        $company->accessUsers()->attach($viewer->id, ['access_type' => 'view']);

        Bank::factory()->for($company)->create([
            'name' => 'Credentials Bank',
            'login' => 'viewer-login',
            'password' => 'Secret123!',
            'online_banking_url' => 'https://bank.example/viewer',
        ]);

        $response = $this->actingAs($viewer)
            ->get(route('admin.companies.show', $company));

        $response->assertOk();
        $response->assertSeeText('Доступ к онлайн-банку');
        $response->assertSee('viewer-login', false);
        $response->assertSee('https://bank.example/viewer', false);
    }
}

