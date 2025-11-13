<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\CompanyAccess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyAccessControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_assign_full_access(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $company = Company::factory()->create();
        $moderator = User::factory()->create();

        $response = $this->actingAs($superAdmin)
            ->post(route('admin.companies.access.store', $company), [
                'user_id' => $moderator->id,
                'access_type' => 'edit',
            ]);

        $response->assertRedirect(route('admin.companies.show', $company));

        $this->assertDatabaseHas('company_user_access', [
            'company_id' => $company->id,
            'user_id' => $moderator->id,
            'access_type' => 'edit',
        ]);
    }

    public function test_super_admin_can_assign_view_only_access(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $company = Company::factory()->create();
        $viewer = User::factory()->create();

        $response = $this->actingAs($superAdmin)
            ->post(route('admin.companies.access.store', $company), [
                'user_id' => $viewer->id,
                'access_type' => 'view',
            ]);

        $response->assertRedirect(route('admin.companies.show', $company));

        $this->assertDatabaseHas('company_user_access', [
            'company_id' => $company->id,
            'user_id' => $viewer->id,
            'access_type' => 'view',
        ]);

        $companyAccess = CompanyAccess::first();

        $this->assertNotNull($companyAccess);
        $this->assertSame('view', $companyAccess->access_type);
    }
}

