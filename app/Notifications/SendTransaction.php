<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendTransaction extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $order)
    {
        $this->order = $order;
        $this->user = $user;
        // dd(count($this->order->courses));
        // dd($this->user);
        // dd($this->order->orderItems);
        //"payment_method" => "at_company"
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
        $textBody = array(__('auth.emails.register_transaction'));
        $name = isset($this->user->name) && $this->user->name !== "" ? $this->user->name : $this->user->email;
        $thankYou = __('auth.emails.thank_you');

            return (new MailMessage)
                ->subject(__('auth.emails.welcome_subject', ['app_name' => env('APP_NAME','Ipretty Education')]))
                ->from(env('MAIL_FROM_ADDRESS', 'no-reply@ipretty.com.vn'), env('MAIL_FROM_NAME', 'Ipretty Education'))
                ->view(
                    'notifications.mail',
                    [
                        'textTitle' =>  ucwords($name),
                        'textBody' => $textBody,
                        // 'nameButton' => __('auth.emails.verify_button'),
                        // 'linkEvent' => url($url),
                        // 'name' => $name,
                        // 'course_name' => $this->order->courses[0]->course_name,
                        // 'course_price' => $this->order->courses[0]->course_price,
                        'courses' => $this->order->courses,
                        'transaction_code' => $this->order->transaction->transaction_code,
                        'grand_total' => $this->order->grandTotal,
                        'thank_you' => $thankYou,
                        // 'password' => $this->password ? $this->password : null
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
