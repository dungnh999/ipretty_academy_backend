<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostCategoryResource extends JsonResource
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
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'category_slug' => $this->category_slug,
            'description' => $this->description ? $this->description : "",
            'isPublished' => $this->isPublished,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i') : "",
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : ""
        ];
    }
}
