<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportError extends Notification
{
    use Queueable;

    protected $user;
    protected $model;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($model, $user = null)
    {
        $this->user = $user;
        $this->model = $model;
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
        return [
            'message' => $this->model->report_content,
            'user_id' => $this->user ? $this->user->id : '',
            'avatar' => $this->user ? $this->user->avatar : '',
            'notification' => $notifiable->unreadNotifications->find($this->id)
        ];
    }
}
