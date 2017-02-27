<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\AbstractRequest;

class SocialRequest extends AbstractRequest
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
           'user_id' => 'required|numeric',
           'provider_user_id' => 'required|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $trans = trans('social.validate');

        return [
            'user_id.required' => $trans['user_id']['required'],
            'user_id.numeric' => $trans['user_id']['numeric'],
            'provider_user_id.required' => $trans['provider_user_id']['required'],
            'provider_user_id.max' => $trans['provider_user_id']['max'],
        ];
    }
}
