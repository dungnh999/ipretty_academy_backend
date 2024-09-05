<?php

namespace App\Http\Requests\API;

use App\Models\PostCategory;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostAPIRequest extends FormRequest
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
        ];


        // if (isset($this->is_banner)) {
        //     $rules["bannerUrl"] = 'required|mimes:jpeg,jpg,png';
        // }

        if (isset($this->bannerUrl) && $this->bannerUrl != null) {
            $rules["bannerUrl"] = 'mimes:jpeg,jpg,png';
        }


        if ((!isset($this->is_banner) && !isset($this->isTrademark)) || (isset($this->isTrademark) && $this->isTrademark == 0) || (isset($this->is_banner) && $this->is_banner == 0)) {
            $rules["category_id"] = 'required';
            // $rules["bannerUrl"] = 'required|mimes:jpeg,jpg,png';
            $rules["content"] = 'required';
        }

        $category = PostCategory::where('category_id', $this->category_id)->where('category_name', PostCategory::Post_Category_Name)->first();

        if ($category) {
            if (isset($this->bannerUrl) && $this->bannerUrl != null) {
                $rules["bannerUrl"] = 'mimes:jpeg,jpg,png';
            }
            
            $rules["content"] = 'required|max:100';
        }

        return $rules;
    }

    public function messages()
    {
        $category = PostCategory::where('category_id', $this->category_id)->where('category_name', PostCategory::Post_Category_Name)->first();

        $requiredTitle = __('validation.required', ['attribute' => __('models/posts.fields.title')]);

        if (isset($this->isTrademark) && ($this->isTrademark == 1 || $this->isTrademark == 'true') && !$category) {
            $requiredTitle = __('validation.required', ['attribute' => __('models/posts.fields.trademark_name')]);
        } else if (isset($this->is_banner) && ($this->is_banner == 1 || $this->is_banner == 'true') && !$category) {
            $requiredTitle = __('validation.required', ['attribute' => __('models/posts.fields.banner_name')]);
        }

        $coverImage = 'bannerUrl';
        $coverImageUp = 'bannerUrlUp';

        if (isset($this->isTrademark) && ($this->isTrademark == 1 || $this->isTrademark == 'true') && !$category) {
            $coverImage = 'cover_image';
            $coverImageUp = 'cover_image_up';

        }

        return [
            'bannerUrl.required' => __('validation.required_file', ['attribute' => __('models/posts.fields.' . $coverImage)]),
            'bannerUrl.mimes' => __('validation.mimes', ['attribute' => __('models/posts.fields.' . $coverImageUp)]),
            'category_id.required' => __('validation.required', ['attribute' => __('models/posts.fields.category_id')]),
            'content.max' => __('validation.max', ['attribute' => __('models/posts.fields.content_up')]),
            'title.required' => $requiredTitle,
        ];
    }
}
