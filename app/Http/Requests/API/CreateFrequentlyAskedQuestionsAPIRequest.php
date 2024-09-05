<?php

namespace App\Http\Requests\API;

use App\Models\FrequentlyAskedQuestions;
use InfyOm\Generator\Request\APIRequest;

class CreateFrequentlyAskedQuestionsAPIRequest extends APIRequest
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
            'faqs_resources' => json_decode($this->input('faqs_resources'), true)
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
            'title' => 'required|min:5|unique:frequently_asked_questions',
        ];
        // dd(count($this->faqs_resources['questions']));
        if (count($this->faqs_resources['questions']) == 0) {
            $rules['questions'] = 'required';
        } else {
            foreach ($this->faqs_resources['questions'] as $key => $question) {
                
                // dd(ctype_space($question["question_name"]));

                if (!$question["question_name"] || $question["question_name"] == '' || ctype_space($question["question_name"])) {

                    $rules['faqs_resources.questions.' . $key . '.question_name'] = 'required';

                }

                if (!$question["answer_name"] || $question["answer_name"] == '' || ctype_space($question["answer_name"])) {

                    $rules['faqs_resources.questions.' . $key . '.answer_name'] = 'required';
                    
                }
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => __('validation.required', ['attribute' => __('models/frequentlyAskedQuestions.fields.titleUp')]),
            'title.unique' => __('validation.unique', ['attribute' => __('models/frequentlyAskedQuestions.fields.title')]),
            'title.min' => __('validation.min', ['attribute' => __('models/frequentlyAskedQuestions.fields.title')]),
            'questions.required' => __('messages.questions_length_faq'),
            'faqs_resources.questions.*.question_name.required' => __('validation.required', ['attribute' => __('models/frequentlyAskedQuestions.fields.question_name')]),
            'faqs_resources.questions.*.answer_name.required' => __('validation.required', ['attribute' => __('models/frequentlyAskedQuestions.fields.answer_name')])
        ];
    }
}
