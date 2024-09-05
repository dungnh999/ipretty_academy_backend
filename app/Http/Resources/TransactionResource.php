<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'transaction_id' => $this->transaction_id,
            'transaction_code' => $this->transaction_code,
            'payment_method' => $this->payment_method,
            'order_id' => $this->order_id,
            'order' => $this->order,
            'user_id' => $this->user_id,
            'buyer' => new AuthorResource($this->buyer),
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
