<?php

namespace App\Http\Resources;

use App\Models\Chapter;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyShortTermResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth()->user();
        $chapter = Chapter::where('survey_id', $this->survey_id)->first();
        $learningProcess = "";
        $learningProcess = $user ? $this->learningProcess($user->id, $this->survey_id) : null;
        if ($learningProcess) {
            $learningProcess["rework"] = false;
            if ($learningProcess->completed_at && $learningProcess->completed_at < Carbon::now()->subDays(1)) {
                $learningProcess["rework"] = true;
            }
        }


        return [
            'survey_id' => $this->survey_id,
            'survey_title' => $this->survey_title,
            'survey_description' => $this->survey_description,
            'course_id' => $chapter ? $chapter->course_id : null,
            // 'survey_duration' => $this->survey_duration,
            'percent_to_pass' => $this->percent_to_pass,
            'question_per_page' => $this->question_per_page,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'learningProcess' => $learningProcess,
        ];
    }
}
