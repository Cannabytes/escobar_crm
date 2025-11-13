<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompanyAccessRequest;
use App\Models\Company;
use App\Models\CompanyAccess;
use Illuminate\Http\RedirectResponse;

class CompanyAccessController extends Controller
{
    public function store(StoreCompanyAccessRequest $request, Company $company): RedirectResponse
    {
        $validated = $request->validated();

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

    /**
     * Удаление главного модератора компании.
     */
    public function removeModerator(Company $company): RedirectResponse
    {
        $user = auth()->user();

        if (! $user || ! $user->hasAnyPermission(['companies.manage'])) {
            abort(403, __('У вас нет прав для удаления главного модератора.'));
        }

        $this->authorize('update', $company);

        $company->update(['moderator_id' => null]);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Главный модератор удален.'));
    }
}
