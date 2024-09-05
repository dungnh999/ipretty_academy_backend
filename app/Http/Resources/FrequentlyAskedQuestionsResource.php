<?php

namespace App\Http\Resources;

use App\Models\FAQQuestion;
use Illuminate\Http\Resources\Json\JsonResource;

class FrequentlyAskedQuestionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request, $id = null)
    {
        $questions = FAQQuestion::where('faq_id', $this->id)->get();
        $faq_question["questions"] = FAQQuestionResource::collection($questions);
        // dd($this);
        // dd($this->updated_at);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'created_by' => new AuthorResource($this->createdBy),
            'questions' => $faq_question,
            'created_at' => $this->created_at,
            'updated_at' => $this->id != null ? $this->updated_at : '',
            'isPublished' => $this->isPublished,
            // 'count_like' => isset($this->count_like) ? $this->count_like : 0,
            // 'count_dislike' =>  isset($this->count_dislike) ? $this->count_dislike : 0,
            // 'listComments' =>  isset($this->listComments) ? $this->listComments : "",
        ];
    }
}
