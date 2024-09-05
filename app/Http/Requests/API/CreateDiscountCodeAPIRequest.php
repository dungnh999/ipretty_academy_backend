<?php

namespace App\Http\Requests\API;

use App\Models\DiscountCode;
use InfyOm\Generator\Request\APIRequest;

class CreateDiscountCodeAPIRequest extends APIRequest
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


        $rules =
            [
                'title' => 'required|string|max:255',
                'sale_price' => 'required|numeric|min:1',
                'expired_at' => 'required|after:time_start',
                'time_start' => 'required|after:now',
                'discount_code' => 'required|unique:discount_code',
            ];

        if (isset($this->type) && $this->type == "percent") {
            $rules["sale_price"] = 'required|numeric|between:1,100';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => __('validation.required', ['attribute' => __('validation.attributes.title_discount_code')]),
            'discount_code.unique' => __('validation.unique', ['attribute' => __('validation.attributes.discount_code_up')]),
            'time_start.after' => __('validation.after_now', ['attribute' => __('validation.attributes.time_start_up')]),
            'expired_at.after' => __('validation.after', ['attribute' => __('validation.attributes.expired_at_up')]),
            'sale_price.between' => __('validation.between', ['attribute' => __('validation.attributes.sale_price_up')]),
            'sale_price.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.sale_price_up')]),
            'sale_price.min' => __('validation.min', ['attribute' => __('validation.attributes.sale_price_up')]),
        ];
    }
}
