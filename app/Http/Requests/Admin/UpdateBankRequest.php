<?php

namespace App\Http\Requests\Admin;

use App\Models\Bank;
use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBankRequest extends FormRequest
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

        return $this->user()?->can('update', $bank) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'country' => ['nullable', 'string', 'max:100'],
            'bank_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'login' => ['nullable', 'string', 'max:191'],
            'login_id' => ['nullable', 'string', 'max:191'],
            'password' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:191'],
            'email_password' => ['nullable', 'string'],
            'online_banking_url' => ['nullable', 'url', 'max:500'],
            'manager_name' => ['nullable', 'string', 'max:191'],
            'manager_phone' => ['nullable', 'string', 'max:100'],
        ];
    }
}

