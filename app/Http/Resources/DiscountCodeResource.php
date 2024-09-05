<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountCodeResource extends JsonResource
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
            'discount_code' => $this->discount_code,
            'title' => $this->title,
            'sale_price' => $this->sale_price,
            'type' => $this->type,
            'created_by' => $this->created_by,
            'time_start' => $this->time_start->format('Y-m-d H:i:s'),
            'expired_at' => $this->expired_at->format('Y-m-d H:i:s'),
            'count' => $this->count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
