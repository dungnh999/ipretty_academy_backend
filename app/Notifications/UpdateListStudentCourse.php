<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateListStudentCourse extends Notification
{
    use Queueable;
    protected $course;
    protected $name_student;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Course $course,$name_student)
    {
        $this->course = $course;
        $this->name_student = $name_student;
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
        if($this->name_student != null){
            $messenger = "Thành viên ". $this->name_student. " vừa được thêm vào khóa học : ". $this->course->course_name;
        }else {
            $messenger = "Có 1 thành viên mới được thêm vào khóa học : ". $this->course->course_name ;
        }

        return [
            'message' => $messenger,
            'course_id' => $this->course->course_id,
            'avatar' => $this->course->course_feature_image,
            'notification' => $notifiable->unreadNotifications->find($this->id)
        ];
    }
}
