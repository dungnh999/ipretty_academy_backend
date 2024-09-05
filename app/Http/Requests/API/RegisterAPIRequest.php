<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;

class RegisterAPIRequest extends APIRequest
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
            // 'name' => 'required',
            'email' => 'required|unique:users|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'password' => 'required|min:6|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/',
            'confirmpassword' => 'required|same:password|min:6'
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
            'name.required' =>  __('messages.name_required'),
            'email.required' =>  __('messages.email_required'),
            'email.unique' =>  __('messages.email_unique'),
            'password.required' =>  __('messages.password_required'),
            'password.regex' => __('messages.invalid_format_pw'),
            'password.min' =>  __('messages.password_min'),
            'confirmpassword.required' =>  __('messages.confirmpassword_required'),
            'confirmpassword.same' =>  __('messages.confirmpassword_same'),
        ];
    }
}
