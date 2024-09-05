<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
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
            'session_id' => $this->session_id,
            'session_name' => $this->session_name,
            'survey_id' => $this->survey_id,
            'questions' => QuestionResource::collection($this->questions)
        ];
    }
}
