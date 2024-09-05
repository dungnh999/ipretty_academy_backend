<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\User;
use App\Notifications\UpdateCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationWhenUpdateCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $course;
    protected $user_id;
    protected $student_new;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Course $course,$user_id,$student_new)
    {
        $this->course = $course;
        $this->student_new = $student_new;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $admins = User::where('menuroles', 'admin')->wherenull('deleted_at')->where('id', '!=', $this->user_id)->get();
        if(count($admins) > 0 ){
            foreach($admins as $admin) {

                $type = "admin";
                Notification::send($admin, new UpdateCourse($this->course,$this->user_id,$type));
                event(new \App\Events\PushNotification($admin->id));
            }
        }

        $course_student = CourseStudent::where('course_id', $this->course->course_id)->whereNotIn('student_id', $this->student_new)->get();
        if(count($course_student) > 0 ){
            foreach($course_student as $student) {
                $member_student = User::where('id', $student->student_id)->first();
                if($member_student)
                { 
                    $type = "student";
                    Notification::send($member_student, new UpdateCourse($this->course, $this->user_id, $type));
                    event(new \App\Events\PushNotification($member_student->id));
                }
            }
        }

        $teacher = User::where('id', $this->course->teacher_id)->first();
        if($teacher)
        { 
            $type = "teacher";
            Notification::send($teacher, new UpdateCourse($this->course, $this->user_id, $type));
            event(new \App\Events\PushNotification($teacher->id));
        }

        
        // notification for new student 
        if (count($this->student_new)) {
            $list_new_student = User::whereIn('id', $this->student_new)->get();
            if (count($course_student) > 0) {
                foreach ($list_new_student as $new_student) {
                    $type = "new_student";
                    Notification::send($new_student, new UpdateCourse($this->course, $this->user_id, $type));
                    event(new \App\Events\PushNotification($new_student->id));
                }
            }
        }

    }
}
