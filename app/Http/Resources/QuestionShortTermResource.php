<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionShortTermResource extends JsonResource
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
        $user_id = $user->id;
        if ($this->user_id) {
            $user_id = $this->user_id;
        }
        // $question_attachments = $this->getMedia(MEDIA_COLLECTION["QUESTION_ATTACHMENTS"]);

        // if (count($question_attachments)) {
        //     foreach ($question_attachments as $key => $question_attachment) {
        //         $question_attachment->url = $question_attachment->getUrl();
        //     }
        // }
        
        $answer = $this->answerBy($user_id, $this->survey_id);

        return [
            'question_id' => $this->question_id,
            'question_title' => $this->question_title,
            'question_description' => $this->question_description,
            'question_type' => $this->question_type,
            'number_order' => $this->number_order,
            'question_attachments' => $this->question_attachments,
            'has_attachment' => $this->has_attachment,
            'survey_id' => $this->survey_id,
            'percent_achieved' => $this->percent_achieved,
            'answer' => $answer,
            'options' => QuestionOptionResource::collection($this->options)
        ];
    }
}
