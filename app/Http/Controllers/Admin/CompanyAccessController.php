<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyAccessController extends Controller
{
    public function store(Request $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'access_type' => 'required|in:view,edit',
        ]);

        CompanyAccess::updateOrCreate(
            [
                'company_id' => $company->id,
                'user_id' => $validated['user_id'],
            ],
            ['access_type' => $validated['access_type']]
        );

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Доступ пользователя настроен.'));
    }

    public function destroy(Company $company, CompanyAccess $access): RedirectResponse
    {
        $this->authorize('update', $company);

        if ($access->company_id !== $company->id) {
            return redirect()
                ->route('admin.companies.show', $company)
                ->with('error', __('Доступ не найден.'));
        }

        $access->delete();

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Доступ пользователя удален.'));
    }
}
