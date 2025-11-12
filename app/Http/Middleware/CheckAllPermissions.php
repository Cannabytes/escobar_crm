<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAllPermissions
{
    /**
     * Handle an incoming request.
     * Требует наличия ВСЕХ указанных разрешений.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (empty($permissions)) {
            return $next($request);
        }

        // Требуем наличия ВСЕХ указанных разрешений
        if ($user->hasAllPermissions($permissions)) {
            return $next($request);
        }

        abort(403, 'У вас нет прав доступа к этому разделу.');
    }
}

