<?php

namespace App\Jobs;

use App\Contract\CommonBusiness;
use App\Models\User;
use App\Notifications\ActiveAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationWhenActiveAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use CommonBusiness;
    protected $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
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

        if (count($admins)) {
            foreach ($admins as $admin) {

                $type = "admin";

                Notification::send($admin, new ActiveAccount($this->user, $type));
                event(new \App\Events\PushNotification($admin->id));
            }
        }

        // $admins = User::where('menuroles', 'admin')->wherenull('deleted_at')->get();
        // if(count($admins) > 0 ){
        //     foreach($admins as $admin) {
        //         $type = "admin";
        //         Notification::send($admin, new ActiveAccount($this->user_id, $type));
        //     }
        // }

        // $member = User::where('id', $this->user_id)->first();
        if($this->user)
        {
            $type = "student";
            Notification::send($this->user, new ActiveAccount($this->user, $type));
            event(new \App\Events\PushNotification($this->user->id));

        }
    }
}
