<?php

namespace App\Http\Requests\API;

use App\Models\Survey;
use InfyOm\Generator\Request\APIRequest;

class UpdateSurveyAPIRequest extends APIRequest
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
            'questions_data' => json_decode($this->input('questions_data'), true)
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
        $rules = [
            'survey_title' => 'required|min:5',
            // 'survey_duration' => 'numeric|min:0',
            'percent_to_pass' => 'integer|between:0,100',
            // 'question_per_page' => 'numeric|min:0',
        ];

        if (
            (isset($this->questions_data) && count($this->questions_data) && !$this->questions_data["questions"]) ||
            (isset($this->questions_data["questions"]) && !count($this->questions_data["questions"]))
        ) {
            $rules["questions_data.questions"] = 'required';
        }

        if (count($this->questions_data["questions"])) {

            foreach ($this->questions_data["questions"] as $key => $question) {
                if (!$question["question_title"]) {
                    $rules['questions_data.questions.' . $key . '.question_title'] = 'required';
                }

                if (count($question["options"])) {
                    foreach ($question["options"] as $keyo => $option) {
                        # code...
                        if (!$option["option_body"]) {
                            $rules['questions_data.questions.' . $key . '.options.' . $keyo . '.option_body'] = 'required';
                        }
                    }
                }
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'survey_title.min' => __('validation.min', ['attribute' => __('models/surveys.fields.survey_titleUp')]),
            'percent_to_pass.between' => __('validation.between', ['attribute' => __('models/surveys.fields.percent_to_passUp')]),
            'percent_to_pass.integer' => __('validation.integer', ['attribute' => __('models/surveys.fields.percent_to_passUp')]),
            'questions_data.questions.required' => __('validation.required', ['attribute' => __('models/surveys.fields.questionsSurvey')]),
            'questions_data.questions.*.question_title.required' => __('validation.required', ['attribute' => __('models/surveys.fields.question_title')]),
            'questions_data.questions.*.options.*.option_body.required' => __('validation.required', ['attribute' => __('models/surveys.fields.option_body')]),
        ];
    }
}
