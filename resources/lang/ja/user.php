<?php

return [
    'title' => '管理者 - ユーザー',
    'panel_head' => [
        'index' => 'ユーザーリスト',
        'create' => '新しいユーザーを作成',
        'edit' => 'ユーザーの編集',
    ],
    'button' => [
        'create' => 'ユーザーを作成',
        'reset_search' => 'ビューリスト',
        'search' => 'サーチ',
        'back' => 'バック',
        'edit' => 'ユーザーの編集',
    ],
    'label' => [
        'search' => 'ユーザー情報を検索する...',
        'STT' => '数値順',
        'name' => 'フルネーム',
        'email' => 'メールアドレス',
        'chatwork' => 'ID chatwork',
        'gender' => [
            'name' => '性別',
            'male' => '男性',
            'female' => '女性',
            'other' => '他の性別',
            '' => null,
        ],
        'avatar' => 'アバター',
        'password' => 'パスワード',
        'required' => '*',
    ],
    'label_for' => [
        'name' => '名前',
        'email' => 'メール',
        'chatwork' => 'chatwork',
        'gender' => [
            'name' => '性別',
            'male' => '男性',
            'female' => '女性',
            'other' => '他の性別',
        ],
        'avatar' => 'アバター',
        'password' => 'パスワード',
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
        'edit' => 'このユーザーを編集',
        'delete' => 'このユーザーを削除',
    ],
    'placeholder' => [
        'name' => 'ユーザーの名前を入力してください...',
        'email' => 'ユーザーのメールアドレスを入力してください...',
        'chatwork' => 'ユーザーのchatwork_idを入力してください',//
        'password' => 'ユーザーのパスワードを入力してください',
        'gender' => '性別を選択してください',
    ],
    'validate' => [
        'name' => [
            'required' => '名前を入力してください！',
            'max' => '値は ' . config('common.length_user.name') . ' 文字以下でなければなりません',
        ],
        'email' => [
            'required' => 'メールアドレスを入力してください!',
            'max' => '値は ' . config('common.length_user.email') . ' 文字以下でなければなりません',
            'email' => '無効なメール',
            'unique' => 'メールはシステムにすでに存在しています. 別のメールアドレスを入力してください!',
        ],
        'chatwork' => [
            'max' => '値は ' . config('common.length_user.chatwork') . ' 文字以下でなければなりません',
        ],
        'avatar' => [
            'image' => '画像をアップロードしてください',
            'max' => '画像サイズは ' . config('common.length_user.name') . ' MB以下でなければなりません',
        ],
        'password' => [
            'required' => 'パスワードを入力してください',
            'max' => '値は ' . config('common.length_user.password') . ' 文字以下でなければなりません',
        ],
    ],
    'update_profile_successfully' => 'プロフィールが正常に更新されました',
    'register_account_successfully' => 'アカウントが正常に有効にしました',
    'register_account_fail' => 'アカウントを失敗有効にしました', //
    'register_account' => 'アカウントを正常に登録しました, アカウントを有効にするためにメールをチェックしてください',
    'account_unactive' => 'メールを開いてこのアカウントを有効にしてください',
    'login_successfully' => 'ログインの成功',
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
