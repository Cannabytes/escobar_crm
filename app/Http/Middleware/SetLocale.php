<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = array_keys(config('app.supported_locales', []));
        $defaultLocale = config('app.locale');

        $locale = Session::get('locale', $defaultLocale);

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = $defaultLocale;
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        setlocale(LC_TIME, $this->getLocaleVariants($locale));

        return $next($request);
    }

    /**
     * @return array<int, string>
     */
    private function getLocaleVariants(string $locale): array
    {
        return match ($locale) {
            'ru' => ['ru_RU.UTF-8', 'ru_RU', 'ru'],
            'en' => ['en_US.UTF-8', 'en_US', 'en_GB.UTF-8', 'en_GB', 'en'],
            default => [$locale],
        };
    }
}

