<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ApprovedAndRejectTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PushNotificationTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction;
    protected $user;
    protected $status;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transaction, $user, $status)
    {
        $this->transaction = $transaction;
        $this->user = $user;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $member = User::where('id', $this->transaction->user_id)->first();

        if ($member) {

            Notification::send($member, new ApprovedAndRejectTransaction($member, $this->status));
            event(new \App\Events\PushNotification($member->id));

        }
    }
}
