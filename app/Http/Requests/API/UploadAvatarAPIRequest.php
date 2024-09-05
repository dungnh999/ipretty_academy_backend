<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;

class UploadAvatarAPIRequest extends APIRequest
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
            'avatar' => 'required|mimes:jpeg,png,jpg,gif,svg|max:10240|min:1',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'avatar.required' =>  __('auth.avatar.required'),
            'avatar.max' =>  __('auth.avatar.max'),
            'avatar.min' =>  __('auth.avatar.min'),
            'avatar.mimes' =>  __('auth.avatar.mimes')
        ];
    }
}
