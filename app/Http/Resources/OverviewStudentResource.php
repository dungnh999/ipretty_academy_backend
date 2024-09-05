<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OverviewStudentResource extends JsonResource
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
            'courses_count' => $this->courses_count,
            'courses_learning_count' => $this->courses_learning_count,
            'courses_completed_count' => $this->courses_completed_count,
            'courses_learning_not_start_count' => $this->courses_learning_not_start_count,
            'my_exam_count' => $this->my_exam_count,
            'exam_passed_count' => $this->exam_passed_count,
            'exam_fail_count' => $this->exam_fail_count,
            'exam_doing_and_pending_count' => $this->exam_doing_and_pending_count,
        ];
    }
}
