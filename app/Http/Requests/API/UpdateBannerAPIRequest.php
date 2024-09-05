<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerAPIRequest extends FormRequest
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
            // 'title' => 'required',
            // 'content' => 'required',
            // 'introduction' => 'required',
            // 'sub_introduction' => 'required',
            // 'color_introduction' => 'required',
            // 'color_button' => 'required',
            // 'color_title' => 'required',
            // 'color_content' => 'required',
            // 'bg_color_button' => 'required',
        ];

        if (isset($this->bannerUrl) && $this->bannerUrl != null) {
            $rules["bannerUrl"] = 'mimes:jpeg,jpg,png';
        }
        
        return $rules;
    }

    public function messages()
    {
        $requiredTitle = __('validation.required', ['attribute' => __('models/posts.fields.banner_name')]);

        $coverImage = 'bannerUrl';
        $coverImageUp = 'bannerUrlUp';

        return [
            'bannerUrl.required' => __('validation.required_file', ['attribute' => __('models/posts.fields.' . $coverImage)]),
            'bannerUrl.mimes' => __('validation.mimes', ['attribute' => __('models/posts.fields.' . $coverImageUp)]),
            'title.required' => $requiredTitle,
        ];
    }
}
