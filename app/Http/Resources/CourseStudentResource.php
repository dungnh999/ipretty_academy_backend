<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseStudentResource extends JsonResource
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
            'id' => $this->id,
            'course_id' => $this->course_id,
            'course' => $this->course,
            'student_id' => $this->student_id,
            'student' => $this->student,
            'percent_finish' => $this->percent_finish,
            'isPassed' => $this->isPassed,
            'created_at' => $this->created_at,
            'rating' => $this->created_at,
            'comment' => $this->created_at,
        ];
    }
}
