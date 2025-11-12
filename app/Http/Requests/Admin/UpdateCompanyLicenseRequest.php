<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'license_number' => ['required', 'string', 'max:191'],
            'registration_number' => ['required', 'string', 'max:191'],
            'incorporation_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date', 'after_or_equal:incorporation_date'],
            'free_zone' => ['required', 'string', 'max:191'],
            'business_activities' => ['required', 'string'],
            'legal_address' => ['required', 'string'],
            'actual_address' => ['required', 'string'],
            'owner_name' => ['required', 'string', 'max:191'],
            'owner_email' => ['required', 'email', 'max:191'],
            'owner_phone' => ['required', 'string', 'max:100'],
            'owner_website' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'license_number' => __('номер лицензии'),
            'registration_number' => __('номер регистрации'),
            'incorporation_date' => __('дата основания'),
            'expiry_date' => __('дата истечения срока'),
            'free_zone' => __('зона регистрации'),
            'business_activities' => __('виды деятельности'),
            'legal_address' => __('юридический адрес'),
            'actual_address' => __('фактический адрес'),
            'owner_name' => __('имя владельца'),
            'owner_email' => __('email владельца'),
            'owner_phone' => __('телефон владельца'),
            'owner_website' => __('веб-сайт'),
        ];
    }
}

