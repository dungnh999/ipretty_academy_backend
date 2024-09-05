<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LearningProcessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $chapter_name = "";

        if ($this->lesson_id) {
            $chapters = $this->lesson->chapters;
            if (count($chapters)) {
                $chapter_name = $chapters[0]->chapter_name;
            }
        }

        if ($this->survey_id) {
            $chapter_name = $this->survey->chapter->chapter_name;
        }

        $rework = "";
        if (isset($this->rework)) {
            $rework = $this->rework;
        }

        $user = auth()->user();
        $courseStudent = $user->courseStudentById($this->course_id);
        $isCompletedCourse = false;
        $isConfirmNotice = false;
        if ($courseStudent) {
            $isCompletedCourse = $courseStudent->isPassed;
            $isConfirmNotice = $courseStudent->isNoticed;
        }

        return [
            'process_id' => $this->process_id,
            'chapter_name' => $chapter_name,
            'lesson_id' => $this->lesson_id,
            'lesson_name' => $this->lesson_id ? $this->lesson->lesson_name : "",
            'survey_id' => $this->survey_id,
            'survey_title' => $this->survey_id ? $this->survey->survey_title : "",
            'course_id' => $this->course_id,
            'course_name' => $this->course->course_name,
            'student_id' => $this->student_id,
            'process' => $this->process,
            'isDraft' => $this->isDraft,
            'isPassed' => $this->isPassed,
            'rework' => $rework,
            'isCompletedCourse' => $isCompletedCourse,
            'isConfirmNotice' => $isConfirmNotice,
            'completed_at' => $this->completed_at ? $this->completed_at->format('Y-m-d H:i') : "",
            'started_at' => $this->started_at ? $this->started_at->format('Y-m-d H:i') : ""
        ];
    }
}
