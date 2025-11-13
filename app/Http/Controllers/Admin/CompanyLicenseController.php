<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyLicense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompanyLicenseController extends Controller
{
    /**
     * Загрузить новую лицензию
     */
    public function store(Request $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $request->validate([
            'license_files' => 'required|array|min:1',
            'license_files.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ], [
            'license_files.required' => __('Выберите хотя бы один файл для загрузки.'),
            'license_files.*.image' => __('Файл должен быть изображением.'),
            'license_files.*.mimes' => __('Допустимые форматы: JPEG, PNG, JPG, GIF, WEBP.'),
            'license_files.*.max' => __('Размер файла не должен превышать 10 МБ.'),
        ]);

        DB::transaction(function () use ($request, $company) {
            $maxSortOrder = $company->licenses()->max('sort_order') ?? 0;

            foreach ($request->file('license_files') as $file) {
                $maxSortOrder++;
                $path = $file->store('licenses', 'public');
                
                $company->licenses()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'sort_order' => $maxSortOrder,
                ]);
            }
        });

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Лицензии успешно загружены.'));
    }

    /**
     * Удалить лицензию
     */
    public function destroy(Company $company, CompanyLicense $license): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $company);

        if ($license->company_id !== $company->id) {
            abort(404);
        }

        DB::transaction(function () use ($license) {
            // Удаляем файл из storage
            if (Storage::disk('public')->exists($license->file_path)) {
                Storage::disk('public')->delete($license->file_path);
            }

            $license->delete();
        });

        // Если это AJAX запрос, возвращаем JSON
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Лицензия успешно удалена.'),
            ]);
        }

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('status', __('Лицензия успешно удалена.'));
    }
}
