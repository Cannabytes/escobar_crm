<?php

namespace App\Http\Requests\Admin;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->route('company');

        return $company instanceof Company
            && $this->user()?->can('update', $company);
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'access_type' => ['required', 'in:view,edit'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => __('Выберите пользователя.'),
            'user_id.exists' => __('Выбранный пользователь не найден.'),
            'access_type.required' => __('Выберите тип доступа.'),
            'access_type.in' => __('Некорректный тип доступа.'),
        ];
    }
}

