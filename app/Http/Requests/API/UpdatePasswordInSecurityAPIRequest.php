<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordInSecurityAPIRequest extends FormRequest
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
            'current_password' => 'required',
            'new_password' => 'required|'
        ];


        if (isset($this->new_password) && $this->new_password != null) {
            $rules['new_password'] = 'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';
        }

        if (isset($this->confirm_password) && $this->confirm_password != null) {
            $rules['confirm_password'] = 'same:new_password';
        }

        if (!isset($this->confirm_password) && isset($this->new_password) && $this->new_password != null) {
            $rules['confirm_password'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'confirm_password.same' =>  __('messages.same_password'),
            'new_password.regex' => __('messages.invalid_format_pw'),
        ];
    }
}
