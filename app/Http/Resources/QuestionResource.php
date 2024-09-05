<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        // $question_attachments = $this->getMedia(MEDIA_COLLECTION["QUESTION_ATTACHMENTS"]);

        // if (count($question_attachments)) {
        //     foreach ($question_attachments as $key => $question_attachment) {
        //         $question_attachment->url = $question_attachment->getUrl();
        //     }
        // }
        
        return [
            'question_id' => $this->question_id,
            'question_title' => $this->question_title,
            'question_description' => $this->question_description,
            'question_type' => $this->question_type,
            'number_order' => $this->number_order,
            'question_attachments' => $this->question_attachments,
            // 'question_attachments' => count($question_attachments) ? MediaResource::collection($question_attachments) : [],
            'has_attachment' => $this->has_attachment,
            'session_id' => $this->session_id,
            'percent_achieved' => $this->percent_achieved,
            'options' => QuestionOptionResource::collection($this->options)
        ];
    }
}
