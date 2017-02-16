<?php

return [
    'title' => '管理者 - ユーザー',
    'panel_head' => [
        'index' => 'ユーザー一覧',
        'create' => 'ユーザー新規作成',
        'edit' => 'ユーザーを編集する',
    ],
    'button' => [
        'create' => '新規作成',
        'reset_search' => '再検索',
        'search' => '検索',
        'back' => '戻る',
        'edit' => '編集',
    ],
    'label' => [
        'search' => 'ユーザー情報を検索する...',
        'STT' => '順',
        'name' => '名前',
        'email' => 'メールアドレス',
        'chatwork' => 'チャットワークID',
        'gender' => [
            'name' => '性別',
            'male' => '男性',
            'female' => '女性',
            'other' => 'その他',
            '' => null,
        ],
        'avatar' => 'アバター',
        'password' => 'パスワード',
        'required' => '*',
    ],
    'label_for' => [
        'name' => '名前',
        'email' => 'メール',
        'chatwork' => 'チャットワークID',
        'gender' => [
            'name' => '性別',
            'male' => '男性',
            'female' => '女性',
            'other' => 'その他',
        ],
        'avatar' => 'アバター',
        'password' => 'パスワード',
    ],
    'message' => [
        'confirm_delete' => 'ユーザー情報を削除します。よろしいですか？',
        'not_found_users' => '該当ユーザーを見つかりません',
        'create_success' => '作成できました',
        'create_fail' => '作成できませんでした',
        'update_success' => '更新できました',
        'update_fail' => '更新できませんでした',
        'delete_success' => '削除できました',
        'delete_fail' => '削除できませんでした',
        'feedback_success' => 'Cảm ơn bạn đã gửi feedback cho chúng tôi.',
        'feedback_fail' => 'Quá trình gửi feedback của bạn thất bại! Xin vui lòng thử lại',
    ],
    'tooltip' => [
        'edit' => 'このユーザーを編集する',
        'delete' => 'このユーザーを削除する',
    ],
    'placeholder' => [
        'name' => 'ユーザーの名前を入力してください...',
        'email' => 'ユーザーのメールアドレスを入力してください...',
        'chatwork' => 'ユーザーのチャットワークIDを入力してください',//
        'password' => 'パスワードを入力してください',
        'gender' => '性別を選択してください',
    ],
    'validate' => [
        'name' => [
            'required' => '名前は入力必須です！',
            'max' => ' ' . config('settings.length_user.name') . ' 文字以上入力してください',
        ],
        'email' => [
            'required' => 'メールアドレスを入力してください!',
            'max' => '' . config('settings.length_user.email') . ' 文字以上入力してください',
            'email' => 'メールは正しくありません',
            'unique' => '入力したメールは既に存在しています。別のメールアドレスを入力してください!',
        ],
        'chatwork' => [
            'max' => ' ' . config('settings.length_user.chatwork') . ' 文字以上入力してください',
        ],
        'avatar' => [
            'image' => '画像をアップロードしてください',
            'max' => '最大画像サイズは ' . config('settings.length_user.name') . ' MB',
        ],
        'password' => [
            'required' => 'パスワードを入力してください',
            'max' => 'パスワードは最大' . config('settings.length_user.password') . ' 文字です',
        ],
        'feedback' => [
            'required' => 'Hãy nhập phản hồi của bạn',
            'max' => 'Feedback phải nhỏ hơn hoặc bằng ' . config('settings.length_user.name') . ' ký tự',
        ],
    ],
    'update_profile_successfully' => 'プロフィールは更新されました',
    'register_account_successfully' => 'アカウント登録が完了しました',
    'register_account_fail' => 'アカウント登録に失敗しました', //
    'register_account' => 'アカウント登録が完了しました。確認メールを送信しましたので、登録されたメールアドレスをご確認ください',
    'account_unactive' => 'メールに記載したURLにアクセスして、アカウントを有効化してください',
    'login_successfully' => 'ログインできました',
    'login_fail' => 'ログイン失敗しました。もう一度お試しください',
    'login' => [
        'placeholder' => [
            'email' => 'メールアドレスを入力してください',
            'password' => 'パスワードを入力してください',
        ],
    ],
    'logout_fail' => 'Đăng xuất thất bại, vui lòng thử lại',
    'logout_success' => 'Đăng xuất thành công',
    'register' => [
        'placeholder' => [
            'name' => '名前を入力してください',
            'email' => 'メールアドレスを入力してください',
            'password' => 'パスワードを入力してください',
            'password_confirm' => 'パスワードを再度入力してください',
            'feedback' => 'Nhập phản hồi của bạn...',
        ],
    ],
];
