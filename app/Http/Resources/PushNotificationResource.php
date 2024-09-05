<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PushNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $group_receivers = $this->group_receivers;

        // $notification_cat = ['AD', 'DOC', 'FUNC', 'HOL', 'POL'];

        // if (in_array($this->notification_cat, $notification_cat)) {
        //     $group_receivers = "";
        // }

        return [
            'notification_id' => $this->notification_id,
            'notification_title' => $this->notification_title,
            'isPublished' => $this->isPublished,
            'created_by' => $this->created_by,
            'createdBy' => new AuthorResource($this->createdBy),
            'notification_cat' => $this->notification_cat ? $this->notification_cat : "",
            'group_receivers' => $group_receivers,
            'notification_message' => $this->notification_message,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i')
        ];
    }
}
