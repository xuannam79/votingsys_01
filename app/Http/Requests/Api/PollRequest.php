<?php

namespace App\Http\Requests\Api;

class PollRequest extends AbstractRequest
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
            'multiple' => 'required|boolean',
            'date_close' => 'date_format:"d-m-Y H:i"|after:"' . date('d-m-Y H:i') . '"',
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
            'multiple.required' => $trans['type']['required'],
            'multiple.boolean' => $trans['type']['boolean'],
            'date_close.after' => trans('polls.message_client.time_close_poll'),
            'date_close.date_format' => $trans['date_close']['format'],
        ];
    }
}
