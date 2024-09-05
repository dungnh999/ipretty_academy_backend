<?php

namespace App\Jobs;

use App\Contract\CommonBusiness;
use App\Notifications\NewPushNotification;
use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushWhenPublishedNewPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CommonBusiness;
    
    protected $message;
    protected $receivers;
    protected $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message, $receivers = null, $type = null)
    {
        $this->message = $message;
        $this->receivers = $receivers;
        $this->type = $type;
        // dd($this->getUserByMultipleRole($this->receivers));
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dd($users);
        if (isset($this->receivers) && $this->receivers != null) {
            $users = $this->getUserByMultipleRole($this->receivers);

            foreach ($users as $key => $user) {
                Notification::send($user, new NewPushNotification($this->message, $this->type));
                event(new \App\Events\PushNotification($user->id));

            }
        }

        // $this->pushNotificationForUser($this->receivers);

        

        // if (isset($this->category) && $this->category != null) {
        //     $category = $this->category;
        //     switch ($category) {
        //         case 'AD':
        //             $users = $this->getUserByMultipleRole('user, employee');
        //             // dd($users);
        //             foreach ($users as $key => $user) {
        //                 Notification::send($user, new NewPushNotification($this->message));
        //             }
        //             break;
        //         case 'DOC':
        //             $users = $this->getUserByMultipleRole('user, employee, teacher');
        //             foreach ($users as $key => $user) {
        //                 Notification::send($user, new NewPushNotification($this->message));
        //             }
        //             break;
        //         case 'POL':
        //             $users = $this->getUserByMultipleRole('user, employee, teacher');
        //             foreach ($users as $key => $user) {
        //                 Notification::send($user, new NewPushNotification($this->message));
        //             }
        //             break;
        //         case 'HOL':
        //             $users = $this->getUserByMultipleRole('user, employee, teacher');
        //             foreach ($users as $key => $user) {
        //                 Notification::send($user, new NewPushNotification($this->message));
        //             }
        //             break;
        //         case 'FUNC':
        //             $users = $this->getUserByMultipleRole('user, employee, teacher');
        //             foreach ($users as $key => $user) {
        //                 Notification::send($user, new NewPushNotification($this->message));
        //             }
        //             break;
                
        //         default:
        //             # code...
        //             break;
        //     }
        // }
    }
}
