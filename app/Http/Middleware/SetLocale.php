<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $defaultLocale = SiteSetting::value('default_language', config('app.locale', 'th'));
        $locale = session('locale', $defaultLocale);

        if (! in_array($locale, ['th', 'en'], true)) {
            $locale = in_array($defaultLocale, ['th', 'en'], true) ? $defaultLocale : 'en';
            session(['locale' => $locale]);
        }

        App::setLocale($locale);

        return $next($request);
    }
}
