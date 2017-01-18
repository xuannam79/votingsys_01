<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FeedbackRequest;
use App\Mail\Feeback;
use Mail;

class FeedbackController extends Controller
{
    public function showFeedback()
    {
        return view('user.feedback');
    }

    public function sendFeedback(FeedbackRequest $request)
    {
        $dataFeedback = $request->only(['name', 'email', 'feedback']);

        Mail::to(config('mail.from.address'))->queue(new Feeback($dataFeedback));

        if (count(Mail::failures())) {
            flash(trans('user.message.feedback_fail'), config('settings.notification.danger'));

            return back();
        }

        flash(trans('user.message.feedback_success'), config('settings.notification.success'));

        return back();
    }
}
