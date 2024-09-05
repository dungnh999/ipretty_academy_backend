<?php

namespace App\Http\Requests\API;

use App\Models\CourseCategory;
use InfyOm\Generator\Request\APIRequest;
use Illuminate\Validation\Rule;

class UpdateCourseCategoryAPIRequest extends APIRequest
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
        $categoryId = $this->route()->parameter('course_category');

        $rules = [
            'category_name' => 'required|min:6|'.
            Rule::unique('course_categories')->ignore($this->category_id, 'category_id'),
        ];


        if (isset($this->course_category_attachment)) {
            $rules["course_category_attachment"] = 'mimes:jpeg,jpg,png|max:10240';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'course_category_attachment.mimes' => __('validation.mimes', ['attribute' => __('models/courses.fields.course_category_attachmentUp')]),
        ];
    }
}
