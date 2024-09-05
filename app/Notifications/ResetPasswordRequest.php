<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;
    protected $name;
    protected $email;
    protected $isAdminPage;

    public function __construct($token, $email, $name, $isAdminPage = 0)
    {
        $this->token = $token;
        $this->email = $email;
        $this->name = $name;
        $this->isAdminPage = $isAdminPage;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('resetPassword', [$this->token, $this->isAdminPage]);
        
        $textBody = array("Vui lòng truy cập vào liên kết dưới đây để lấy lại mật khẩu.");
        return (new MailMessage)
            ->subject('Thông báo lấy lại mật khẩu đăng nhập!')
            ->from('no-reply@ipretty.vn', 'Ipretty Education Flatform')
            ->view('notifications.mail',
            [
                'textTitle' => ucwords($this->name),
                'textBody' => $textBody,
                'nameButton' =>  __('auth.reset_password.reset_pwd_btn'),
                'linkEvent' => $url,
                'linkReset' => $url
            ]
        );
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
