<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CheckDateController extends Controller
{
    public function checkDateClosePoll(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->only('date_close_poll');
            $dateClosePoll = $inputs['date_close_poll'];

            //check time close poll
            if (Carbon::now()->toAtomString() > Carbon::parse($dateClosePoll)->toAtomString()) {
                return response()->json(['success' => false]);
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
}
