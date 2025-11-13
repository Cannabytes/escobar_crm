<?php

namespace App\Http\Requests\Admin;

use App\Models\Bank;
use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class StoreBankDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company|null $company */
        $company = $this->route('company');

        /** @var Bank|null $bank */
        $bank = $this->route('bank');

        if (! $company || ! $bank || $bank->company_id !== $company->id) {
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




