<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class StudentCompletedCourse extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;
    protected $student_name;
    protected $isTeacher;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($course, $student_name, $isTeacher = false)
    {
        // dd(2);
        $this->course = $course;
        $this->student_name = $student_name;
        $this->isTeacher = $isTeacher;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
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
        if($this->isTeacher) {
            $messenger = $this->student_name. " đã hoàn thành khóa học : ".$this->course->course_name. ".";
            // dd($messenger);
        }else {
            $messenger = "Chúc mừng bạn đã hoàn thành khóa học : ". $this->course->course_name. ".";
        }

        return [
            'message' => $messenger,
            'course_id' => $this->course->course_id,
            'avatar' => $this->course->course_feature_image,
            'notification' => $notifiable->unreadNotifications->find($this->id)
        ];
    }
}
