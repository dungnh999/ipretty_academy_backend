<?php

namespace App\Notifications;

use App\Models\CommentFAQ;
use App\Models\FAQQuestion;
use App\Models\Question;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentFAQ extends Notification
{
    use Queueable;
    protected $comments;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(CommentFAQ $comments)
    {
        $this->comments = $comments;
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
        $question = FAQQuestion::where('question_id', $this->comments->question_id)->first();
        $user = User::where('id', $this->comments->commentator_id)->first();
        $message = $user->name. " đã bình luận cho một câu hỏi thường gặp .";
        return [
            'message' => $message,
            'faq_id' => $question->faq_id,
            'faq_question' => $this->comments->question_id,
            'avatar' => $user->avatar,
            'notification' => $notifiable->unreadNotifications->find($this->id)
        ];
    }
}
