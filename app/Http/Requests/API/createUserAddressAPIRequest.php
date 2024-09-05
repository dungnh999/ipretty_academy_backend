<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class createUserAddressAPIRequest extends FormRequest
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
        // dd('11111');
        return [
            'phone_shipping' => 'required|regex:/(0)[0-9]/|not_regex:/[a-z]/|min:10',
            'province_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'phone_shipping.required' =>  __('messages.phone_shipping_required'),
            'phone_shipping.regex' =>  __('messages.phone_shipping_regex'),
            'phone_shipping.not_regex' =>  __('messages.phone_shipping_not_regex'),
            'province_id.required' =>  __('messages.province_id_required'),
            'district_id.required' =>  __('messages.district_id_required'),
            'ward_id.required' =>  __('messages.ward_id_required'),
        ];
    }
}
