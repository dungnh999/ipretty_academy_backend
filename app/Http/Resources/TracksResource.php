<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TracksResource extends JsonResource
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
            'completed' => [],
            'has_paid' => false,
            'is_registered' => true,
            'remaining' => '',
            'track_steps_count' => '',
            'tracks' => $this->chapters,
        ];
    }
}
