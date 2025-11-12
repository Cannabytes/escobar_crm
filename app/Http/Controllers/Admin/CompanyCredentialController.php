<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyCredential;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyCredentialController extends Controller
{
    public function store(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'login' => 'nullable|string|max:191',
            'login_id' => 'nullable|string|max:191',
            'password' => 'nullable|string',
            'email' => 'nullable|email|max:191',
            'email_password' => 'nullable|string',
            'online_banking_url' => 'nullable|url|max:500',
            'manager_name' => 'nullable|string|max:191',
            'manager_phone' => 'nullable|string|max:64',
        ]);

        DB::transaction(function () use ($company, $validated) {
            $validated['company_id'] = $company->id;

            CompanyCredential::updateOrCreate(
                ['company_id' => $company->id],
                $validated
            );
        });

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Учетные данные сохранены.'));
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        return $this->store($request, $company);
    }
}
