<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PollRequest extends FormRequest
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
            'name' => 'required|max:' . $config['name'],
            'email' => 'required|email|max:' . $config['email'],
            'title' => 'required|max:' . $config['title'],
            'type' => 'required',
            'closingTime' => 'info',
            'optionText' => 'option:optionImage',
            'value' => 'setting:setting',
            'member' => 'participant:participant',
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
            'name.required' => $trans['name']['required'],
            'name.max' => $trans['name']['max'],
            'email.required' => $trans['email']['required'],
            'email.email' => $trans['email']['email'],
            'email.max' => $trans['email']['max'],
            'title.required' => $trans['title']['required'],
            'title.max' => $trans['title']['max'],
            'type.required' => $trans['type']['required'],
            'optionText.option' => $trans['option']['option'],
            'value.setting' => $trans['setting']['setting'],
            'member.participant' => $trans['participant']['email'],
        ];
    }
}
