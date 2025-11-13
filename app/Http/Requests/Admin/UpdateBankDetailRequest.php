<?php

namespace App\Http\Requests\Admin;

use App\Models\Bank;
use App\Models\BankDetail;
use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBankDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company|null $company */
        $company = $this->route('company');

        /** @var BankDetail|null $detail */
        $detail = $this->route('detail');

        if (! $company || ! $detail) {
            return false;
        }

        /** @var Bank $bank */
        $bank = $detail->bank;

        if ($bank->company_id !== $company->id) {
            return false;
        }

        return $this->user()?->can('manageDetails', $bank) ?? false;
    }

    public function rules(): array
    {
        return [
            'account_number' => ['nullable', 'string', 'max:100'],
            'iban' => ['nullable', 'string', 'max:50'],
            'swift' => ['nullable', 'string', 'max:20'],
            'currency' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', 'string', 'in:active,inactive,hold,closed'],
        ];
    }
}




