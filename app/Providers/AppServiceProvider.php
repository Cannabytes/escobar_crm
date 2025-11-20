<?php

namespace App\Providers;

use App\Models\ChatRoom;
use App\Services\CurrencyRateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->usePublicPath(base_path());

        $this->app->singleton(CurrencyRateService::class);
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

        View::composer('partials.admin.navbar', function ($view): void {
            /** @var CurrencyRateService $currencyRates */
            $currencyRates = app(CurrencyRateService::class);

            $view->with('currencyTicker', $currencyRates->getRates(['USD', 'EUR', 'CNY']));
        });

        View::composer('partials.admin.sidebar', function ($view) {
            $user = Auth::user();

            $companies = collect();
            $hasUnreadPrivateMessages = false;

            if ($user) {
                // Используем новый метод getMenuCompanies() который учитывает выбор пользователя
                $companies = $user->getMenuCompanies();

                $hasUnreadPrivateMessages = DB::table('chat_room_user as cru')
                    ->join('chat_rooms as cr', 'cru.chat_room_id', '=', 'cr.id')
                    ->join('chat_messages as cm', 'cm.chat_room_id', '=', 'cr.id')
                    ->where('cr.type', ChatRoom::TYPE_PRIVATE)
                    ->where('cru.user_id', $user->id)
                    ->where('cm.user_id', '!=', $user->id)
                    ->where(function ($query) {
                        $query->whereNull('cru.last_read_at')
                            ->orWhereColumn('cm.created_at', '>', 'cru.last_read_at');
                    })
                    ->exists();
            }

            $view->with([
                'sidebarUserCompanies' => $companies,
                'hasUnreadPrivateMessages' => $hasUnreadPrivateMessages,
            ]);
        });
    }
}
