<?php

return [
    'login' => 'ログイン',
    'logout' => 'サインアウト',
    'register' => '登録された',
    'create_poll' => '投票を作成する',
    'forgot_password' => 'パスワードを忘れました?',
    'remember' => 'パスワードを記憶',
    'email' => 'メール',
    'password' => 'パスワード',
    'avatar' => 'アバター',
    'confirm_password' => 'パスワードを認証する',
    'name' => '名前',
    'label_gender' => '性別',
    'male' => '男性',
    'female' => '女性',
    'profile' => 'プロフィール',
    'edit' => '編集',
    'home' => 'ホームページ',
    'admin_page' => 'ホームページ管理者',
    'errors' => 'エラー',

    /**
     * MASTER ADMIN
     */
    'placeholder_search' => '入力を開始...',
    'name_admin_page' => '投票管理',
    'main_menu' => 'メインメニュー',
    'nav_menu' => [
        'user' => 'ユーザー',
        'poll' => '投票',
    ],

    /**
     * EMAIL
     */
    'mail' => [
        'head' => '投票のシステム',
        'link_vote' => 'リンク投票:',
        'link_admin' => '投票管理のリンク:',
        'subject' => '投票のシステム',
        'delete_all_participant' => 'この投票の管理者はすべての投票投票を削除しました',
        'register_active_mail' => 'Bạn đã đăng ký thành công, hãy kích vào đường dẫn sau để kích hoạt tài khoản.',
        'edit_poll' => [
            'head' => '投票',
            'summary' => 'あなたの投票は変更されました.',
            'thead' => [
                'STT' => '数値順',
                'info' => '情報',
                'old_data' => '古いデータ',
                'new_data' => '新しいデータ',
                'date' => '日',
            ],
        ],
        'create_poll' => [
            'title' => '投票',
            'head' => '投票のシステム',
            'dear' => '親愛な ',
            'thank' => '私たちのウェブサイトをご利用いただきありがとうございます. <br> あなたの投票が正常に作成されました. ここにあなたのメッセージに送信された2つのリンクがあります',
            'link_vote' => '投票にリンクする',
            'description_link_vote' => '投票に招待したい人々にこのリンクを送信してください.',
            'link_admin' => 'これは投票を管理するためのリンクです',
            'description_link_admin' => 'Truy cập link này để thay đổi, đóng hoặc xóa bình chọn của bạn.',//
            'password' => 'パスワード',
            'note' => '*<u>注意</u>: 新しいアカウントを登録することなくウェブサイトにログインすることができます, 「アカウントの有効化」をクリックしてアカウントを開きます',
            'active_account' => 'アカウントの有効化',
            'end' => '-- 終了 --',
        ],
        'backup_database' => [
            'head' => 'Chào Admin, File backup database đã được gửi trong phần đính kèm.',
        ],
        'participant_vote' => [
            'invite' => 'Bạn đã được mời tham gia bình chọn này, hãy kích vào đường dẫn dưới đây để tham gia bầu chọn',
        ],
        'edit_option' => [
            'old_option' => 'TÙY CHỌN CŨ',
            'new_option' => 'TÙY CHỌN MỚI',
            'thank' => 'Cảm ơn bạn đã sử dụng website của chúng tôi',
            'title' => 'Thay đổi tùy chọn',
        ],
        'edit_setting' => [
            'old_setting' => 'CÀI ĐẶT CŨ',
            'new_setting' => 'CÀI ĐẶT MỚI',
            'title' => 'Thay đổi cài đặt',
        ],
        'register' => [
            'thank' => 'Cảm ơn bạn đã sử dụng Website của chúng tôi. <br> Bạn đã đăng ký tài khoản THÀNH CÔNG. Bên dưới là link để kích hoạt tài khoản',
            'link_active' => 'Click vào link bên dưới để kích hoạt tài khoản',
        ],
        'edit_link' => [
            'thank' => 'Cảm ơn bạn đã sử dụng Website của chúng tôi. <br> Bạn đã chỉnh sửa link THÀNH CÔNG.',
            'link_edit' => 'Click vào đường dẫn bên dưới để xem chi tiết',
        ],
        'close_poll' => [
            'thank' => 'Cảm ơn bạn đã sử dụng website của chúng tôi. <br> Bạn đã đóng poll THÀNH CÔNG.',
            'link_admin' => 'Click vào đường dẫn bên dưới để quản lý poll',
        ],
        'open_poll' => [
            'thank' => 'Cảm ơn bạn đã sử dụng website của chúng tôi. <br> Bạn đã mở poll THÀNH CÔNG.',
            'link_admin' => 'Click vào đường dẫn bên dưới để quản lý poll',
        ],
        'delete_participant' => [
            'thank' => 'Cảm ơn bạn đã sử dụng website của chúng tôi. <br> Bạn đã xóa tất cả bầu chọn THÀNH CÔNG.',
            'link_admin' => 'Click vào đường dẫn bên dưới để quản lý poll',
        ],
    ],
    'paginations' => 'Hiển thị :start đến :finish của :numberOfRecords mục|Đang hiển thị :start đến :finish of :numberOfRecords mục',
    'gender' => [
        '' => '',
        '0' => '女性',
        '1' => '男性',
        '2' => '他の性別',
    ],
    'footer' => [
        'location' => 'Hùng Vương, Đà Nẵng, Việt Nam',
        'copyright' => 'Copyright &copy; 2016',
        'email' => 'poll.voting.hv@gmail.com',
        'phone' => '0988965135',
        'about' => 'ウェブサイトを推薦',
        'description_website' => '投票を迅速かつ簡単に作成できるようにするウェブサイト',
    ],

    /*
     * home page
     */
    'feature' => [
        'name' => 'TÍNH NĂNG',
        'vote' => 'Tạo bình chọn nhanh chóng và dễ dàng',
        'chart' => 'Minh họa kết quả qua các biểu đồ',
        'security' => 'Đảm bảo tính bảo mật thông qua mật khẩu bình chọn',
        'export' => 'Truy xuất kết quả dưới dạng PDF, EXCEL',
        'share' => 'Chia sẻ bình chọn thông qua Facebook',
        'responsive' => 'Truy cập mọi lúc mọi nơi và hỗ trợ trên nhiều loại thiết bị',
    ],
    'top' => 'Đầu trang',
    'tutorial' => 'Hướng dẫn',
    'feedback' => 'Gởi phản hồi',
];
