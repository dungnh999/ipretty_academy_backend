<?php

namespace App\Http\Requests\API;

use App\Models\CourseStudent;
use InfyOm\Generator\Request\APIRequest;

class CreateCourseStudentAPIRequest extends APIRequest
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
            'course_id' => 'required|numeric|exists:courses'
        ];

        return $rules;
    }
}
