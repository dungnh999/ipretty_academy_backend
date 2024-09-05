<?php

namespace App\Http\Requests\API;

use App\Models\DiscountCode;
use Carbon\Carbon;
use InfyOm\Generator\Request\APIRequest;
use Illuminate\Validation\Rule;
class UpdateDiscountCodeAPIRequest extends APIRequest
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
        $discount_code = null;
        if ($this->route()->parameter('id') != null) {
            $discount_code_id = $this->route()->parameter('id');
            $discount_code = DiscountCode::find($discount_code_id);
        }

        $rules =
        [
            'title' => 'required|string|max:255',
            'sale_price' => 'required|numeric|min:1',
            'expired_at' => 'required|after:time_start',
            'time_start' => 'required',
            'discount_code' => 'required|'.
            Rule::unique('discount_code')->ignore($discount_code_id, 'id'),
        ];

        if (isset($this->type) && $this->type == "percent") {
            $rules["sale_price"] = 'required|numeric|between:1,100';
        }

        if (isset($this->time_start) && $this->time_start != null && $this->time_start != $discount_code->time_start && $discount_code != null && $discount_code->time_start > Carbon::now() && $this->time_start < Carbon::now()) {
            $rules["time_start"] = 'after:now';
        } else if (isset($this->time_start) && $this->time_start != null && $discount_code != null && $this->time_start != $discount_code->time_start && $discount_code->time_start < Carbon::now() && $this->time_start < $discount_code->time_start) {
            $rules["time_start"] = 'after: '. $discount_code->time_start;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'discount_code.unique' => __('validation.unique', ['attribute' => __('validation.attributes.discount_code_up')]),
            'time_start.after' => __('validation.after', ['attribute' => __('validation.attributes.time_start_up')]),
            'expired_at.after' => __('validation.after', ['attribute' => __('validation.attributes.expired_at_up')]),
            'sale_price.between' => __('validation.between', ['attribute' => __('validation.attributes.sale_price_up')]),
            'sale_price.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.sale_price_up')]),
            'sale_price.min' => __('validation.min', ['attribute' => __('validation.attributes.sale_price_up')]),
        ];
    }
}
