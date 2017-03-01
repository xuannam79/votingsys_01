<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FeedbackRequest;
use App\Mail\Feeback;
use Mail;

class FeedBackController extends ApiController
{
    public function sendFeedback(FeedbackRequest $request)
    {
        $dataFeedback = $request->only('name', 'email', 'feedback');
        Mail::to(config('mail.from.address'))->queue(new Feeback($dataFeedback));

        if (count(Mail::failures())) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, config('settings.notification.danger'));
        }

        return $this->trueJson(config('settings.notification.success'));
    }
}
