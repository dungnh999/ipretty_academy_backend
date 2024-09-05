<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FAQQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $likes_count = $this->likes()->count();
        $dislikes_count = $this->dislikes()->count();
        $comments = $this->comments;

        return [
            'question_id' => $this->question_id,
            'question_name' => $this->question_name,
            'answer_name' => $this->answer_name,
            'number_order' => $this->number_order,
            'faq_id' => $this->faq_id,
            'likes_count' => $likes_count,
            'dislikes_count' => $dislikes_count,
            'comments' => $comments,
        ];
    }
}
