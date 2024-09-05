<?php

namespace App\Http\Requests\API;

use App\Models\CourseCategory;
use InfyOm\Generator\Request\APIRequest;

class CreateCourseCategoryAPIRequest extends APIRequest
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
            'category_name' => 'required|min:6|unique:course_categories',
            'course_category_attachment' => 'required|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'course_category_attachment.mimes' => __('validation.mimes', ['attribute' => __('models/courses.fields.course_category_attachmentUp')]),
        ];
    }
}
