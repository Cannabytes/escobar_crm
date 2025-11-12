<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Обновляем время последней активности для аутентифицированного пользователя
        if (Auth::check()) {
            $user = Auth::user();
            
            // Обновляем только если прошло больше 1 минуты с последнего обновления
            // Это предотвращает избыточные обновления БД, но обеспечивает актуальность статуса
            if (
                !$user->last_activity_at || 
                $user->last_activity_at->diffInMinutes(now()) >= 1
            ) {
                // Обновляем время последней активности
                $user->updateQuietly(['last_activity_at' => now()]);
            }
        }

        return $next($request);
    }
}
