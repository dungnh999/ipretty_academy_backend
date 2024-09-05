<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmitRegisterReceive extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $email;
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $textBody = array(__('auth.emails.notification_submit_register'));
        $name = isset($notifiable->name) && $notifiable->name !== "" ? $notifiable->name : $this->user->email;

        return (new MailMessage)
            ->subject(__('auth.emails.welcome_subject', ['app_name' => env('APP_NAME', 'Ipretty Education')]))
            ->from(env('MAIL_FROM_ADDRESS', 'no-reply@ipretty.com.vn'), env('MAIL_FROM_NAME', 'Ipretty Education'))
            ->view(
                'notifications.mail',
                [
                    'textTitle' =>  ucwords($name),
                    'textBody' => $textBody,
                    'reporter_email' => $this->email,
                ]
            );
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
            //
        ];
    }
}
