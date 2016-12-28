<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $user = auth()->user();

        return [
            'name' => 'required|max:255',
            'password' => 'confirmed',
            'avatar' => 'image|mimes:jpg,jpeg,png,gif,svg|max:1000',
            'email' => [
                'required',
                'max:255',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
        ];
    }
}
