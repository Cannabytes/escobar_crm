<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankDetail;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyBankDetailController extends Controller
{
    /**
     * Добавить реквізит банку
     */
    public function store(Request $request, Company $company, Bank $bank): RedirectResponse
    {
        $this->authorize('update', $company);

        // Проверяем, что банк принадлежит этой компании
        if ($bank->company_id !== $company->id) {
            return redirect()
                ->route('admin.companies.show', $company)
                ->with('error', __('Банк не найден.'));
        }

        $validated = $request->validate([
            'account_number' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:50',
            'swift' => 'nullable|string|max:20',
            'currency' => 'nullable|string|max:10',
            'status' => 'nullable|string|in:active,inactive,hold,closed',
        ]);

        $validated['bank_id'] = $bank->id;
        $validated['sort_order'] = BankDetail::where('bank_id', $bank->id)->max('sort_order') + 1;
        // Set default values for removed fields
        $validated['detail_type'] = 'account_number'; // Значение по умолчанию
        $validated['detail_key'] = 'account_number';
        $validated['detail_value'] = '';
        $validated['is_primary'] = false;
        $validated['notes'] = null;
        $validated['status'] = $validated['status'] ?? 'active';

        BankDetail::create($validated);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Реквізит успешно добавлен.'));
    }

    /**
     * Обновить реквізит
     */
    public function update(Request $request, Company $company, BankDetail $detail): RedirectResponse
    {
        $this->authorize('update', $company);

        // Проверяем, що реквізит і банк належать цій компанії
        if ($detail->bank->company_id !== $company->id) {
            return redirect()
                ->route('admin.companies.show', $company)
                ->with('error', __('Реквізит не найден.'));
        }

        $validated = $request->validate([
            'account_number' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:50',
            'swift' => 'nullable|string|max:20',
            'currency' => 'nullable|string|max:10',
            'status' => 'nullable|string|in:active,inactive,hold,closed',
        ]);

        // Keep existing values for removed fields
        $validated['detail_type'] = $detail->detail_type; // Сохраняем существующее значение
        $validated['detail_key'] = $detail->detail_key;
        $validated['detail_value'] = $detail->detail_value;
        $validated['is_primary'] = $detail->is_primary;
        $validated['notes'] = $detail->notes;
        $validated['status'] = $validated['status'] ?? $detail->status;

        $detail->update($validated);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Реквізит успешно обновлен.'));
    }

    /**
     * Удалить реквізит
     */
    public function destroy(Company $company, BankDetail $detail): RedirectResponse
    {
        $this->authorize('update', $company);

        // Проверяем, що реквізит принадлежит цій компанії
        if ($detail->bank->company_id !== $company->id) {
            return redirect()
                ->route('admin.companies.show', $company)
                ->with('error', __('Реквізит не найден.'));
        }

        $detail->delete();

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Реквізит удален.'));
    }
}
