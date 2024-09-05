<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseCategoryResource extends JsonResource
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
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'category_code' => $this->category_code,
            'isPublished' => $this->isPublished,
            'category_description' => $this->category_description,
            'category_type' => new CategoryTypeResource($this->categoryType),
            'course_category_attachment' => $this->course_category_attachment,
            'created_by' => new AuthorResource($this->createdBy),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}