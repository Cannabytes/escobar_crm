<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyBankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyBankAccountController extends Controller
{
    public function store(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:191',
            'country' => 'required|string|max:100',
            'company_name' => 'required|string|max:191',
            'currency' => 'required|string|max:10',
            'account_number' => 'required|string|max:100',
            'iban' => 'nullable|string|max:50',
            'swift' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($company, $validated) {
            $validated['company_id'] = $company->id;
            $validated['sort_order'] = $company->bankAccounts()->max('sort_order') + 1;

            CompanyBankAccount::create($validated);
        });

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Банковский счет добавлен.'));
    }

    public function update(Request $request, Company $company, CompanyBankAccount $bankAccount): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:191',
            'country' => 'required|string|max:100',
            'company_name' => 'required|string|max:191',
            'currency' => 'required|string|max:10',
            'account_number' => 'required|string|max:100',
            'iban' => 'nullable|string|max:50',
            'swift' => 'nullable|string|max:20',
        ]);

        $bankAccount->update($validated);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Банковский счет обновлен.'));
    }

    public function destroy(Company $company, CompanyBankAccount $bankAccount): RedirectResponse
    {
        $bankAccount->delete();

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Банковский счет удален.'));
    }
}
