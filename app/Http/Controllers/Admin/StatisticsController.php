<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCountry = $request->query('country');
        
        $user = auth()->user();
        
        // Получаем компании с банками
        $query = Company::with(['banks' => function ($q) {
            $q->orderBy('sort_order');
        }]);

        // Фильтр по стране
        if ($selectedCountry) {
            $query->where('country', $selectedCountry);
        }

        // Если пользователь не супер-админ и не имеет общих разрешений,
        // показываем только компании, к которым у него есть доступ
        if (!$user->isSuperAdmin() && !$user->hasAnyPermission([
            'companies.view',
            'companies.manage',
        ])) {
            $query->where(function ($q) use ($user) {
                $q->where('moderator_id', $user->id)
                    ->orWhereHas('accessUsers', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
            });
        }

        $companies = $query->orderBy('name')->get();

        // Получаем список стран
        $countries = config('countries.list', []);
        
        // Если выбрана страна, получаем список банков для неё
        $banksForCountry = [];
        if ($selectedCountry) {
            $banksForCountry = Bank::getBanksForCountry($selectedCountry);
        } else {
            // Если страна не выбрана, собираем банки из всех стран, где есть компании
            $uniqueCountries = $companies->pluck('country')->unique()->filter();
            foreach ($uniqueCountries as $countryCode) {
                $countryBanks = Bank::getBanksForCountry($countryCode);
                // Объединяем массивы, сохраняя ключи (полные названия)
                foreach ($countryBanks as $fullName => $shortName) {
                    if (!isset($banksForCountry[$fullName])) {
                        $banksForCountry[$fullName] = $shortName;
                    }
                }
            }
        }

        // Подготавливаем данные для таблицы
        $tableData = [];
        foreach ($companies as $company) {
            $companyBanks = [];
            
            // Для каждого банка из конфига проверяем, есть ли он у компании
            foreach ($banksForCountry as $bankFullName => $bankShortName) {
                $bank = $company->banks->firstWhere('name', $bankFullName);
                $companyBanks[$bankFullName] = [
                    'exists' => $bank !== null,
                    'status' => $bank ? $bank->status : null,
                    'short_name' => $bankShortName,
                ];
            }
            
            $tableData[] = [
                'company' => $company,
                'banks' => $companyBanks,
            ];
        }

        return view('admin.statistics.index', [
            'companies' => $companies,
            'countries' => $countries,
            'selectedCountry' => $selectedCountry,
            'banksForCountry' => $banksForCountry,
            'tableData' => $tableData,
        ]);
    }
}
