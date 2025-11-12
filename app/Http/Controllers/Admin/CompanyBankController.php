<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyBankController extends Controller
{
    /**
     * Добавить новый банк для компании
     */
    public function store(Request $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);

        // Валидация
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'country' => 'nullable|string|max:100',
            'bank_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $company->id;
        $validated['sort_order'] = Bank::where('company_id', $company->id)->max('sort_order') + 1;

        Bank::create($validated);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Банк успешно добавлен.'));
    }

    /**
     * Обновить данные банка
     */
    public function update(Request $request, Company $company, Bank $bank): RedirectResponse
    {
        $this->authorize('update', $company);

        // Проверяем, что банк принадлежит этой компании
        if ($bank->company_id !== $company->id) {
            return redirect()
                ->route('admin.companies.show', $company)
                ->with('error', __('Банк не найден.'));
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'country' => 'nullable|string|max:100',
            'bank_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $bank->update($validated);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Банк успешно обновлен.'));
    }

    /**
     * Удалить банк (со всеми реквизитами)
     */
    public function destroy(Company $company, Bank $bank): RedirectResponse
    {
        $this->authorize('update', $company);

        // Проверяем, что банк принадлежит этой компании
        if ($bank->company_id !== $company->id) {
            return redirect()
                ->route('admin.companies.show', $company)
                ->with('error', __('Банк не найден.'));
        }

        $bank->delete(); // Реквізити удаляются каскадно

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Банк удален.'));
    }
}
