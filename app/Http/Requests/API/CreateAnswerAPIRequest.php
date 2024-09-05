<?php

namespace App\Http\Requests\API;

use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionOption;
use InfyOm\Generator\Request\APIRequest;

class CreateAnswerAPIRequest extends APIRequest
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

    public function validator($factory)
    {
        return $factory->make(
            $this->sanitize(),
            $this->container->call([$this, 'rules']),
            $this->messages()
        );
    }

    public function sanitize()
    {
        $this->merge([
            'answer_data' => json_decode($this->input('answer_data'), true)
        ]);
        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {       
        $questionIds = Question::where('survey_id', $this->survey_id)->pluck('question_id')->toArray();
        
        $optionIds = QuestionOption::whereIn('question_id', $questionIds)->pluck('option_id')->toArray();

        $rules = [
            'course_id' => 'required|exists:courses,course_id',
            'survey_id' => 'required|exists:surveys,survey_id',
            'answer_data' => 'required|array',
            'answer_data.answers' => 'required|array',
            'answer_data.answers.*.question_id' => 'required|numeric|in:' . implode(',', $questionIds),
            'answer_data.answers.*.option_id' => 'array',
            'answer_data.answers.*.option_id.*' => 'in:' . implode(',', $optionIds)
        ];

        return $rules;
    }
}
