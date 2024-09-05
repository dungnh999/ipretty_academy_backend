<?php

namespace App\Http\Requests\API;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Request\APIRequest;

class CreateUserAPIRequest extends APIRequest
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

        $role = ['user', 'employee', 'admin'];

        $rules = [
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users',
            'birthday' => 'required|date',
            'name' => 'required|string|max:50',
            'code' => 'required|string|min:5|unique:users|regex:/^\S*$/',
            // 'department_id' => 'required|exists:user_departments,department_id',
            'phone' => 'required|string|regex:/(0)[0-9]/|between:10,11',
            'address' => 'required|string|min:2',
            // 'password' => 'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/',
            // 'confirmpassword' => 'same:password',
            'role' => "regex:/^(?'big'[a-z]+)(,(?&big))*$/|in:". implode(',', $role)
        ];

        if ($this->role != 'user') {
            $rules["department_id"] = 'required|exists:user_departments,department_id';
        }
        // var_dump(isset($this->confirmpassword));

        if (isset($this->password) && $this->password != null) {
            $rules['password'] = 'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';
        }

        if (!isset($this->confirmpassword) && isset($this->password) && $this->password != null ){
            $rules['confirmpassword'] = 'required';
        }else if (isset($this->confirmpassword) && $this->confirmpassword != null ){
            $rules['confirmpassword'] = 'same:password';
        }

        if (isset($this->birthday) && $this->birthday != 'null' ) {
            $now = date(Carbon::now());
            $rules["birthday"] = 'before:' . $now;
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
            'email.unique' => __('validation.unique_capitalize', ['model' => __('models/users.fields.emailUpper')]),
            'password.regex' => __('messages.invalid_format_pw'),
            'code.regex' => __('messages.invalid_format_code'),
            'role.regex' => __('validation.required', ['attribute' => __('models/users.fields.role')]),
            'role.in' => '',
            'department_id.exists' => '',
            'confirmpassword.same' => __('messages.same_password'),
            'phone.between' => __('validation.betweenPhone', ['attribute' => __('models/users.fields.phoneUp')]),
            // 'phone.min' => __('validation.min', ['attribute' => __('models/users.fields.phoneUp')]),
            'address.min' => __('validation.min', ['attribute' => __('models/users.fields.addressUp')]),
            'code.min' => __('validation.min', ['attribute' => __('models/users.fields.codeUp')]),
            'code.unique' => __('validation.unique', ['attribute' => __('models/users.fields.codeUp')]),
            'birthday.date' => __('validation.date', ['attribute' => __('models/users.fields.birth_day')]),
            'birthday.before' => __('validation.before_now', ['attribute' => __('models/users.fields.birth_day')]),
            'name.max' => __('validation.max', ['attribute' => __('models/users.fields.name')]),
        ];
    }
}
