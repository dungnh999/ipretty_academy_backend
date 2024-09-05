<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportContactResource extends JsonResource
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
            'report_id' => $this->report_id,
            'report_content' => $this->report_content,
            'reporter_name' => $this->reporter_name,
            'reporter_email' => $this->reporter_email,
            'reporter_phone' => $this->reporter_phone,
            'attachments' => $this->attachments,
            'reporter_id' => $this->reporter_id,
            'isReport' => $this->isReport,
            'isSended' => $this->isSended,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i')
        ];
    }
}
