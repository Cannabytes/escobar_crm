<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FAQRCode\Google2FA as Google2FAQRCode;

class TwoFactorController extends Controller
{
    /**
     * Показать страницу настройки 2FA
     */
    public function index(): View
    {
        $user = Auth::user();
        $google2fa = new Google2FA();
        
        // Если 2FA уже включена, показываем статус
        if ($user->hasTwoFactorEnabled()) {
            return view('admin.two-factor.index', [
                'user' => $user,
                'enabled' => true,
            ]);
        }

        // Генерируем новый секрет, если его еще нет
        if (!$user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => encrypt($secret)]);
        } else {
            $secret = decrypt($user->two_factor_secret);
        }

        // Генерируем QR-код - используем роут для получения изображения
        $qrCodeUrl = route('admin.two-factor.qr-code');

        return view('admin.two-factor.index', [
            'user' => $user,
            'enabled' => false,
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * Подтвердить и включить 2FA
     */
    public function enable(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $secret = decrypt($user->two_factor_secret);

        // Проверяем код
        $valid = $google2fa->verifyKey($secret, $request->code, config('twofactor.window', 1));

        if (!$valid) {
            return back()->withErrors([
                'code' => __('twofactor.invalid_code'),
            ]);
        }

        // Включаем 2FA
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        return redirect()->route('admin.two-factor.index')
            ->with('success', __('twofactor.enabled_successfully'));
    }

    /**
     * Отключить 2FA
     */
    public function disable(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        // Проверяем пароль
        if (!\Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => __('twofactor.invalid_password'),
            ]);
        }

        // Отключаем 2FA
        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
        ]);

        return redirect()->route('admin.two-factor.index')
            ->with('success', __('twofactor.disabled_successfully'));
    }

    /**
     * Получить QR-код как изображение
     */
    public function qrCode(): Response
    {
        $user = Auth::user();
        $google2fa = new Google2FA();
        
        // Получаем секрет
        if (!$user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => encrypt($secret)]);
        } else {
            $secret = decrypt($user->two_factor_secret);
        }
        
        try {
            // Используем автоматическое определение бэкенда
            $google2faQRCode = new Google2FAQRCode();
            
            // Получаем issuer из конфига, если не установлен - используем значение по умолчанию
            $issuer = config('twofactor.issuer', 'Escobar CRM');
            
            $qrCodeData = $google2faQRCode->getQRCodeInline(
                $issuer,
                $user->email,
                $secret,
                config('twofactor.qr_code_size', 200)
            );
            
            // Проверяем, что данные получены
            if (empty($qrCodeData)) {
                abort(500, 'QR код не может быть сгенерирован');
            }
            
            // Если это SVG, возвращаем как SVG
            $trimmed = trim($qrCodeData);
            if (str_starts_with($trimmed, '<svg')) {
                // Удаляем XML декларацию, если есть
                $svg = preg_replace('/<\?xml[^>]*\?>/', '', $qrCodeData);
                return response($svg, 200)
                    ->header('Content-Type', 'image/svg+xml')
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            }
            
            // Если это data URI, извлекаем данные
            if (str_starts_with($qrCodeData, 'data:image/svg+xml')) {
                $svg = base64_decode(substr($qrCodeData, strpos($qrCodeData, ',') + 1));
                return response($svg, 200)
                    ->header('Content-Type', 'image/svg+xml')
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            }
            
            // Если это PNG base64
            if (str_starts_with($qrCodeData, 'data:image/png')) {
                $png = base64_decode(substr($qrCodeData, strpos($qrCodeData, ',') + 1));
                return response($png, 200)
                    ->header('Content-Type', 'image/png')
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            }
            
            // По умолчанию возвращаем как есть
            return response($qrCodeData, 200)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        } catch (\Exception $e) {
            \Log::error('Error generating QR code', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, 'Ошибка генерации QR кода: ' . $e->getMessage());
        }
    }
}
