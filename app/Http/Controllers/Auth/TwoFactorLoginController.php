<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorLoginController extends Controller
{
    /**
     * Показать форму ввода кода 2FA
     */
    public function create(Request $request): View|RedirectResponse
    {
        // Проверяем, что пользователь прошел первый этап входа
        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        $userId = $request->session()->get('login.id');
        $user = User::find($userId);

        if (!$user || !$user->hasTwoFactorEnabled()) {
            $request->session()->forget('login.id');
            return redirect()->route('login');
        }

        return view('auth.two-factor-login', [
            'user' => $user,
        ]);
    }

    /**
     * Проверить код 2FA и завершить вход
     */
    public function store(Request $request): RedirectResponse
    {
        // Проверяем, что пользователь прошел первый этап входа
        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        $userId = $request->session()->get('login.id');
        $user = User::find($userId);

        if (!$user || !$user->hasTwoFactorEnabled()) {
            $request->session()->forget('login.id');
            return redirect()->route('login');
        }

        $throttleKey = sprintf(
            'two-factor-login|%s',
            sha1($request->ip() . '|' . $user->id)
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'code' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
            ]);
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);

        // Проверяем код
        $valid = $google2fa->verifyKey($secret, $request->code, config('twofactor.window', 1));

        if (!$valid) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'code' => __('twofactor.invalid_code'),
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->forget('login.id');

        // Входим в систему
        Auth::login($user, $request->session()->get('login.remember', false));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }
}
