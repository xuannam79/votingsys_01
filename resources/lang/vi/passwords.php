<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'password' => 'Mật khẩu phải có ít nhất 6 ký tự',
    'reset' => 'Mật khẩu của bạn đã được reset',
    'sent' => 'Chúng tôi đã gửi email để reset mật khẩu',
    'token' => 'Token để reset mật khẩu không chính xác',
    'user' => "Không tìm thấy người dùng với địa chỉ email này",
    'send_password_reset_link' => 'Gửi link để reset mật khẩu',
    'reset_password' => 'Reset mật khẩu',

    'validate' => [
        'old_password' => [
            'required' => 'Nhập mật khẩu cũ!',
            'min' => 'Mật khẩu phải ít nhất 6 ký tự',
        ],
        'password' => [
            'required' => 'Nhập mật khẩu',
            'min' => 'Mật khẩu phải ít nhất 6 ký tự',
            'confirmed' => 'Nhập lại mật khẩu không đúng',
        ],
        'password_confirmation' => [
            'required' => 'Nhập mật khẩu xác thực',
            'min' => 'Mật khẩu phải ít nhất 6 ký tự',
        ],
    ],
];
