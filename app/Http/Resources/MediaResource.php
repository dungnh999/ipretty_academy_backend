<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'id' => $this->id,
            'collection_name' => $this->collection_name,
            'name' => $this->name,
            'file_name' => $this->file_name,
            'size' => $this->size,
            'url' => $this->url,
            'uuid' => $this->uuid,
            'created_at' => $this->created_at->format('Y-m-d H:i')
        ];
    }
}
