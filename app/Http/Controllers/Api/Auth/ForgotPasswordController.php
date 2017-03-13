<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Validator;

class ForgotPasswordController extends ApiController
{
    use SendsPasswordResetEmails;

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return [
                'status' => API_RESPONSE_CODE_UNPROCESSABLE,
                'error' => true,
                'messages' => $validator->errors()->all(),
            ];
        }

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response != RESET_LINK_SENT) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans($response));
        }

        return $this->trueJson(new \StdClass, trans($response));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator($data = [])
    {
        $trans = trans('user.validate');

        $messages = [
            'email.required' => $trans['email']['required'],
            'email.email' => $trans['email']['email'],
        ];

        return Validator::make($data, [
            'email' => 'required|email',
        ], $messages);
    }
}
