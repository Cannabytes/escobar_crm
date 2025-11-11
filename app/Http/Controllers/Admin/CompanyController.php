<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function create(): View
    {
        return view('admin.companies.create');
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            Company::create([
                'license_number' => $request->string('license_number')->value(),
                'registration_number' => $request->string('registration_number')->value(),
                'incorporation_date' => $request->date('incorporation_date')->toDateString(),
                'expiration_date' => $request->date('expiration_date')->toDateString(),
                'jurisdiction_zone' => $request->string('jurisdiction_zone')->value(),
                'business_activities' => $request->string('business_activities')->value(),
                'legal_address' => $request->string('legal_address')->value(),
                'factual_address' => $request->string('factual_address')->value(),
                'owner_name' => $request->string('owner_name')->value(),
                'email' => $request->string('email')->value(),
                'phone' => $request->string('phone')->value(),
                'website' => $request->filled('website') ? $request->string('website')->value() : null,
            ]);
        });

        return redirect()
            ->route('admin.companies.create')
            ->with('status', __('Компания успешно добавлена в систему.'));
    }
}

