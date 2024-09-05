<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseIsOpen extends Notification
{
    use Queueable;
    protected $course;
    protected $type_user;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Course $course,$type_user)
    {
        $this->course = $course;
        $this->type_user = $type_user;
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
        $message = "";
        $course_name = $this->course->course_name;
        if($this->type_user == "student") {
            $message = "Khóa học: ".$course_name . " của bạn đã bắt đầu.";
        }
        if($this->type_user == "teacher") {
            $message = "Khóa học: ".$course_name . " của bạn đảm nhiệm đã bắt đầu.";
        }
        if($this->type_user == "admin") {
            $message = "Khóa học: ".$course_name . " đã bắt đầu.";
        }
        // dd($this->course);
        return [
            'message' => $message,
            'course_id' => $this->course->course_id,
            'avatar' => $this->course->course_feature_image,
            'notification' => $notifiable->unreadNotifications->find($this->id)
        ];
    }
}
