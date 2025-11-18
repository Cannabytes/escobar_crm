<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $company = $this->route('company');

        if ($company) {
            return $user?->can('update', $company) ?? false;
        }

        return $user?->can('create', \App\Models\Company::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->name ? trim($this->name) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'country' => ['required', 'string', 'max:100'],
            'moderator_id' => ['required', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('название компании'),
            'country' => __('страна'),
            'moderator_id' => __('модератор'),
        ];
    }
}
