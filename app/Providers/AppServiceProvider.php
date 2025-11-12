<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->usePublicPath(base_path());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        Password::defaults(function () {
            return Password::min(6);
        });

        View::share('supportedLocales', config('app.supported_locales', []));

        View::composer('*', function ($view): void {
            $view->with('currentLocale', app()->getLocale());
        });

        
        View::composer('partials.admin.sidebar', function ($view) {
            $user = Auth::user();

            $companies = collect();

            if ($user) {
                $moderated = $user->moderatedCompanies()
                    ->orderBy('name')
                    ->get();

                $accessible = $user->accessibleCompanies()
                    ->orderBy('name')
                    ->get();

                $companies = $moderated
                    ->merge($accessible)
                    ->unique('id')
                    ->sortBy('name', SORT_LOCALE_STRING)
                    ->values();
            }

            $view->with('sidebarUserCompanies', $companies);
        });
    }
}
