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
            
            // Обновляем только если прошло больше 5 минут с последнего обновления
            // Это предотвращает избыточные обновления БД
            if (
                !$user->last_activity_at || 
                $user->last_activity_at->diffInMinutes(now()) >= 5
            ) {
                $user->update([
                    'last_activity_at' => now()
                ]);
            }
        }

        return $next($request);
    }
}
