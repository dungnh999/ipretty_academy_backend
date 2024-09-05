<?php

namespace App\Http\Requests\API;

use App\Models\ReportContact;
use InfyOm\Generator\Request\APIRequest;

class CreateReportErrorAPIRequest extends APIRequest
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
            'report_content' => 'required'
        ];

        if (isset($this->attachments) && $this->attachments != 'null') {
            $rules['attachments'] = 'mimes:jpeg,png,jpg,gif,svg,mp4|max:10240';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'report_content.required' => __('validation.required', ['attribute' => __('models/users.fields.report_content')]),
            'attachments.mimes' => __('validation.mimes', ['attribute' => __('models/users.fields.attachments_up')]),
        ];
    }
}
