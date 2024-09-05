<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        // dd($this->courses);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'cart_token' => $this->cart_token,
            'status' => $this->status,
            'cartItems' => $this->cartItemsWithCourses,
        ];
    }
}
