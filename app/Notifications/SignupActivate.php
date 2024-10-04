<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SignupActivate extends Notification implements ShouldQueue
{
  use Queueable;

  protected $user;
  protected $password;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($user, $password = null)
  {

    $this->user = $user;
    $this->password = $password;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param mixed $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param mixed $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
    public function toMail($notifiable)
    {
        Log::info($notifiable);
        $url = env('ACADEMY_URL') . env('ACADEMY_LINK_VERIFY') . $notifiable->id . '/' . $notifiable->activation_token;
        $textBody = array(__('auth.emails.welcome_body'));
        $name = isset($this->user->name) && $this->user->name !== "" ? $this->user->name : $this->user->email;

        return (new MailMessage)
            ->subject(__('auth.emails.welcome_subject', ['app_name' => env('APP_NAME','Ipretty Education')]))
            ->from(env('MAIL_FROM_ADDRESS', 'no-reply@ipretty.com.vn'), env('MAIL_FROM_NAME', 'Ipretty Education'))
            ->view(
                'contents.notifications.mail',
                [
                    'textTitle' =>  ucwords($name),
                    'textBody' => $textBody,
                    'nameButton' => __('auth.emails.verify_button'),
                    'linkEvent' => $url,
                    'name' => $name,
                    'username' => $this->user->email,
                    'password' => $this->password ? $this->password : null
                ]
            );
    }

  /**
   * Get the array representation of the notification.
   *
   * @param mixed $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
