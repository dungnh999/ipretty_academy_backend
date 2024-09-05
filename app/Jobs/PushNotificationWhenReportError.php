<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\User;
use App\Notifications\ReportError;
use App\Notifications\UpdateCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use App\Contract\CommonBusiness;
class PushNotificationWhenReportError implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use CommonBusiness;
    protected $user;
    protected $model;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $user = null)
    {
        $this->model = $model;
        $this->user = $user;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $admins = $this->getUserByMultipleRole('admin');

        if(count($admins) > 0 ){

            foreach($admins as $admin) {

                if($admin)

                {

                    Notification::send($admin, new ReportError($this->model, $this->user));
                    event(new \App\Events\PushNotification($admin->id));   

                }
            }
        }
    }
}
