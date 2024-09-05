<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed'   => 'Email đăng nhập hoặc mật khẩu không đúng.',
    'throttle' => 'Quá nhiều lần đăng nhập. Vui lòng thử lại sau :seconds giây.',
    'unverify_email' => 'Email chưa xác thực.',

    'name'             => 'Họ và tên',
    'full_name'        => 'Họ và tên',
    'email'            => 'Email',
    'password'         => 'Mật khẩu',
    'confirm_password' => 'Xác nhận mật khẩu',
    'remember_me'      => 'Ghi nhớ',
    'sign_in'          => 'Đăng nhập',
    'sign_out'         => 'Đăng xuất',
    'register'         => 'Đăng ký',
    'register_free'         => 'Đăng ký miễn phí',

    'login' => [
        'title'               => 'Đăng nhập',
        'forgot_password'     => 'Quên mật khẩu',
        'register_membership' => 'Đăng ký thành viên mới',
        'success_message'     => 'Đăng nhập thành công',
        'account_locked'     => 'Tài khoản đã bị khoá.',
        'session_expired'     => 'Phiên sử dụng hết hạn.',
        'logout_successfully'     => 'Đăng xuất thành công.',
    ],

    'registration' => [
        'title'           => 'Đăng ký thành viên mới',
        'i_agree'         => 'Tôi đồng ý',
        'terms'           => 'chính sách',
        'exist'           => 'Tài khoản của bạn đã tồn tại',
        'have_membership' => 'I already have a membership',
        'success_message' => 'Đăng ký thành công. Vui lòng xác thực email trước khi đăng nhập.',
        'success_invite' => 'Đã gửi thư mời thành công.',
        'resend_active_email' => 'Đã gửi lại email xác thực tài khoản. Vui lòng kiểm tra và xác thực email trước khi đăng nhập.',
    ],

    'forgot_password' => [
        'title'          => 'Nhập email để khôi phục mật khẩu',
        'send_pwd_reset' => 'Send Password Reset Link',
    ],

    'reset_password' => [
        'title'         => 'Khôi phục mật khẩu',
        'reset_pwd_btn' => 'Xác nhận',
        'password_update_success' => 'Đổi mật khẩu thành công',
        'old_password_doesnt_matched' => 'Mật khẩu hiện tại không đúng',
        'success_message' => 'Chúng tôi đã gửi cho bạn một e-mail với URL đặt lại mật khẩu của bạn.',
        'unknown_error' => 'Có lỗi xảy ra trong quá trình khôi phục lại mật khẩu.',
        'timeout' => 'Thời gian hiệu lực của mã xác thực đã hết hạn.',
        'setting_new_password' => 'Bạn có thể nhập mật khẩu mới',
        'notFound' => 'Mã xác thực không hợp lệ',
    ],

    'emails' => [
        'password' => [
            'reset_link' => 'Nhấn vào đây để khôi phục mật khẩu',
        ],
        'welcome_subject' => 'Chào mừng bạn đã đến với :app_name',
        'welcome_body' => 'Cảm ơn bạn đã đăng ký ứng dụng của chúng tôi! Để bắt đầu những trải nghiệm thú vị, bạn vui lòng nhấn nút xác nhận bên dưới để kích hoạt tài khoản của bạn nhé.',
        'register_transaction' => 'Chúc mừng đã đăng ký thành công khoá học của chúng tôi. Chúc bạn có những trải nghiệm thú vị của khoá học',
        'verify_button' => 'Xác nhận',
        'not_exist' => 'Chúng tôi không thể tìm thấy người dùng có địa chỉ email này.',
        'thank_you' => 'Cảm ơn bạn đã tin tưởng và sử dụng khoá học của chúng tôi.',
        'notification_error' => 'Hiện tại người dùng đã phát hiện lỗi. Vui lòng khắc phục lỗi này sớm nhất có thể.',
        'notification_submit_register' => 'Bạn nhận được một yêu cầu đăng ký nhận thông tin từ email'
    ],
    'app' => [
        'member_since' => 'Member since',
        'messages'     => 'Messages',
        'settings'     => 'Settings',
        'lock_account' => 'Lock Account',
        'profile'      => 'Profile',
        'online'       => 'Online',
        'search'       => 'Search',
        'create'       => 'Create',
        'export'       => 'Export',
        'print'        => 'Print',
        'reset'        => 'Reset',
        'reload'       => 'Reload',
        'import'       => 'Import',
        'exportTemp'   => 'Export Template',
    ],
    'users' => [
        'sign_in' => 'Đăng nhập',
        'email' => 'Email',
        'login' => 'Đăng nhập',
        'Password' => 'Mật khẩu',
        'confirmpassword' => 'Nhập lại mật khẩu',
        'forget_password' => 'Quên mật khẩu',
        'name_user' => 'Họ và tên',
        'register' => 'Đăng ký',
        'or' => 'Hoặc',
        'currentPassword' => 'Nhập mật khẩu cũ',
        'avatar_update_success' => 'Cập nhật ảnh đại diện thành công.',
        'update_success' => 'Cập nhật thông tin thành công.',
        'block_success' => 'Khoá tài khoản thành công.',
        'active_success' => 'Kích hoạt tài khoản thành công.'
    ],
    'avatar' => [
        'required' => 'Bạn chưa chọn ảnh đại diện.',
        'max' => 'Dung lượng file ảnh đại diện không thể lớn hơn :max kilobytes.',
        'min' => 'Dung lượng file ảnh đại diện không thể nhỏ hơn :min kilobytes.',
        'mines' => 'Ảnh đại diện phải thuộc 1 trong các loại :values.',
    ],
    'image_attachment' => [
        'max' => 'Dung lượng file ảnh không thể lớn hơn :max kilobytes.',
        'min' => 'Dung lượng file ảnh không thể nhỏ hơn :min kilobytes.',
        'mines' => 'Ảnh phải thuộc 1 trong các loại :values.',
    ],
    'lang' => [
        'required' => 'Cài đặt ngôn ngữ là bắt buộc.',
        'in' => 'Cài đặt ngôn ngữ không hợp lệ',
    ],
    'roles' => [
        'admin' => 'Admin',
        'user' => 'Học viên',
        'employee' => 'Nhân viên',
        'teacher' => 'Giảng viên',
    ]
];
