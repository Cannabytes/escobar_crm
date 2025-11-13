<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBankDetailRequest;
use App\Http\Requests\Admin\UpdateBankDetailRequest;
use App\Models\Bank;
use App\Models\BankDetail;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class CompanyBankDetailController extends Controller
{
    /**
     * Добавить реквизит банку
     */
    public function store(StoreBankDetailRequest $request, Company $company, Bank $bank): RedirectResponse
    {
        $validated = $request->validated();

        $validated['bank_id'] = $bank->id;

        $maxSortOrder = BankDetail::where('bank_id', $bank->id)->max('sort_order');
        $validated['sort_order'] = (int) ($maxSortOrder ?? 0) + 1;

        $validated['detail_type'] = 'account_number';
        $validated['detail_key'] = 'account_number';
        $validated['detail_value'] = '';
        $validated['is_primary'] = false;
        $validated['notes'] = null;
        $validated['status'] = $validated['status'] ?? 'active';

        BankDetail::create($validated);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Реквизит успешно добавлен.'));
    }

    /**
     * Обновить реквизит
     */
    public function update(UpdateBankDetailRequest $request, Company $company, BankDetail $detail): RedirectResponse
    {
        $validated = $request->validated();

        $validated['detail_type'] = $detail->detail_type;
        $validated['detail_key'] = $detail->detail_key;
        $validated['detail_value'] = $detail->detail_value;
        $validated['is_primary'] = $detail->is_primary;
        $validated['notes'] = $detail->notes;
        $validated['status'] = $validated['status'] ?? $detail->status;

        $detail->update($validated);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Реквизит успешно обновлен.'));
    }

    /**
     * Удалить реквизит
     */
    public function destroy(Company $company, BankDetail $detail): RedirectResponse|JsonResponse
    {
        try {
            $bank = $detail->bank;

            // Проверяем, что реквизит принадлежит компании
            if (!$bank || $bank->company_id !== $company->id) {
                if (request()->expectsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Реквизит не найден.')
                    ], 404);
                }

                return redirect()
                    ->route('admin.companies.show', $company)
                    ->with('error', __('Реквизит не найден.'));
            }

            // Проверяем права доступа
            $this->authorize('manageDetails', $bank);

            $detailId = $detail->id;
            $detail->delete();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Реквизит удален.'),
                    'detail_id' => $detailId
                ]);
            }

            return redirect()
                ->route('admin.companies.show', $company)
                ->with('status', __('Реквизит удален.'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('У вас нет прав для удаления этого реквизита.')
                ], 403);
            }

            return redirect()
                ->route('admin.companies.show', $company)
                ->with('error', __('У вас нет прав для удаления этого реквизита.'));
        } catch (\Exception $e) {
            Log::error('Ошибка при удалении реквизита', [
                'detail_id' => $detail->id ?? null,
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Ошибка при удалении реквизита. Попробуйте позже.')
                ], 500);
            }

            return redirect()
                ->route('admin.companies.show', $company)
                ->with('error', __('Ошибка при удалении реквизита. Попробуйте позже.'));
        }
    }
}
