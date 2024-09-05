<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\AddStudentEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationWhenAddStudentEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $student_id;
    protected $event;
    protected $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($student_id, $event, $time)
    {
        $this->student_id = $student_id;
        $this->event = $event;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $member_student = User::where('id', $this->student_id)->first();

        Notification::send($member_student, new AddStudentEvent($member_student, $this->event, $this->time));
    }
}
