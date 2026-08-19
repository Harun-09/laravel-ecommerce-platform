<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\StorefrontPreferences;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PreferenceController extends Controller
{
    public function updateLanguage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(array_keys(StorefrontPreferences::locales()))],
        ]);

        $request->session()->put(
            StorefrontPreferences::SESSION_LOCALE_KEY,
            StorefrontPreferences::resolveLocale($validated['locale'])
        );

        return back();
    }

    public function updateCurrency(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'currency' => ['required', 'string', Rule::in(array_keys(StorefrontPreferences::currencies()))],
        ]);

        $request->session()->put(
            StorefrontPreferences::SESSION_CURRENCY_KEY,
            StorefrontPreferences::resolveCurrency($validated['currency'])
        );

        return back();
    }
}
