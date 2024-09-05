<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActiveAccount extends Notification
{
    use Queueable;
    protected $user;
    protected $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $type)
    {
        $this->user = $user;
        $this->type = $type;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if($this->type == "admin") {
            $message = "Một khách hàng đã kích hoạt tài khoản thành công.";
        }
        if($this->type == "student") {
            $message = "Chúc mừng bạn đã kích hoạt tài khoản thành công.";
        }
        // $user = User::where('id', $this->user_id)->first();
        return [
            'message' => $message,
            'user_id' => $this->user->id,
            'avatar' => $this->user->avatar ? $this->user->avatar : "",
            'notification' => $notifiable->unreadNotifications->find($this->id)
        ];
    }
}
