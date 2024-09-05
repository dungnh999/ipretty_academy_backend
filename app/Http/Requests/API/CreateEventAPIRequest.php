<?php

namespace App\Http\Requests\API;

use App\Models\Event;
use InfyOm\Generator\Request\APIRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CreateEventAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'title' => 'required',
            'status_reminder' => 'required',
            'time_end' => 'required|date_format:"Y-m-d H:i:00"',
            'time_start' => 'required|date_format:"Y-m-d H:i:00"'
        ];

        if (isset($this->distance_time_reminder) && $this->distance_time_reminder != "null") {

            $rules["distance_time_reminder"] = 'gt:0';

        }

        if (isset($this->distance_time_reminder_2) && $this->distance_time_reminder_2 != "null") {

            $rules["distance_time_reminder_2"] = 'gt:0';

        }

        if (isset($this->time_start) && $this->time_start != "null") {

            $now = date(Carbon::now());

            if (strtotime($this->time_start) <= strtotime($now)) {

                $rules["time_start"] = 'after:' . $now;

            }
        }

        if (isset($this->time_end) && $this->time_end != "null") {

            $now = date(Carbon::now());

            if (strtotime($this->time_end) <= strtotime($now)) {

                $rules["time_end"] = 'after:' . $now;

            }
        }

        if (isset($this->time_start) && $this->time_start != "null" && isset($this->time_end) && $this->time_end != "null") {

            if (strtotime($this->time_end) <= strtotime($this->time_start)) {

                $rules["time_end"] = 'after:' . $this->time_start;

                // dd($rules);

            }

        }

        // dd($rules);

        return $rules;
    }

    public function messages()
    {
        return [
            'time_end.after' => __('validation.end_time_after_start_time', ['attribute' => __('models/events.fields.end_time_after_start_time')]),
            'time_start.after' => __('validation.start_time_after', ['attribute' => __('models/events.fields.start_time_after')]),
            'time_end.after' => __('validation.end_time_after', ['attribute' => __('models/events.fields.end_time_after')]),
            'distance_time_reminder.gt' => __('validation.gt', ['attribute' => __('models/events.fields.distance_time_reminder')]),
            'distance_time_reminder_2.gt' => __('validation.gt', ['attribute' => __('models/events.fields.distance_time_reminder_2')]),
        ];
    }
}
