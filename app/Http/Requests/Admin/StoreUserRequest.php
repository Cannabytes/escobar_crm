<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->string('name')->trim()->value(),
            'email' => $this->string('email')->trim()->lower()->value(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:150', Rule::unique(User::class, 'email')],
            'password' => ['required', 'string', 'min:6', 'max:128', 'confirmed'],
            'role' => ['required', 'string', Rule::in([
                User::ROLE_SUPER_ADMIN,
                User::ROLE_MODERATOR,
                User::ROLE_VIEWER,
            ])],
            'phone' => ['nullable', 'string', 'max:64'],
            'operator' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('Имя'),
            'email' => __('Email'),
            'password' => __('Пароль'),
            'password_confirmation' => __('Подтверждение пароля'),
            'role' => __('Роль'),
            'phone' => __('Телефон'),
            'operator' => __('Оператор'),
        ];
    }
}

