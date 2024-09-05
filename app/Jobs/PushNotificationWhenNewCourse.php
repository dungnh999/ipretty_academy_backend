<?php

namespace App\Jobs;

use App\Contract\CommonBusiness;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\User;
use App\Notifications\NewCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationWhenNewCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use CommonBusiness;
    protected $course;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($course)
    {
        $this->course = $course;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $admins = $this->getUserByMultipleRole('admin');

        $author_id = $this->course->course_created_by;

        if (count($admins)) {
            foreach ($admins as $key => $admin) {

                if ($admin->id == $author_id) {
                    $message = "Bạn vừa tạo mới một khóa học.";
                } else {
                    $message = $this->course->createdBy->name . " vừa tạo mới một khóa học.";
                }

                Notification::send($admin, new NewCourse($this->course, $message));
                event(new \App\Events\PushNotification($admin->id));

            }
        }

        // $admins = User::where('menuroles', 'admin')->wherenull('deleted_at')->get();
        // // $course = Course::where('course_id', $this->course_id)->first();
        // $user = auth()->user();
        // if(count($admins) > 0 ){
        //     foreach($admins as $admin) {
        //         $member_admin = User::where('id', $admin->id)->first();
        //         if($admin->id == $user->id){
        //             $message = "Bạn vừa tạo mới một khóa học.";
        //         }else {
        //             $message = $user->name. " vừa tạo mới một khóa học.";
        //         }
        //         if($member_admin)
        //         { 
        //             Notification::send($member_admin, new NewCourse($this->course,$message));
        //         }
        //     }
        // }
        
        $teacher = $this->course->teacher;

        if ($teacher) {
            $message = $teacher->name ? ($teacher->name . ", bạn vừa được thêm vào phụ trách khóa học : " . $this->course->course_name) : $teacher->email . ", bạn vừa được thêm vào phụ trách khóa học : " . $this->course->course_name;
            Notification::send($teacher, new NewCourse($this->course, $message));
            event(new \App\Events\PushNotification($teacher->id));
        }

        $course_students = $this->course->students;

        if (count($course_students)) {
            foreach ($course_students as $course_student) {
                
                $message = $course_student->name ? ($course_student->name . ", bạn vừa được thêm vào khóa học : " . $this->course->course_name) : $course_student->email . ", bạn vừa được thêm vào khóa học : " . $this->course->course_name;
                Notification::send($course_student, new NewCourse($this->course, $message));
                event(new \App\Events\PushNotification($course_student->id));
            } 
        }
    }
}
