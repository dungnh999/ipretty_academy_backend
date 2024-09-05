<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\User;
use App\Notifications\StudentCompletedCourse;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationWhenStudentCompletedCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $course_id;
    protected $student;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($course_id, User $student)
    {
        $this->course_id = $course_id;
        $this->student = $student;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $course = Course::where('course_id', $this->course_id)->first();

        if($course){
            $teacher = $course->teacher;
            $student = $this->student;
            
            if($teacher){
                $isteacher = true;
                Notification::send($teacher, new StudentCompletedCourse($course, $student->name ? $student->name : $student->email, $isteacher));
                event(new \App\Events\PushNotification($teacher->id));
            }

            if($student){
                Notification::send($student, new StudentCompletedCourse($course, $student->name ? $student->name : $student->email));
                event(new \App\Events\PushNotification($student->id));
            }
        }
    }
}
