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

    /**
     * Удаление главного модератора компании.
     */
    public function removeModerator(Company $company): RedirectResponse
    {
        // Только супер-админ может удалить главного модератора
        if (auth()->user()?->role !== \App\Models\User::ROLE_SUPER_ADMIN) {
            abort(403, __('Только супер-администратор может удалить главного модератора.'));
        }

        $this->authorize('update', $company);

        $company->update(['moderator_id' => null]);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Главный модератор удален.'));
    }
}
