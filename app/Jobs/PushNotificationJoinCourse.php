<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\JoinCouser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationJoinCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $student_id;
    protected $course;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($course, $student_id)
    {
        $this->student_id = $student_id;
        $this->course = $course;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $member = User::where('id', $this->student_id)->first();

        if ($member) {

            Notification::send($member, new JoinCouser($this->course, $member));

        }

    }
}
