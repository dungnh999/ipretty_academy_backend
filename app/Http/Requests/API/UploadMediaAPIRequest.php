<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;

class UploadMediaAPIRequest extends APIRequest
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
            'image_attachment' => 'required|mimes:jpeg,png,jpg|max:512000|min:1',
        ];
    }

    public function messages()
    {
        return [
            'image_attachment.required' =>  __('auth.image_attachment.required'),
            'image_attachment.max' =>  __('auth.image_attachment.max'),
            'image_attachment.min' =>  __('auth.image_attachment.min'),
            'image_attachment.mimes' =>  __('auth.image_attachment.mimes')
        ];
    }
}
