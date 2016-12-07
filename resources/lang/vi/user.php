<?php

return [
    'title' => 'Admin - User',
    'panel_head' => [
        'index' => 'DANH SÁCH NGƯỜI DÙNG',
        'create' => 'TẠO NGƯỜI DÙNG MỚI',
        'edit' => 'CHỈNH SỬA MỘT NGƯỜI DÙNG',
    ],
    'button' => [
        'create' => 'TẠO NGƯỜI DÙNG',
        'reset_search' => 'XEM DANH SÁCH',
        'search' => 'TÌM KIẾM',
        'back' => 'QUAY LẠI',
        'edit' => 'CHỈNH SỬA NGƯỜI DÙNG',
    ],
    'label' => [
        'search' => 'Tìm kiếm thông tin người dùng...',
        'STT' => 'Số thứ tự',
        'name' => 'Tên đầy đủ',
        'email' => 'Địa chỉ email',
        'chatwork' => 'ID chatwork',
        'gender' => [
            'name' => 'Giới tính',
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
            '' => null,
        ],
        'avatar' => 'Hình đại diện',
        'password' => 'Mật khẩu',
        'required' => '*',
    ],
    'label_for' => [
        'name' => 'tên',
        'email' => 'email',
        'chatwork' => 'chatwork',
        'gender' => [
            'name' => 'giới tính',
            'male' => 'nam',
            'female' => 'nữ',
            'other' => 'khác',
        ],
        'avatar' => 'hình đại diện',
        'password' => 'mật khẩu',
    ],
    'message' => [
        'confirm_delete' => 'Bạn có chắc chắn sẽ xóa người dùng này hay không',
        'not_found_users' => 'Không thể tìm thấy người dùng nào trong hệ thống',
        'create_success' => 'Tạo người dùng THÀNH CÔNG',
        'create_fail' => 'Tạo người dùng THẤT BẠI',
        'update_success' => 'Chỉnh sửa thông tin THÀNH CÔNG',
        'update_fail' => 'Chỉnh sửa thông tin THẤT BẠI',
        'delete_success' => 'Xóa người dùng THÀNH CÔNG',
        'delete_fail' => 'Xóa người dùng THẤT BẠI',
    ],
    'tooltip' => [
        'edit' => 'Chỉnh sửa người dùng ngày',
        'delete' => 'Xóa người dùng này',
    ],
    'placeholder' => [
        'name' => 'Hãy nhập tên của người dùng...',
        'email' => 'Hãy nhập email của người dùng...',
        'chatwork' => 'Hãy nhập chatwork id của người dùng',
        'password' => 'Hãy nhập mật khẩu của người dùng',
        'gender' => 'Hãy chọn giới tính',
    ],
    'validate' => [
        'name' => [
            'required' => 'Hãy nhập tên của bạn',
            'max' => 'Giá trị phải nhỏ hơn hoặc bằng ' . config('common.length_user.name') . ' ký tự',
        ],
        'email' => [
            'required' => 'Hãy nhập email của bạn',
            'max' => 'Giá trị phải nhỏ hơn hoặc bằng ' . config('common.length_user.email') . ' ký tự',
            'email' => 'Email không hợp lệ',
            'unique' => 'Email đã tồn tại trong hệ thống. Hãy nhập một email khác!',
        ],
        'chatwork' => [
            'max' => 'Giá trị phải nhỏ hơn hoặc bằng ' . config('common.length_user.chatwork') . ' ký tự',
        ],
        'avatar' => [
            'image' => 'Hãy tải lên một hình ảnh',
            'max' => 'Kích thước hình ảnh phải nhỏ hơn hoặc bằng ' . config('common.length_user.name') . ' MB',
        ],
        'password' => [
            'required' => 'Hãy nhập mật khẩu của bạn',
            'max' => 'Giá trị phải nhỏ hơn hoặc bằng ' . config('common.length_user.password') . ' ký tự',
            'min' => 'Nhập mật khẩu lớn hơn hoặc bằng 6 ký tự',
            'confirmed' => 'Mật khẩu xác nhận chưa chính xác, hãy kiểm tra lại!',
        ],
    ],
    'update_profile_successfully' => 'Bạn đã cập nhật thông tin cá nhân thành công',
    'register_account_successfully' => 'Bạn đã kích hoạt tài khoản thành công',
    'register_account_fail' => 'Kích hoạt tài khoản thất bại',
    'register_account' => 'Bạn đã đăng ký tài khoản thành công, Vui lòng check mail để kích hoạt tài khoản',
    'account_unactive' => 'Vui lòng mở mail và kích hoạt tài khoản này',
    'login_fail' => 'Đăng nhập thất bại, Vui lòng thử lại',
    'login_successfully' => 'Đăng nhập thành công',
    'login' => [
        'placeholder' => [
            'email' => 'Nhập địa chỉ emai...',
            'password' => 'Nhập mật khẩu...',
        ],
    ],
    'register' => [
        'placeholder' => [
            'name' => 'Nhập tên của bạn...',
            'email' => 'Nhập địa chỉ email của bạn...',
            'password' => 'Nhập mật khẩu...',
            'password_confirm' => 'Xác nhận mật khẩu...',
        ],
    ],
];
