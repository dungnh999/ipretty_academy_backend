<?php

namespace App\Http\Requests\API;

use App\Models\LearningProcess;
use InfyOm\Generator\Request\APIRequest;

class FinishLessonAPIRequest extends APIRequest
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
        $user = auth()->user();

        $course_students = LearningProcess::where('student_id', $user->id);

        $course_ids = $course_students->pluck('course_id')->toArray();

        $lesson_ids = $course_students->pluck('lesson_id')->toArray();

        $rules = [
            'course_id' => 'required|exists:courses,course_id|exists:learning_processes,course_id|in:' . implode(',', $course_ids),
            'lesson_id' => 'required|exists:lessons,lesson_id|exists:learning_processes,lesson_id|in:' . implode(',', array_filter($lesson_ids)),
            'view_duration' => 'required|numeric',
        ];

        return $rules;
    }
}
