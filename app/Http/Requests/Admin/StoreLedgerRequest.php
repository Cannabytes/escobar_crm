<?php

namespace App\Http\Requests\Admin;

use App\Models\Ledger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLedgerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) $user?->hasAnyPermission(['ledger.manage']);
    }

    public function rules(): array
    {
        return [
            'wallet' => ['required', 'string', 'max:255'],
            'network' => ['nullable', 'string', 'max:120'],
            'currency' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'string', Rule::in(Ledger::statuses())],
        ];
    }
}

