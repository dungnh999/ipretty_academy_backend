<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;

class InviteUserAPIRequest extends APIRequest
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
        $rules = [
            'email' => 'required',
        ];

        if (isset($this->email) && $this->email != NULL) {
            $rules["email"] = "regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users";
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' =>  __('messages.email_required'),
            'email.unique' =>  __('messages.email_unique')
        ];
    }
}
