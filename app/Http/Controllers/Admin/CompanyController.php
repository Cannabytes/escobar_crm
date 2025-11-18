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
        $this->authorizeResource(Company::class, 'company', [
            'except' => ['removeModerator']
        ]);
    }

    public function index(): View
    {
        $user = auth()->user();
        
        $query = Company::with(['moderator', 'accessUsers']);

        // Если пользователь не супер-админ и не имеет общих разрешений,
        // показываем только компании, к которым у него есть доступ
        if (!$user->isSuperAdmin() && !$user->hasAnyPermission([
            'companies.view',
            'companies.manage',
            'companies.create',
            'companies.edit',
        ])) {
            $query->where(function ($q) use ($user) {
                $q->where('moderator_id', $user->id)
                    ->orWhereHas('accessUsers', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
            });
        }

        $companies = $query->latest()->paginate(20);

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
            'banks' => fn ($query) => $query->with('details'),
            'accessUsers',
            'licenses',
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
            'banks' => fn ($query) => $query->with('details'),
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

    public function removeModerator(Company $company): RedirectResponse
    {
        // Только супер-админ может удалить главного модератора
        if (auth()->user()?->role !== User::ROLE_SUPER_ADMIN) {
            abort(403, __('Только супер-администратор может удалить главного модератора.'));
        }

        $this->authorize('update', $company);

        $company->update(['moderator_id' => null]);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Главный модератор удален.'));
    }
}
