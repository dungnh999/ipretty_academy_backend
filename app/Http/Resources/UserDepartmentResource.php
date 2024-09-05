<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDepartmentResource extends JsonResource
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
            'department_id' => $this->department_id,
            'department_name' => $this->department_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
