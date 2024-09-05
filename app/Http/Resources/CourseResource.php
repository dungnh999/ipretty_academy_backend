<?php

namespace App\Http\Resources;

use App\Models\Chapter;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $chapters = Chapter::where('course_id', $this->course_id)->with('lessons')->with('survey')->orderBy('number_order', 'asc')->get();
        $course_resources["chapters"] = ChapterResource::collection($chapters);
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
            'course_sale_price' => $this->course_sale_price,
            'course_target' => $this->course_target ? json_decode($this->course_target) : "",
            'category_id' => $this->category_id,
            'category' => $this->category->category_name,
            'is_published' => $this->is_published,
            'isDraft' => $this->isDraft,
            'unit_currency' => $this->unit_currency,
            'course_type' => $this->course_type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'course_resources' => $course_resources,
        ];

        if (!empty($this->course_price)) {
            $response["course_price"] = $this->course_price;
        }

        if (!empty($this->course_sale_price)) {
            $response["course_sale_price"] = $this->course_sale_price;
        }

        if (!empty($this->deadline)) {
            $response["deadline"] = $this->deadline->format('Y-m-d H:i:s');
        }

        if (!empty($this->startTime)) {
            $response["startTime"] = $this->startTime->format('Y-m-d H:i:s');
        }

        if (!empty($this->endTime)) {
            $response["endTime"] = $this->endTime->format('Y-m-d H:i:s');
        }

        // if (!empty($this->leader_id)) {
        //     $response["leader_id"] = $this->leader_id;
        //     $response["leader"] = AuthorResource::collection($this->leaders);
        // }

        if ($this->course_type == "Group") {

            $response["leader"] = AuthorResource::collection($this->leaders);

            $response["students"] = AuthorResource::collection($this->students);

        }

        return $response;
    }
}
