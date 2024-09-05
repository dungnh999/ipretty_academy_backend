<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\User;
use App\Notifications\UpdateListStudentCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationWhenUpdateListStudentCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $course;
    protected $student_new;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Course $course,$student_new)
    {
        $this->course = $course;
        $this->student_new = $student_new;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $list_new_student = User::whereIn('id', $this->student_new)->get();
        $teacher = User::where('id', $this->course->teacher_id)->first();
        if($teacher) {
            if(count($list_new_student) > 0 ){
                foreach($list_new_student as $new_student) {

                    Notification::send($new_student, new UpdateListStudentCourse($this->course, $new_student->name));
                }
            }
        }
      
    }
}
