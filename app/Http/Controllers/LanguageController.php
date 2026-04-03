<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switchLanguage($language)
    {
        if (in_array($language, ['en', 'fr', 'ar'])) {
            session(['locale' => $language]);
            app()->setLocale($language);
        }
        
        return redirect()->back();
    }
}
