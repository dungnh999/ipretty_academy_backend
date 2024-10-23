<?php

namespace App\Http\Resources;

use App\Models\Chapter;
use App\Models\CourseStudent;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseShortTermForLandingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $chapters = Chapter::where('course_id', $this->course_id)->with('lessons')->with('survey')->get();
        $course_resources["chapters"] = ChapterShortTermResource::collection($chapters);

        $students = $this->students;

        $scoreRating = CourseStudent::whereNotNull('rating')->where('course_id', $this->course_id)->selectRaw("sum(rating) as scoreRating, count(case when rating > 0 then 1 else null end) as rating_round ")->first();

        $response =
        [
            'course_id' => $this->course_id,
            'course_name' => $this->course_name,
            'course_created_by' => $this->course_created_by,
            'created_by' => new AuthorResource($this->createdBy),
            'teacher_id' => $this->teacher_id,
            'teacher' => new AuthorResource($this->teacher),
            'course_feature_image' => $this->course_feature_image,
            'certificate_image' => $this->certificate_image,
            'course_description' => $this->course_description,
            'course_target' => $this->course_target ? json_decode($this->course_target) : "",
            'category_id' => $this->category_id,
            'category' => $this->category->category_name,
            'isDraft' => $this->isDraft,
            'is_published' => $this->is_published,
            'course_type' => $this->course_type,
            'unit_currency' => $this->unit_currency,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
            'course_resources' => $course_resources,
            'number_of_students' => count($students),
            'scoreRating' => $scoreRating->scoreRating,
            'rating_round' => $scoreRating->rating_round,
        ];

        $response["course_price"] = $this->course_price;
        $response["course_sale_price"] = $this->course_sale_price;



        if (!empty($this->deadline)) {
            $response["deadline"] = $this->deadline->format('Y-m-d H:i');
        }

        if (!empty($this->startTime)) {
            $response["startTime"] = $this->startTime->format('Y-m-d H:i');
        }

        if (!empty($this->endTime)) {
            $response["endTime"] = $this->endTime->format('Y-m-d H:i');
        }

        return $response;
    }
}
