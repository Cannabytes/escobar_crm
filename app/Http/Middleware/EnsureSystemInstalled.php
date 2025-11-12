<?php

namespace App\Http\Middleware;

use App\Support\SystemState;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSystemInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (SystemState::usersExist()) {
            return $next($request);
        }

        if ($request->routeIs('install.super-admin.*')) {
            return $next($request);
        }

        return redirect()->route('install.super-admin.create');
    }
}


