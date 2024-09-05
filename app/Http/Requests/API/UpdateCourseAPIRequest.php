<?php

namespace App\Http\Requests\API;

use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use InfyOm\Generator\Request\APIRequest;
use Illuminate\Validation\Rule;

class UpdateCourseAPIRequest extends APIRequest
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

    public function validator($factory)
    {
        return $factory->make(
            $this->sanitize(),
            $this->container->call([$this, 'rules']),
            $this->messages()
        );
    }

    public function sanitize()
    {
        $this->merge([
            'courses_resources' => json_decode($this->input('courses_resources'), true),
            'course_target' => json_decode($this->input('course_target'), true)
        ]);
        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $course_id = $this->route()->parameter('id');

        $course = Course::find($course_id);

        $course_type = ['Local', 'Group', 'Business'];

        $teachers = User::whereHas("roles", function ($q) {
                    $q->where("name", "employee");
                })->whereHas('permissions', function ($q) {
                    $q->where("name", PERMISSION["MANAGE_COURSES"]);
                })->get();

        $teacherIds = [];

        if (count($teachers)) {
            foreach ($teachers as $key => $teacher) {
                array_push($teacherIds, $teacher->id);
            }
        }

        $rules = [
            'course_name' => 'required|min:5|'.
            Rule::unique('courses')->ignore($this->course_id, 'course_id'),
            'course_description' => 'required',
            'course_target.course_target.*.value' => 'required',
            // 'course_feature_image' => 'mimes:jpeg,png,jpg,gif,svg|max:10240',
            // 'certificate_image' => 'mimes:jpeg,png,jpg,gif,svg|max:10240',
            'course_type' => 'required|in:' . implode(',', $course_type),
            // 'courses_resources' => 'json',
            'category_id' => 'required|exists:course_categories,category_id',
            'teacher_id' => 'required|in:' . implode(',', $teacherIds)
        ];
        
        if (isset($this->course_feature_image) && $this->course_feature_image != "null") {
            // $rules["course_feature_image"] = 'mimes:jpeg,png,jpg,gif,svg|max:10240';
        }

        if (isset($this->certificate_image) && $this->certificate_image != "null") {
            // $rules["certificate_image"] = 'mimes:jpeg,png,jpg,gif,svg|max:10240';
        }


        // if (isset($this->startTime) && $this->startTime != "null" && !empty($course) && !empty($course->startTime) && $this->startTime != $course->startTime) {
        //     $now = date(Carbon::now());

        //     if ($this->startTime < $now && $this->startTime > $course->startTime) {
        //         $rules["startTime"] = 'after_or_equal: ' . $now;

        //     }else 
        //     if ($this->startTime < $course->startTime && $this->startTime < $now) {
        //         $rules["startTime"] = 'after_or_equal: ' . $course->startTime;

        //     }
        // }

        if (isset($this->endTime) && $this->endTime != "null") {
            // $now = date(Carbon::now());

            // if (!empty($course->endTime) && isset($this->endTime) && $this->endTime < $course->endTime && $this->endTime < $now && $this->endTime > $course->startTime) {
            //     $rules["endTime"] = 'after:' . $now;
            // }else 
            if (!empty($course->startTime) && isset($this->endTime) && $this->endTime <= $course->startTime) {
                $rules["endTime"] = 'after:startTime';
            } else if (isset($this->startTime) && isset($this->endTime) && $this->endTime <= $this->startTime) {
                $rules["endTime"] = 'after:'. $this->startTime;
            }
        }
        
        if (!isset($this->course_price) && ($course->course_type == "Business" || $this->course_type == "Business")) {

            $rules["course_price"] = 'required|regex:/^\d{1,16}(\.\d{1,2})?$/|numeric|min:0';
        }
        
        if (!isset($this->course_sale_price) && ($course->course_type == "Business" || $this->course_type == "Business")) {

            // $rules["course_sale_price"] = 'required|regex:/^\d{1,16}(\.\d{1,2})?$/|numeric|min:0';
        }

        // if ($this->course_type == "Group" || $course->course_type == "Group") {

        //     $rules["leader_ids"] = "string";

        //     $rules["student_ids"] = "required|string";

        // }
        if (count($this->courses_resources["chapters"])) {
            foreach ($this->courses_resources["chapters"] as $key => $chapter) {
                if (!$chapter["chapter_name"]) {
                    $rules['courses_resources.chapters.' . $key . '.chapter_name'] = 'required';
                }
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'course_feature_image.mimes' => __('validation.mimes', ['attribute' => __('models/courses.fields.course_feature_imageUp')]),
            'certificate_image.mimes' => __('validation.mimes', ['attribute' => __('models/courses.fields.certificate_imageUp')]),
            'course_feature_image.required' =>  __('messages.course_feature_image_required'),
            'certificate_image.required' =>  __('messages.certificate_image_required'),
            'course_name.unique' => __('validation.unique', ['attribute' => __('models/courses.fields.course_nameUp')]),
            'course_name.min' => __('validation.min', ['attribute' => __('models/courses.fields.course_nameUp')]),
            'course_target.min' => __('validation.min', ['attribute' => __('models/courses.fields.course_targetUp')]),
            'startTime.after' => __('validation.after', ['attribute' => __('models/courses.fields.startTimeUp')]),
            'endTime.after' => __('validation.after', ['attribute' => __('models/courses.fields.endTimeUp')]),
            'courses_resources.chapters.*.chapter_name.required' => __('validation.required', ['attribute' => __('models/courses.fields.chapter_name')]),
        ];
    }
}
