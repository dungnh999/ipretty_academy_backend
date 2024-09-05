<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendContact extends Notification implements ShouldQueue
{
    use Queueable;
    protected $reportContact;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reportContact)
    {
        $this->reportContact = $reportContact;
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
        $textBody = ['Đây là nội dung của liên hệ :', $this->reportContact->report_content];
        $url = $this->reportContact->attachments;
        return (new MailMessage)
        ->subject("Thông báo gửi liên hệ !")
        ->from(env('MAIL_FROM_ADDRESS', 'no-reply@ipretty.com.vn'), env('MAIL_FROM_NAME', 'Ipretty Education'))
        ->view(
            'notifications.mail',
            [
                'textTitle' =>  ucwords($notifiable->name),
                'textBody' => $textBody,
                'linkImage' => $url,
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
