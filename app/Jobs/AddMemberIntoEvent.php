<?php

namespace App\Jobs;

use App\Models\EventStudent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddMemberIntoEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $member_id;
    protected $events;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($member_id, $events)
    {
        $this->member_id = $member_id;
        $this->events = $events;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (count($this->events)) {
            foreach ($this->events as $key => $event) {
                # code...
                EventStudent::create([
                    'user_id' => $this->member_id,
                    'event_id' => $event->id,
                ]);

                $job = (new PushNotificationWhenAddStudentEvent($this->member_id, $event, null));

                dispatch($job);
            }
        }
    }
}
