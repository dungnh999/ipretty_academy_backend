<div style="background-color:#F6F6F6;">
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>

    <body>
        <div class="wrapper" style="
                                    max-width: 500px;
                                    margin: auto;
                                    " cellpadding="0" cellspacing="0">
            <div class="content-logo" cellpadding="0" cellspacing="0" style="  padding-top: 48px;
                                                                                text-align: center;
                                                                                padding-bottom: 46px;">
                <a href="#" style="text-decoration: none;
                display: inline-flex;
                text-align: center;
                align-items: center;
                justify-content: center;">
                    <img src="{{ asset('LandingPage/images/logo.png') }}" style="max-width: 80%; margin: auto;" />
                </a>
                <div style="margin-top: 10px;">
                    <span style="font-size: 30px;
                      color:#147B65;
                      margin: auto 0;">
                        <strong font-weight: bold;>Ipretty.vn Education</strong>
                    </span>
                </div>
            </div>
            <div class="content-background" style="padding-left: 32px;
                                                background-color: #fff;
                                                padding-right: 32px;
                                                padding-top: 40px;
                                                padding-bottom: 40px;">
                <div class="header-main">
                    @isset($mailTitle)
                    <div class="title-main" style="text-align: center;
                        font-size:20px; padding-bottom:10px;
                        border-bottom: 1px solid #B4B4B4;
                        font-weight:bold;">
                        <p>
                            <span>{{ $mailTitle ?? '' }}<span>
                                    @isset ($eventName)
                                    <span> - {{ $eventName }}<span>
                                            @endisset
                        </p>
                </div>
                    @endisset
                </div>
                <div class="content-main" style="margin: 40px 0px">
                    @isset($contentMore)
                    @if($contentMore == "congratulation")
                    <p>Xin chúc mừng!</p>
                    @elseif($contentMore =="checkedInEvent")
                    <p>{{''}}</p>
                    @else
                    <p>Chào
                        <span style="font-weight:bold;">
                            {{ $name }}
                        </span>
                    </p>
                    @endif
                    @endisset

                    @if( isset($customizeInviteEmailContent) && isset($isCustomizeInviteEmail) && $isCustomizeInviteEmail && !empty($customizeInviteEmailContent) )
                    <div>
                        {!! html_entity_decode($customizeInviteEmailContent) !!}
                    </div>
                    @else
                    @isset($contentMore)
                    @if($contentMore == "Invitation")
                    <p><span style="font-weight:bold;"> {{ $organizerName ?? '' }} </span> đang sử dụng Ipretty.vn để mời bạn tham gia <span style="font-weight:bold;"> {{ $eventName ?? '' }} </span> </p>
                    @elseif ( $contentMore == "eventConfirmation" )
                    <p> Lời chào từ Ipretty.vn.
                        Email này là để xác nhận bạn đã đăng ký thành công {{ $eventName ?? '' }} by {{ $organizerName ?? '' }}
                    </p>
                    @elseif ( $contentMore == "resetPassword" )
                    <p>
                        Bạn đã yêu cầu đặt lại mật khẩu của mình để truy cập vào Ipretty.vn.
                    </p>
                    @isset($usernamereset)
                    <p>Tên đăng nhập của bạn: {{ $usernamereset }} </p>
                    @endisset
                    <p>
                        Vui lòng nhấp vào nút bên dưới để hoàn thành quá trình.
                    </p>
                    @elseif ( $contentMore == "congratulation" )
                    <p>Mật khẩu của bạn đã được thay đổi thành công.</p>
                    @isset($usernameSuccess)
                    <p>Tên đăng nhập của bạn: {{ $usernameSuccess }} </p>
                    @endisset
                    @isset($passwordSuccess)
                    <p>Mật khẩu của bạn: {{ $passwordSuccess }} </p>
                    <p>Vui lòng đổi mật khẩu sau khi đăng nhập. </p>
                    @endisset
                    <p>Nếu bạn không thay đổi mật khẩu, vui lòng bảo vệ tài khoản của bạn ngay lập tức.</p>
                    @endif
                    @endisset
                    @endif
                    @isset($textTitle)
                    <p>Xin chào <span style="font-weight: bold">{{$textTitle ?? ''}}</span></p>
                    @endisset
                    @isset($textBody)
                    @if(gettype($textBody) == "string")
                    <p> {{ $textBody }} </p>
                    @else
                    @foreach($textBody as $body)
                    <p> {{ $body }} </p>
                    @endforeach
                    @endif
                    @endisset
                    @isset($username)
                    <p>Tên đăng nhập của bạn: {{ $username }} </p>
                    @endisset
                    @isset($transaction_code)
                    <p>Mã đơn hàng: {{ $transaction_code }} </p>
                    @endisset
                    @isset($courses)
                        @if (count($courses) > 0)
                            <p>Danh sách khoá học bạn đã mua</p>
                            @foreach($courses as $course)
                                <p>Tên khoá học: {{ $course->course_name }} </p>
                                <p>Gía khoá học: {{ $course->course_price }} </p>
                            @endforeach
                        @endif
                    @endisset
                    @isset($grand_total)
                    <p>Tổng số tiền: {{ $grand_total }} </p>
                    @endisset
                    @isset($reporter_email)
                    <p>Thông tin người gửi: {{ $reporter_email }} </p>
                    @endisset
                    @isset($report_content)
                    <p>Mô tả lỗi: {{ $report_content }} </p>
                    @endisset
                    @isset($attachments)
                    <a href="{{ $attachments ?? '' }}" >hình ảnh lỗi</a>
                    @endisset
                    @isset($thank_you)
                    <p>{{ $thank_you }} </p>
                    @endisset
                    @isset($reset)
                    <p>Hãy chọn chức năng "Quên mật khẩu" ở màn hình đăng nhập để thiết lập mật khẩu mới cho bạn.</p>
                    @endisset
                    @isset($linkImage)
                    <span>Link hình ảnh : </span> {{$linkImage}}
                    @endisset
                    @isset($password)
                    <p>Mật khẩu của bạn: {{ $password }} </p>
                    <p>Vui lòng đổi mật khẩu sau khi đăng nhập. </p>
                    @endisset

                    @isset($nameButton)
                    <div class="button-submit" style="text-align: center;">
                        <p>
                            <a href="{{ $linkEvent ?? '' }}" style="font-family: Calibri,Helvetica,sans-serif;
                                      box-sizing: border-box;
                                      border-radius: 3px;
                                      color: #fff;
                                      display: inline-block;
                                      text-decoration: none;
                                      background-color: #147B65;
                                      border-top: 10px solid #147B65;
                                      border-right: 18px solid #147B65;
                                      border-bottom: 10px solid #147B65;
                                      border-left: 18px solid #147B65;
                                      border-radius: 30px;
                                      box-shadow:0px 1px 5px 0px rgba(0,0,0,0.2);
                                      margin: 10px 0px
                                      ">
                                {{ $nameButton ?? '' }}
                            </a>
                        </p>
                    </div>
                    @endisset
                    @isset($contentMore)
                    @if($contentMore == "Invitation")
                    <p>Nếu nó mang đến cho bạn sự kiện đầu tiên trên ứng dụng Ipretty.vn, đây là thông tin nhanh để giúp bạn làm quen với chúng tôi:</p>
                    @elseif ( $contentMore == "resetPassword")
                    <p> Nếu bạn gặp sự cố với nút "Đặt lại mật khẩu", hãy sao chép URL bên dưới và dán vào trình duyệt web của bạn.</p>
                    <a style="text-align: center; display: block;" href="{{ $linkReset ?? '' }}">www.linkhere</a>
                    @elseif ( $contentMore == "congratulation")
                    <p> Cảm ơn bạn đã sử dụng ứng dụng của chúng tôi!</p>
                    @elseif ( $contentMore == "checkedInEvent")
                    <p> Chúc bạn có một thời gian tuyệt vời khi tham gia sự kiện</p>
                    @endif
                    @endisset
                    @isset($textEndMail)
                    <p>{{ $textEndMail ?? '' }} </p>
                    @endisset

                    <p>Best Regards,</p>
                    <p>Ipretty.vn Supporting Team</p>
                </div>
            </div>
            <div class="footer" align="center" width="570" cellpadding="0" cellspacing="0" style="
                                max-width: 500px;
                                margin: auto;
                                padding-top:32px;
                                padding-bottom:30px;">

                <tr>
                    <td colspan="2" class="content-cell" align="center">
                        © 2021 Ipretty.vn
                    </td>
                </tr>
            </div>
        </div>
    </body>

    </html>

</div>