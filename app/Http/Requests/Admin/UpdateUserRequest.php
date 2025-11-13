<?php

namespace App\Http\Requests\Admin;

use App\DTO\Admin\UpdateUserData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyPermission(['users.edit', 'users.manage']);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->string('name')->trim()->value(),
            'email' => $this->string('email')->trim()->lower()->value(),
            'role_id' => $this->integer('role_id'),
            'phone' => $this->prepareNullableTrimmed('phone'),
            'operator' => $this->prepareNullableTrimmed('operator'),
            'phone_comment' => $this->prepareNullableString('phone_comment'),
            'password' => $this->prepareNullableRaw('password'),
            'password_confirmation' => $this->prepareNullableRaw('password_confirmation'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->getKey();

        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:150',
                Rule::unique(User::class, 'email')->ignore($userId),
            ],
            'phone' => ['nullable', 'string', 'max:64'],
            'operator' => ['nullable', 'string', 'max:50'],
            'phone_comment' => ['nullable', 'string'],
            'role_id' => [
                'required',
                'integer',
                Rule::exists(Role::class, 'id')->where('is_active', true),
            ],
            'password' => [
                'nullable',
                'string',
                Password::min(8)->letters()->numbers(),
                'confirmed',
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
            'phone' => __('Телефон'),
            'operator' => __('Оператор'),
            'phone_comment' => __('Комментарий к телефону'),
            'role_id' => __('Роль'),
            'password' => __('Пароль'),
            'password_confirmation' => __('Подтверждение пароля'),
        ];
    }

    public function toDto(): UpdateUserData
    {
        return new UpdateUserData(
            name: $this->string('name')->trim()->value(),
            email: $this->string('email')->trim()->lower()->value(),
            roleId: $this->integer('role_id'),
            phone: $this->input('phone'),
            operator: $this->input('operator'),
            phoneComment: $this->input('phone_comment'),
            password: $this->input('password'),
        );
    }

    private function prepareNullableTrimmed(string $key): ?string
    {
        $value = $this->string($key)->trim();

        return $value->isEmpty() ? null : $value->value();
    }

    private function prepareNullableString(string $key): ?string
    {
        $value = $this->input($key);

        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed === '' ? null : $trimmed;
        }

        return null;
    }

    private function prepareNullableRaw(string $key): ?string
    {
        $value = $this->input($key);

        if (is_string($value)) {
            return $value === '' ? null : $value;
        }

        return null;
    }
}

