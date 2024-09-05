<?php

namespace App\Jobs;

use App\Contract\CommonBusiness;
use App\Models\User;
use App\Notifications\NewRegisterAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationWhenNewAccount implements ShouldQueue
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
            foreach ($admins as $key => $admin) {

                Notification::send($admin, new NewRegisterAccount($this->user));
                event(new \App\Events\PushNotification($admin->id));
            }
        }
    }
}
