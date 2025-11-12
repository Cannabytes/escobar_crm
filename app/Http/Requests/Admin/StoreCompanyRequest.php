<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'license_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('название компании'),
            'country' => __('страна'),
            'moderator_id' => __('модератор'),
            'license_file' => __('файл лицензии'),
        ];
    }
}
