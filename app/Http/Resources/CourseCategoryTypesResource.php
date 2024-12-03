<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseCategoryTypesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
      return [
        // 'category_type_id' => $this->id,
        'category_type_name' => $this->category_type_name,
        'isPublished' => $this->isPublished,
        'category_type_description' => $this->category_type_description,
        'created_by' => new AuthorResource($this->createdBy),
        'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
      ];
    }
}
