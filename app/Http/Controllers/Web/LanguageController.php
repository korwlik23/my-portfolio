<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        $supportedLocales = ['th', 'en'];
        if (in_array($locale, $supportedLocales)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }
        return back();
    }
}
