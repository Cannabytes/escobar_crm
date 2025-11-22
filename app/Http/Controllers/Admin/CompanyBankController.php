<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBankRequest;
use App\Http\Requests\Admin\UpdateBankRequest;
use App\Models\Bank;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyBankController extends Controller
{
    /**
     * Получить список банков для страны (AJAX)
     */
    public function getBanksByCountry(Request $request): JsonResponse
    {
        $country = $request->query('country');
        
        if (!$country) {
            return response()->json(['banks' => []]);
        }

        $banks = Bank::getBanksForCountry($country);
        
        return response()->json(['banks' => $banks]);
    }

    /**
     * Добавить новый банк для компании
     */
    public function store(StoreBankRequest $request, Company $company): RedirectResponse
    {
        $validated = $request->validated();

        // Если страна не указана, используем страну компании
        if (empty($validated['country'])) {
            $validated['country'] = $company->country;
        }

        $validated['company_id'] = $company->id;

        $maxSortOrder = Bank::where('company_id', $company->id)->max('sort_order');
        $validated['sort_order'] = (int) ($maxSortOrder ?? 0) + 1;

        Bank::create($validated);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Банк успешно добавлен.'));
    }

    /**
     * Обновить данные банка
     */
    public function update(UpdateBankRequest $request, Company $company, Bank $bank): RedirectResponse
    {
        $validated = $request->validated();

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
        if ($bank->company_id !== $company->id) {
            return redirect()
                ->route('admin.companies.show', $company)
                ->with('error', __('Банк не найден.'));
        }

        $this->authorize('delete', $bank);

        $bank->delete(); // Реквізити удаляются каскадно

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Банк удален.'));
    }
}
