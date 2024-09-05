<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateCourse extends Notification
{
    use Queueable;
    protected $course;
    protected $user_id;
    protected $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Course $course,$user_id, $type)
    {
        $this->course = $course;
        $this->user_id = $user_id;
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
        $user = User::where('id', $this->user_id)->first();
        if($user->name != null){
            $messenger = $user->name. " đã cập nhật thành công khóa học : ". $this->course->course_name;
        }else {
            $messenger = "Admin đã cập nhật thành công khóa học : ". $this->course->course_name ;
        }
        
        if($this->type == "student") {
            $messenger = $messenger . " mà bạn tham gia .";
        }
        
        if($this->type == "new_student") {
            $messenger = "Bạn đã được thêm vào khóa học : ".$this->course->course_name;
        }

        if($this->type == "teacher") {
            $messenger = $messenger . " mà bạn giảng dạy .";
        }else {
            $messenger = $messenger .".";
        }
        return [
            'message' => $messenger,
            'course_id' => $this->course->course_id,
            'avatar' => $this->course->course_feature_image,
            'notification' => $notifiable->unreadNotifications->find($this->id)
        ];
    }
}
