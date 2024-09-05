<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'answer_id' => $this->answer_id,
            'question_id' => $this->question_id,
            'option_id' => $this->option_id,
            'answer_by' => $this->answer_by,
            'survey_id' => $this->survey_id,
            'percent_achieved' => $this->percent_achieved,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
