<?php

namespace App\Http\Requests\API;

use App\Models\UserDepartment;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Request\APIRequest;

class UpdateUserDepartmentAPIRequest extends APIRequest
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
            'department_name' => 'required|' .
            Rule::unique('user_departments')->ignore($this->department_id, 'department_id'),
        ];

        return $rules;
    }
}
