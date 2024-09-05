<?php

namespace App\Jobs;

use App\Models\EventStudent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveMemberOutEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $member_id;
    protected $course;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($member_id, $course)
    {
        $this->member_id = $member_id;
        $this->course = $course;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // var_dump($this->member_id);
        $eventStudents = $this->course->eventStudents($this->member_id)->pluck('id')->toArray();

        // var_dump($eventStudents);
        if (count($eventStudents)) {
            // foreach ($eventStudents as $key => $eventStudent) {
            //     EventStudent::where
            //     $eventStudent->delete();
            // }
            EventStudent::whereIn('id', $eventStudents)->delete();
        }
    }
}
