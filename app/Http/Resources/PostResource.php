<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'post_id' => $this->post_id,
            'title' => $this->title ? $this->title : "",
            'content' => $this->content ? $this->content : "",
            'description' => $this->description ? $this->description : "",
            'bannerUrl' => $this->bannerUrl,
            'color_introduction' => $this->color_introduction ? $this->color_introduction : "",
            'introduction' => $this->introduction ? $this->introduction : "",
            'sub_introduction' => $this->sub_introduction ? $this->sub_introduction : "",
            'color_content' => $this->color_content ? $this->color_content : "",
            'color_title' => $this->color_title ? $this->color_title : "",
            'color_button' => $this->color_button ? $this->color_button : "",
            'bg_color_button' => $this->bg_color_button ? $this->bg_color_button : "",
            // 'external_url' => $this->external_url,
            'slug' => $this->slug,
            'created_by' => $this->created_by,
            'category_id' => $this->category_id ? $this->category_id : "" ,
            'category' => new PostCategoryResource($this->postCategory) ,
            'is_active' => $this->is_active,
            'is_banner' => $this->is_banner,
            'isTrademark' => $this->isTrademark,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i') : "",
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : ""
        ];
    }
}
