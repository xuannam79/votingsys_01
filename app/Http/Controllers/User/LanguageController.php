<?php

namespace App\Http\Controllers\User;

use Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $lang = $request->input('lang');
            Session::put('locale', $lang);

            return response()->json([
                'success' => true,
                'url_back' => url()->previous(),
            ]);
        }

        return response()->json(['success' => false]);
    }
}
