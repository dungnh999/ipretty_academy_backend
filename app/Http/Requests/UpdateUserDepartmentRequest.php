<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserDepartment;
use Illuminate\Validation\Rule;

class UpdateUserDepartmentRequest extends FormRequest
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
            'department_name' => 'required|'.
                Rule::unique('user_departments')->ignore($this->department_id, 'department_id'),
        ];

        return $rules;
    }
}
