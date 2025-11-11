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
            'license_number' => $this->license_number ? trim($this->license_number) : null,
            'registration_number' => $this->registration_number ? trim($this->registration_number) : null,
            'jurisdiction_zone' => $this->jurisdiction_zone ? trim($this->jurisdiction_zone) : null,
            'owner_name' => $this->owner_name ? trim($this->owner_name) : null,
            'email' => $this->email ? trim(mb_strtolower($this->email)) : null,
            'phone' => $this->phone ? trim($this->phone) : null,
            'website' => $this->website ? trim($this->website) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'license_number' => ['required', 'string', 'max:191', 'unique:companies,license_number'],
            'registration_number' => ['required', 'string', 'max:191', 'unique:companies,registration_number'],
            'incorporation_date' => ['required', 'date', 'before_or_equal:today'],
            'expiration_date' => ['required', 'date', 'after:incorporation_date'],
            'jurisdiction_zone' => ['required', 'string', 'max:191'],
            'business_activities' => ['required', 'string'],
            'legal_address' => ['required', 'string', 'max:255'],
            'factual_address' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191'],
            'phone' => ['required', 'string', 'max:64'],
            'website' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'license_number' => __('номер лицензии'),
            'registration_number' => __('номер регистрации'),
            'incorporation_date' => __('дата основания'),
            'expiration_date' => __('дата истечения срока'),
            'jurisdiction_zone' => __('юрисдикция'),
            'business_activities' => __('виды деятельности'),
            'legal_address' => __('юридический адрес'),
            'factual_address' => __('фактический адрес'),
            'owner_name' => __('имя владельца'),
            'email' => __('email'),
            'phone' => __('телефон'),
            'website' => __('веб-сайт'),
        ];
    }
}

