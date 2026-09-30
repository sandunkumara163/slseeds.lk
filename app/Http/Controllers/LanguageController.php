<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        if (array_key_exists($lang, config('app.locales', ['en' => 'English', 'si' => 'Sinhala', 'ta' => 'Tamil']))) {
            Session::put('locale', $lang);
        }
        return redirect()->back();
    }
}
