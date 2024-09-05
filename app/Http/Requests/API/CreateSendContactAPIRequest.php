<?php

namespace App\Http\Requests\API;

use App\Models\ReportContact;
use InfyOm\Generator\Request\APIRequest;

class CreateSendContactAPIRequest extends APIRequest
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
            'report_content' => 'required',
            'reporter_name' => 'required',
            'reporter_email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
        ];

        if (isset($this->reporter_phone) && $this->reporter_phone) {
            $rules['reporter_phone'] = 'numeric|regex:/(0)[0-9]/|between:10,11';
        }

        if (isset($this->attachments) && $this->attachments) {
            $rules['attachments'] = 'mimes:jpeg,png,jpg,gif,svg,mp4|max:10240';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'reporter_phone.between' => __('validation.betweenPhone', ['attribute' => __('models/users.fields.phoneUp')]),
            'reporter_phone.numeric' => __('validation.numeric', ['attribute' => __('models/users.fields.phone')]),
        ];
    }
}
