<?php

namespace App\Jobs;

use App\Contract\CommonBusiness;
use App\Models\CommentFAQ;
use App\Models\User;
use App\Notifications\NewCommentFAQ;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationWhenNewCommentFAQ implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use CommonBusiness;
    protected $comments;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CommentFAQ $comments)
    {
        $this->comments = $comments;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $admins = User::where('menuroles', 'admin')->wherenull('deleted_at')->get();
        // if(count($admins) > 0 ){
        //     foreach($admins as $admin) {
        //         $member_admin = User::where('id', $admin->id)->first();
        //         if($member_admin)
        //         { 
        //             Notification::send($member_admin, new NewCommentFAQ($this->comments));
        //         }
        //     }
        // }

        $admins = $this->getUserByMultipleRole('admin');

        if (count($admins)) {
            foreach ($admins as $key => $admin) {

                Notification::send($admin, new NewCommentFAQ($this->comments));
                event(new \App\Events\PushNotification($admin->id));
            }
        }
    }
}
