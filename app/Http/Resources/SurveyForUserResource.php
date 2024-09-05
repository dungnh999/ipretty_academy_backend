<?php

namespace App\Http\Resources;

use App\Models\Chapter;
use App\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyForUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $questions = Question::where('survey_id', $this->survey_id)->with('options')->orderBy('number_order', 'asc')->get();
        foreach ($questions as $key => $question) {
            $question->user_id = $this->user_id;
        }
        $questions_data["questions"] = QuestionShortTermResource::collection($questions);

        $chapter = Chapter::where('survey_id', $this->survey_id)->first();
        
        return [
            'survey_id' => $this->survey_id,
            'survey_title' => $this->survey_title,
            'survey_description' => $this->survey_description,
            'course_id' => $chapter ? $chapter->course_id : null,
            // 'survey_duration' => $this->survey_duration,
            'percent_to_pass' => $this->percent_to_pass,
            'question_per_page' => $this->question_per_page,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'questions_data' => $questions_data
        ];
    }
}
