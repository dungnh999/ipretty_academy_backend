<?php

namespace App\Jobs;

use App\Contract\CommonBusiness;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\User;
use App\Notifications\CourseIsOpen;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationWhenCourseIsOpen implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use CommonBusiness;
    protected $course;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Course $course)
    {
        $this->course =  $course;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $course_students = CourseStudent::where('course_id', $this->course->course_id)->get();
        // foreach ($course_students as $course_student)
        // {
        //   $member = User::where('id', $course_student->student_id)->first();
        //   if($member)
        //   { 
        //       $type_user = "student";
        //       Notification::send($member, new CourseIsOpen($this->course,$type_user));
        //   }
        // } 

        // $teacher = User::where('id', $this->course->teacher_id)->first();
        // if($teacher) {
        //   $type_user = "teacher";
        //   Notification::send($teacher, new CourseIsOpen($this->course,$type_user));
        // }
        // $admins = User::where('menuroles', 'admin')->wherenull('deleted_at')->get();
        // if(count($admins) > 0 ){
        //     foreach($admins as $admin) {
        //         $member_admin = User::where('id', $admin->id)->first();
        //         if($member_admin)
        //         { 
        //             $type_user = "admin";
        //             Notification::send($member_admin, new CourseIsOpen($this->course,$type_user));
        //         }
        //     }
        // }


        $course_students = $this->course->students;
            // dd($course_students);
        if (count($course_students)) {
            foreach ($course_students as $course_student) {
                $type_user = "student";
                Notification::send($course_student, new CourseIsOpen($this->course, $type_user));
                event(new \App\Events\PushNotification($course_student->id));   

            }
        }


        $teacher = User::where('id', $this->course->teacher_id)->first();

        if ($teacher) {
            $type_user = "teacher";
            Notification::send($teacher, new CourseIsOpen($this->course, $type_user));
            event(new \App\Events\PushNotification($teacher->id));   

        }

        $admins = $this->getUserByMultipleRole('admin');
        // dd($admins);
        if (count($admins)) {
            foreach ($admins as $key => $admin) {

                $type_user = "admin";

                Notification::send($admin, new CourseIsOpen($this->course, $type_user));
                event(new \App\Events\PushNotification($admin->id));   

            }
        }
    }
}
