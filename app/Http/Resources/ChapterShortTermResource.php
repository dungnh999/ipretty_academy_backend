<?php

namespace App\Http\Resources;

use App\Models\Lesson;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterShortTermResource extends JsonResource
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
            'chapter_id' => $this->chapter_id,
            'number_order' => $this->number_order,
            'chapter_name' => $this->chapter_name,
            'course_id' => $this->course_id,
            'lessons' => LessonShortTermResource::collection($this->lessons, $this->chapter_id),
            'survey' => new SurveyShortTermResource($this->survey),
            'number_lesson' => count($this->lessons)
        ];
    }
}