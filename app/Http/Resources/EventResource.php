<?php

namespace App\Http\Resources;

use App\Models\Course;
use Illuminate\Http\Resources\Json\JsonResource;

class


EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $course = Course::where('course_id', $this->course_id)->first();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'course_id' => $this->course_id ? $course : null,
            'time_start' => $this->time_start,
            'time_end' => $this->time_end,
            'status_reminder' => $this->status_reminder,
            'color' => $this->color,
            'create_by' => $this->create_by,
            'distance_time_reminder' => $this->distance_time_reminder,
            'distance_time_reminder_2' => $this->distance_time_reminder_2,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
