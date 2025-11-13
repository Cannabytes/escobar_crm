<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChatRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(['public', 'private'])],
            'name' => ['nullable', 'string', 'max:120', 'required_if:type,public'],
            'participant_id' => [
                Rule::requiredIf(fn () => $this->input('type') === 'private'),
                'nullable',
                'integer',
                'exists:users,id',
                Rule::notIn([auth()->id()]),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('Название чата'),
            'participant_id' => __('Участник'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => $this->input('type', 'public'),
        ]);
    }
}

