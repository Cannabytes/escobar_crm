<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyCompaniesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Отобразить страницу "Мои компании"
     */
    public function index(): View
    {
        $user = auth()->user();

        // Получаем все доступные компании
        $allCompanies = $user->allAccessibleCompanies()
            ->with(['moderator', 'accessUsers'])
            ->orderBy('name', 'asc')
            ->get();

        // Получаем ID выбранных компаний для меню
        $selectedCompanyIds = $user->menuCompanies()->pluck('companies.id')->toArray();

        return view('admin.my-companies.index', compact('allCompanies', 'selectedCompanyIds'));
    }

    /**
     * Обновить список компаний для отображения в меню
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'company_ids' => 'array',
            'company_ids.*' => 'integer|exists:companies,id',
        ]);

        $user = auth()->user();
        $companyIds = $request->input('company_ids', []);

        $user->updateMenuCompanies($companyIds);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Настройки меню успешно обновлены.'),
            ]);
        }

        return redirect()
            ->route('admin.my-companies.index')
            ->with('status', __('Настройки меню успешно обновлены.'));
    }
}
