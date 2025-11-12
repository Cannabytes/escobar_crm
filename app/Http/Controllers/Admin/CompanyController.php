<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompanyRequest;
use App\Http\Requests\Admin\UpdateCompanyLicenseRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Company::class, 'company');
    }

    public function index(): View
    {
        $companies = Company::with(['moderator', 'accessUsers'])
            ->latest()
            ->paginate(20);

        return view('admin.companies.index', compact('companies'));
    }

    public function create(): View
    {
        $this->authorize('create', Company::class);

        // Модератором может быть любой пользователь
        $moderators = User::orderBy('name')->get();

        return view('admin.companies.create', compact('moderators'));
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = [
                'name' => $request->string('name')->value(),
                'country' => $request->string('country')->value(),
                'moderator_id' => $request->integer('moderator_id'),
            ];

            if ($request->hasFile('license_file')) {
                $file = $request->file('license_file');
                $path = $file->store('licenses', 'public');
                $data['license_file'] = $path;
            }

            Company::create($data);
        });

        return redirect()
            ->route('admin.companies.index')
            ->with('status', __('Компания успешно добавлена.'));
    }

    public function show(Company $company): View
    {
        $company->load([
            'moderator',
            'bankAccounts',
            'banks' => fn($query) => $query->with('details'),
            'credentials',
            'accessUsers',
        ]);

        return view('admin.companies.show', compact('company'));
    }

    public function edit(Company $company): View
    {
        // Модератором может быть любой пользователь
        $moderators = User::orderBy('name')->get();

        $company->load([
            'moderator',
            'bankAccounts',
            'banks' => fn($query) => $query->with('details'),
            'credentials',
            'accessUsers',
        ]);

        return view('admin.companies.edit', compact('company', 'moderators'));
    }

    public function update(StoreCompanyRequest $request, Company $company): RedirectResponse
    {
        DB::transaction(function () use ($request, $company) {
            $data = [
                'name' => $request->string('name')->value(),
                'country' => $request->string('country')->value(),
                'moderator_id' => $request->integer('moderator_id'),
            ];

            if ($request->hasFile('license_file')) {
                // Удаляем старый файл
                if ($company->license_file) {
                    Storage::disk('public')->delete($company->license_file);
                }

                $file = $request->file('license_file');
                $path = $file->store('licenses', 'public');
                $data['license_file'] = $path;
            }

            $company->update($data);
        });

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Компания успешно обновлена.'));
    }

    public function updateLicense(UpdateCompanyLicenseRequest $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $company->update($request->validated());

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Данные компании обновлены.'));
    }

    public function destroy(Company $company): RedirectResponse
    {
        DB::transaction(function () use ($company) {
            if ($company->license_file) {
                Storage::disk('public')->delete($company->license_file);
            }

            $company->delete();
        });

        return redirect()
            ->route('admin.companies.index')
            ->with('status', __('Компания успешно удалена.'));
    }
}
