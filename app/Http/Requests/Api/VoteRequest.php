<?php

namespace App\Http\Requests\Api;

class VoteRequest extends AbstractRequest
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
        $config = config('settings.length_poll');

        return [
            'name' => 'max:' . $config['name'],
            'email' => 'email|max:' . $config['email'],
            'optionImage.*' => 'image|mimes:jpg,jpeg,png,gif,svg|max:1000',
        ];
    }

     /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $trans = trans('polls.validation');
        return [
            'name.max' => $trans['name']['max'],
            'email.email' => $trans['email']['email'],
            'email.max' => $trans['email']['max'],
        ];
    }
}
