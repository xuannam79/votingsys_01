<?php

namespace App\Http\Requests\Api;

class ResetPasswordRequest extends AbstractRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password' => 'required|min:6',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $trans = trans('passwords.validate');

        return [
            'old_password.required' => $trans['old_password']['required'],
            'old_password.min' => $trans['old_password']['min'],
            'password.required' => $trans['password']['required'],
            'password.min' => $trans['password']['min'],
            'password.confirmed' => $trans['password']['confirmed'],
            'password_confirmation.required' => $trans['password_confirmation']['required'],
            'password_confirmation.min' => $trans['password_confirmation']['min'],
        ];
    }
}
