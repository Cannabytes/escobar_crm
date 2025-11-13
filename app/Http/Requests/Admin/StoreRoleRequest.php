<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyPermission(['roles.create', 'roles.manage']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('roles', 'slug')],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Название роли',
            'slug' => 'Slug роли',
            'description' => 'Описание',
            'is_active' => 'Активность',
            'permissions' => 'Разрешения',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Название роли обязательно для заполнения.',
            'slug.required' => 'Slug роли обязателен для заполнения.',
            'slug.unique' => 'Роль с таким slug уже существует.',
            'slug.alpha_dash' => 'Slug может содержать только буквы, цифры, дефисы и подчёркивания.',
            'permissions.*.exists' => 'Одно или несколько разрешений не существуют.',
        ];
    }
}

