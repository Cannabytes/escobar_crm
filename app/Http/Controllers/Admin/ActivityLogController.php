<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Отображение списка логов
     */
    public function index(Request $request)
    {
        // Проверяем, что пользователь - супер админ
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied');
        }

        $query = ActivityLog::query()->with('user');

        // Фильтр по пользователю
        if ($request->filled('user_id')) {
            $query->forUser($request->user_id);
        }

        // Фильтр по действию
        if ($request->filled('action')) {
            $query->forAction($request->action);
        }

        // Фильтр по модели
        if ($request->filled('model_type')) {
            $query->forModel($request->model_type);
        }

        // Фильтр по уровню
        if ($request->filled('level')) {
            $query->forLevel($request->level);
        }

        // Фильтр по датам
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Поиск по описанию или IP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            });
        }

        // Сортировка по дате (новые первыми)
        $query->orderBy('created_at', 'desc');

        // Пагинация
        $logs = $query->paginate(50)->withQueryString();

        // Получаем список пользователей для фильтра
        $users = User::orderBy('name')->get();

        // Получаем уникальные типы моделей для фильтра
        $modelTypes = ActivityLog::select('model_type')
            ->distinct()
            ->whereNotNull('model_type')
            ->pluck('model_type');

        // Получаем уникальные действия для фильтра
        $actions = ActivityLog::select('action')
            ->distinct()
            ->pluck('action');

        return view('admin.logs.index', compact(
            'logs',
            'users',
            'modelTypes',
            'actions'
        ));
    }

    /**
     * Просмотр деталей лога
     */
    public function show(ActivityLog $log)
    {
        // Проверяем, что пользователь - супер админ
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied');
        }

        $log->load('user', 'model');

        return view('admin.logs.show', compact('log'));
    }
}
