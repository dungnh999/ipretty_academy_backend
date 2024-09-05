<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovedAndRejectTransaction extends Notification
{
    use Queueable;

    protected $user;
    protected $status;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $status)
    {
        $this->user = $user;
        $this->status = $status;
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

        $message = '';

        if ($this->status == 'approved') {

            $message = 'Đơn hàng của bạn đã được phê duyệt';

        } else {

            $message = 'Đơn hàng của bạn đã bị từ chối';

        }
        return [
            'message' => $message,
            'user_id' => $this->user ? $this->user->id : '',
            'avatar' => $this->user ? $this->user->avatar : '',
            'notification' => $notifiable->unreadNotifications->find($this->id)
        ];
    }
}
