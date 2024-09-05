<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        // $option_attachments = $this->getMedia(MEDIA_COLLECTION["OPTION_ATTACHMENTS"]);

        // if (count($option_attachments)) {
        //     foreach ($option_attachments as $key => $option_attachment) {
        //         $option_attachment->url = $option_attachment->getUrl();
        //     }
        // }


        return [
            'option_id' => $this->option_id,
            'question_id' => $this->question_id,
            'option_body' => $this->option_body,
            'right_answer' => $this->right_answer,
            'option_attachmant_name' => $this->	option_attachmant_name,
            'option_attachments' => $this->option_attachments,
            'is_image' => $this->is_image
        ];
    }
}
