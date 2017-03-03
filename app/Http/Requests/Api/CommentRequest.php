<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends AbstractRequest
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
            'content' => 'required|max:' . $config['content'],
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
            'name.required' => trans('polls.comment_name'),
            'name.max' => $trans['name']['max'],
            'content.required' => trans('polls.comment_content'),
            'content.max' => $trans['content']['max'],
        ];
    }

}
