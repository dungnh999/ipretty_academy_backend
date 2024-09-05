<?php

namespace App\Http\Requests\API;

use App\Models\PushNotification;
use InfyOm\Generator\Request\APIRequest;

class CreatePushNotificationAPIRequest extends APIRequest
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
        $notification_cat = ['AD', 'DOC', 'FUNC', 'HOL', 'POL'];

        $rules = [
            'notification_title' => 'required',
            'notification_message' => 'required',
            'isPublished' => 'required|boolean',
        ];

        if (isset($this->notification_cat)) {
            $rules['notification_cat'] = 'in:'. implode(',', $notification_cat);  
        }

        if (!isset($this->notification_cat)) {
            $rules['group_receivers'] = 'required';   
        }

        if (!isset($this->group_receivers)) {
            $rules['notification_cat'] = 'required|in:' . implode(',', $notification_cat);  
        }

        return $rules;
    }


    public function messages()
    {
        return [
            'notification_cat.in' => __('validation.in', ['attribute' => __('validation.attributes.notification_cat_up')]),
            'group_receivers.in' => __('validation.in', ['attribute' => __('validation.attributes.group_receivers_up')]),
        ];
    }
}
