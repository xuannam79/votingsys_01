<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
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
        $length = config('settings.length_user');

        return [
            'name' => 'required|max:' . $length['name'],
            'email' => 'required|email|max:' . $length['email'],
            'feedback' => 'required|max:' . $length['feedback'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $trans = trans('user.validate');

        return [
            'name.required' => $trans['name']['required'],
            'name.max' => $trans['name']['max'],
            'email.required' => $trans['email']['required'],
            'email.email' => $trans['email']['email'],
            'email.max' => $trans['email']['max'],
            'feedback.required' => $trans['feedback']['required'],
            'feedback.max' => $trans['feedback']['max'],
        ];
    }
}
