<?php

namespace App\Http\Requests\API;

use App\Models\Cart;
use App\Models\Course;
use InfyOm\Generator\Request\APIRequest;

class CreateCartAPIRequest extends APIRequest
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
        $courses = Course::select('course_id')->businessCourses()->get()->pluck('course_id')->toArray();

        $rules = [
            'course_id' => 'required|in:'. implode(',',$courses)
        ];
        return $rules;
    }
}
