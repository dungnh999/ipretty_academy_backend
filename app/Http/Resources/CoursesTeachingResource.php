<?php

namespace App\Http\Resources;

use App\Models\CourseStudent;
use Illuminate\Http\Resources\Json\JsonResource;

class CoursesTeachingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        // dd($this);
        $scoreRating = CourseStudent::whereNotNull('rating')->where('course_id', $this->course_id)->selectRaw("AVG(rating) as scoreRating")->first();

        return [
            'course_name' => $this->course_name,
            'course_feature_image' => $this->course_feature_image,
            'teacher' => new AuthorResource($this->teacher),
            'course_price' => $this->course_price,
            'scoreRating' => $scoreRating->scoreRating,
        ];
    }
}
