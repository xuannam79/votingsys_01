<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CheckDateController extends Controller
{
    public function checkDateClosePoll(Request $request)
    {
//check
        if ($request->ajax()) {
            $inputs = $request->only('date_close_poll');
            $dateClosePoll = $inputs['date_close_poll'];

            //check time close poll
            if (Carbon::now()->format('y/m/d h:i') > Carbon::parse($dateClosePoll)->format('y/m/d h:i')) {
                return response()->json(['success' => false]);
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
}
