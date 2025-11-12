<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\CompanyAccess;
use App\Models\CompanyBankAccount;
use App\Models\User;
use App\Support\SystemState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyMenuAndFlashTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_shows_companies_assigned_to_user(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_MODERATOR,
        ]);

        $otherModerator = User::factory()->create();

        $moderatedCompany = Company::create([
            'name' => 'Moderated LLC',
            'country' => 'UAE',
            'moderator_id' => $user->getKey(),
        ]);

        $accessibleCompany = Company::create([
            'name' => 'Accessible LTD',
            'country' => 'USA',
            'moderator_id' => $otherModerator->getKey(),
        ]);

        CompanyAccess::create([
            'company_id' => $accessibleCompany->getKey(),
            'user_id' => $user->getKey(),
            'access_type' => 'view',
        ]);

        SystemState::resetUsersExistCache();

        $response = $this->actingAs($user)->get(route('admin.companies.index'));

        $response->assertSeeText('Мои компании');
        $response->assertSeeText($moderatedCompany->name);
        $response->assertSeeText($accessibleCompany->name);
        $response->assertDontSeeText('Нет закрепленных компаний');
    }

    public function test_company_creation_flash_message_rendered_once(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $moderator = User::factory()->create();

        SystemState::resetUsersExistCache();

        $response = $this->actingAs($superAdmin)
            ->followingRedirects()
            ->post(route('admin.companies.store'), [
                'name' => 'New Company',
                'country' => 'UK',
                'moderator_id' => $moderator->getKey(),
            ]);

        $message = 'Компания успешно добавлена.';

        $response->assertSeeText($message);
        $this->assertSame(1, substr_count($response->getContent(), $message));
    }

    public function test_bank_account_deletion_flash_message_rendered_once(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $company = Company::create([
            'name' => 'Flash Test Corp',
            'country' => 'KSA',
            'moderator_id' => $superAdmin->getKey(),
        ]);

        $bankAccount = CompanyBankAccount::create([
            'company_id' => $company->getKey(),
            'bank_name' => 'Test Bank',
            'country' => 'KSA',
            'company_name' => 'Flash Test Corp',
            'currency' => 'USD',
            'account_number' => '123456789',
            'iban' => null,
            'swift' => null,
            'sort_order' => 1,
        ]);

        SystemState::resetUsersExistCache();

        $response = $this->actingAs($superAdmin)
            ->followingRedirects()
            ->delete(route('admin.companies.bank-accounts.destroy', [$company, $bankAccount]));

        $message = 'Банковский счет удален.';

        $response->assertSeeText($message);
        $this->assertSame(1, substr_count($response->getContent(), $message));
    }
}

