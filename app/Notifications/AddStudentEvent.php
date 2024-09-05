<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddStudentEvent extends Notification
{
    use Queueable;

    protected $event;
    protected $student;
    protected $time;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($student, $event, $time)
    {
        $this->event = $event;
        $this->student = $student;
        $this->time = $time;
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

        if ($this->time) {

            $message = 'Bạn có 1 sự kiện sắp diễn ra trong ' . $this->time . ' phút nữa.';

        } else {

            $message = 'Bạn vừa được thêm vào 1 sự kiện';

        }

        return [
            'message' => $message,
            'event_id' => $this->event->id,
            'notification' => $notifiable->unreadNotifications->find($this->id)
        ];
    }
}
