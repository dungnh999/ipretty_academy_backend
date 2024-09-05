<?php

namespace App\Jobs;

use App\Contract\CommonBusiness;
use App\Notifications\SubmitRegisterReceive;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationWhenSubmitRegisterReceiveInformation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use CommonBusiness;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $admins = $this->getUserByMultipleRole('admin');

        if (count($admins)) {
            foreach ($admins as $key => $admin) {

                Notification::send($admin, new SubmitRegisterReceive($this->email));
                event(new \App\Events\PushNotification($admin->id));   

            }
        }
    }
}
