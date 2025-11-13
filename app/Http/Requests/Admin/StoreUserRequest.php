<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
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
            'role_id' => $this->integer('role_id'),
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
            'phone' => ['nullable', 'string', 'max:64'],
            'operator' => ['nullable', 'string', 'max:50'],
            'role_id' => [
                'required',
                'integer',
                Rule::exists(Role::class, 'id')->where('is_active', true),
            ],
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
            'phone' => __('Телефон'),
            'operator' => __('Оператор'),
            'role_id' => __('Роль'),
        ];
    }
}

