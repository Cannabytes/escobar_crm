<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        // Если пользователь не авторизован - редирект на логин
        if (! $user) {
            return redirect()->route('login');
        }

        // Супер админ имеет все права
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Если не переданы разрешения - пропускаем
        if (empty($permissions)) {
            return $next($request);
        }

        // Проверяем права доступа (достаточно любого из указанных)
        if ($user->hasAnyPermission($permissions)) {
            return $next($request);
        }

        // Если прав нет - 403
        abort(403, 'У вас нет прав доступа к этому разделу.');
    }
}

