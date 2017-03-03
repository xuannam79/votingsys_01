<?php

namespace App\Http\Controllers\Api;

use Session;
use Illuminate\Http\Request;

class LanguageController extends ApiController
{
    public function store(Request $request)
    {
        $lang = $request->input('lang');

        if (!in_array($lang, config('settings.languages'))) {
             return $this->falseJson(API_RESPONSE_CODE_NOT_FOUND, trans('messages.error.not_found_language'));
        }

        Session::put('locale', $lang);

        return $this->trueJson('success');
    }
}
