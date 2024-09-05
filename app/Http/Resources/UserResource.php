<?php

namespace App\Http\Resources;

use App\Models\CourseStudent;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        $meta = [];
//        if (isset($this->meta) && $this->meta != null) {
//            $meta = json_decode($this->meta);
//        }
//
//        $position = !empty($meta->position) ? $meta->position : '';
//        $company = !empty($meta->company) ? $meta->company : '';
//
        $department = null;
        $department_name = null;
        if (isset($this->department_id) && $this->department_id != null) {
            $department = $this->department;
            $department_name = $this->department->department_name;
        }else {
            $department = !empty($meta->department) ? $meta->department : '';
            $department_name = $department;
        }
//        // var_dump($department);
//        $permissions = [];
//        if (isset($this->permissions) && count($this->permissions)) {
//            foreach ($this->permissions as $key => $permission) {
//                array_push($permissions, $permission->name);
//            }
//        }
//
//        $isTeacher = false;
//
//        if ($this->hasRole(['employee']) && $this->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) {
//            $isTeacher = true;
//        }
//
//        $isActive = false;
//        if (isset($this->email_verified_at) && $this->email_verified_at != null) {
//            $isActive = true;
//        }
//
//        $role = count($this->roles) ? $this->roles[0]->name : null;
//
//        $certificates = [];
//
//        $courses = [];
//
//        $coursesTeaching = [];
//
//        if ($this->hasRole('user') || $this->hasRole('employee')) {
//            $certificates = $this->certifacates;
//            $courses = $this->coursesLearning;
//        }
//
//        if ($isTeacher) {
//            $coursesTeaching = $this->coursesTeaching;
//        }
//
        $response = [
            'id' => $this->id,
            'name' => $this->name ? $this->name : "",
            'code' => $this->code ? $this->code : "",
            'email' => $this->email,
            'gender' => $this->gender,
            'menuroles' => $this->menuroles,
//            'role' => $role,

            'phone' => $this->phone ? $this->phone : "",
            'address' => $this->address ? $this->address : "",
            'avatar' => $this->avatar,
            'isLocked' => $this->isLocked,
            'birthday' => $this->birth_day ? $this->birth_day->format('Y-m-d') : "",
            'lang' => $this->lang,
            'id_number' => $this->id_number,
            'point' => $this->point,
            'about' => $this->about ? $this->about : "",
//            'company' => $company,
//            'position' => $position,
//            'isTeacher' => $isTeacher,
            'department_id' => $this->department_id ? $this->department_id : "",
            'department_name' => $department_name,
            'department' => $department,
//            'isActive' => $isActive,
//            'certificates' => CertificateResource::collection($certificates),
//            'courses_learning' => CoursesLearningResource::collection($courses),
//            'courses_teaching' => CoursesTeachingResource::collection($coursesTeaching),
//            'permissions' => count($permissions) ? implode(',', $permissions) : ""
        ];
//
//        if (isset($this->isFullInformation) && $this->isFullInformation === false) {
//            $removeField = ['email', 'address', 'phone', 'birthday', 'id_numnber'];
//            foreach ($removeField as $key => $field) {
//                unset($response[$field]);
//            }
//
//        }
//
//
//        if (isset($this->isShorterm) && $this->isShorterm === true) {
//            $removeField = ['courses_learning', 'courses_teaching', 'permissions', 'certificates', 'role', 'menuroles'];
//            foreach ($removeField as $key => $field) {
//                unset($response[$field]);
//            }
//        }

        return $response;
    }
}
