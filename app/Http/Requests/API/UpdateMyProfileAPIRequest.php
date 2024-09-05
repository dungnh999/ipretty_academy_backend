<?php

namespace App\Http\Requests\API;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Request\APIRequest;

class UpdateMyProfileAPIRequest extends APIRequest
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
        $userId = auth()->user()->id;
        
        if ($this->route()->parameter('id') != null) {
            $userId = $this->route()->parameter('id');
        }

        $rules = [
            'name' => 'required|string|max:50',
            'lang' => [Rule::in('vi', 'en')],
            'gender' => 'required',
            'gender' => [Rule::in('Male', 'Female', 'Other')],
            'phone' => 'required|regex:/(0)[0-9]/|string|between:10,11',
            'address' => 'required|string|min:2',
            'code' => 'string|min:5|regex:/^\S*$/|'.
                Rule::unique('users')->ignore($userId, 'id'),
            'birthday' => 'required|date',
        ];

        if (isset($this->birthday) && $this->birthday != 'null') {
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
            'password.regex' => __('messages.invalid_format_pw'),
            'code.regex' => __('messages.invalid_format_code'),
            'role.regex' => __('validation.required', ['attribute' => __('models/users.fields.role')]),
            'role.in' => '',
            'department_id.exists' => '',
            'confirmpassword.same' => __('messages.same_password'),
            'phone.between' => __('validation.betweenPhone', ['attribute' => __('models/users.fields.phoneUp')]),
            'phone.numeric' => __('validation.numeric', ['attribute' => __('models/users.fields.phone')]),
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
