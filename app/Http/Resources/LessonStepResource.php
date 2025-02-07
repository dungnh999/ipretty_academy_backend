<?php

namespace App\Http\Resources;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseLessonChapter;
use App\Models\Lesson;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonStepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $chapter = Chapter::where('chapter_id', $this->chapter_id)->first();
        $lesson = Lesson::where('lesson_id', $this->lesson_id)->first();
        $course = Course::where('course_id', $this->course_id)->first();

        $chapter_lesson = CourseLessonChapter::where('uuid', $this->uuid)->first();
        $chapter_lesson["chapter"] = $chapter;
        $chapter_lesson["lesson"] = $lesson ;
        return [
            'continue_id' => "hMDhiNjAzZGMtNWIxOS00ZGM5LWJhYTItOTdiNjdhM2ExZjk5M",
            'previous_id' => "UODU0YTI0ZWQtZmJjYy00ZWI4LThmN2EtOGU5YTJmNTBlOTRhQ",
            'is_completed' => "",
            'course' => $course,
            'course_progress' => 19,
            'has_paid' => true,
            'is_completable' => true,
            'learning_log' => [],
            'chapter_lesson' => $chapter_lesson
        ];
    }
}
//[
////                'chapter' => Chapter::where('chapter_id', $this->chapter_id)->get(),
////                'lesson' => Lesson::where('lesson_id', $this->lesson_id)->get(),
//            ]