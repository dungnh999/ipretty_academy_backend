<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostCategoryAPIRequest extends FormRequest
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
            'category_name' => 'required|unique:post_categories'
        ];
    }

    public function messages()
    {
        return [
            'category_name.unique' => __('validation.unique', ['attribute' => __('models/posts.fields.category_name_up')]),
        ];
    }
}
