<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessagesResource extends JsonResource
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
            'sender_id' => $this->sender_id,
            'body' => $this->body,
            'type' => $this->type,
            'is_attachment' => $this->is_attachment,
            'receiver_id' => $this->receiver_id,
            'receiver_seen' => $this->receiver_seen,
            'created_at' =>  $this->created_at->format('H:i'),
            'updated_at' => $this->updated_at
        ];
    }
}
