<?php

namespace App\Http\Requests\API;

use App\Models\Lesson;
use InfyOm\Generator\Request\APIRequest;

class UpdateLessonAPIRequest extends APIRequest
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
        $max_size = env('MAX_SIZE_FILE_UPLOAD', 512000);

        $rules = [
            'lesson_name' => 'required',
            'lesson_description' => 'required',
            'lesson_content' => 'required',
            // 'main_attachment' => 'mimes:mp4,wmv,avi|max:'. $max_size,
            'main_attachment' => 'max:'. $max_size,
            // 'lesson_attachment.*' => 'mimetypes:application/pdf,application/ppt,application/pptx,application/doc,application/docx,application/msword,' .
            // 'application/octet-stream,' .
            // 'application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:' . $max_size,
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'main_attachment.max' => __('validation.max', ['attribute' => __('models/lessons.fields.main_attachment_lowercase')]),
            'lesson_attachment.max' => __('validation.max', ['attribute' => __('models/lessons.fields.lesson_attachment_lowercase')]),
        ];
    }
}
