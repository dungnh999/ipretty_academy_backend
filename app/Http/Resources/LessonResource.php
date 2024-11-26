<?php

namespace App\Http\Resources;

use App\Models\ChapterLesson;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $author = User::find($this->lesson_author);
        $lesson_attachments = $this->getMedia(MEDIA_COLLECTION["LESSON_ATTACHMENT"]);

        if (count($lesson_attachments)) {
            foreach ($lesson_attachments as $key => $lesson_attachment) {
                $lesson_attachment->url = $lesson_attachment->getUrl();
            }
        }

        $main_attachment = $this->getFirstMedia(MEDIA_COLLECTION["LESSON_MAIN_ATTACHMENT"]);

        $main_attachment_name = "";

        if ($main_attachment) {
            $main_attachment_name = $main_attachment->file_name;
        }
        $number_order = 0;

        $chapter_lesson = ChapterLesson::where('lesson_id', $this->lesson_id)->first();

        if ($chapter_lesson) {
            $number_order = $chapter_lesson->number_order;
        }
        return [
            'lesson_id' => $this->lesson_id,
            'lesson_name' => $this->lesson_name,
            'number_order' => $number_order,
            'lesson_description' => $this->lesson_description,
            'lesson_content' => $this->lesson_content,
            'lesson_attachment' => $this->lesson_attachment,
            'lesson_attachments' => count($lesson_attachments) ? MediaResource::collection($lesson_attachments) : [],
            'lesson_author' => $this->lesson_author,
            'lesson_author_info' => new AuthorResource($author),
            // 'lesson_status' => $this->lesson_status,
            'main_attachment' => $this->main_attachment,
            'main_attachment_name' => $main_attachment_name,
            'is_demo' => $this->is_demo,
            'lesson_duration' => $this->lesson_duration,
            'created_at' => $this->created_at->format('Y-m-d H:i')
        ];
    }
}
