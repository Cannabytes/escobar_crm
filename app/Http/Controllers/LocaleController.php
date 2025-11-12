<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        $supported = array_keys(config('app.supported_locales', []));

        if (! in_array($locale, $supported, true)) {
            abort(404);
        }

        Session::put('locale', $locale);

        $previousUrl = url()->previous();

        $redirectUrl = $request->input('redirect', $previousUrl ?: url('/'));

        if (! $this->isSafeRedirect($redirectUrl, $request)) {
            $redirectUrl = url('/');
        }

        return redirect($redirectUrl);
    }

    private function isSafeRedirect(?string $url, Request $request): bool
    {
        if (! $url) {
            return false;
        }

        $root = $request->getSchemeAndHttpHost();

        return str_starts_with($url, $root);
    }
}

