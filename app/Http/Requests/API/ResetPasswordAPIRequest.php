<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordAPIRequest extends FormRequest
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
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'password' => 'required',
            'token' => 'required|string'
        ];

        if (isset($this->password) && $this->password != null) {
            $rules['password'] = 'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';
        }

        if (isset($this->confirm_password) && $this->confirm_password != null) {
            $rules['confirm_password'] = 'same:password';
        }

        if (!isset($this->confirm_password) && isset($this->password) && $this->password != null) {
            $rules['confirm_password'] = 'required';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'token.required' =>  __('messages.token_is_required'),
            'confirm_password.same' =>  __('messages.same_password'),
            'password.regex' => __('messages.invalid_format_pw'),
        ];
    }
}
