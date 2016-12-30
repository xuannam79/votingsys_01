<?php

return [
    'login' => 'ログイン',
    'logout' => 'ログアウト',
    'register' => '登録',
    'create_poll' => '投票を作成',
    'forgot_password' => 'パスワードを忘れました？',
    'remember' => 'パスワードを保持する',
    'email' => 'メール',
    'password' => 'パスワード',
    'avatar' => 'アバター',
    'confirm_password' => 'パスワード（再入力）',
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
    'placeholder_search' => '入力する...',
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
        'head' => 'Fpoll',
        'link_vote' => '投票のリンク:',
        'link_admin' => '投票管理のリンク:',
        'subject' => 'Fpoll',
        'delete_all_participant' => '管理者は投票結果をすべて削除しました',
        'register_active_mail' => '登録が完了しました。アカウントを有効化するには下記のリンクをクリックしてください。',
        'edit_poll' => [
            'head' => '投票',
            'summary' => 'あなたの投票は変更されました',
            'thead' => [
                'STT' => '順',
                'info' => '情報',
                'old_data' => '旧データ',
                'new_data' => '新データ',
                'date' => '日付',
            ],
        ],
        'create_poll' => [
            'subject' => 'Fpoll - Create a poll',
            'title' => '投票',
            'head' => 'Fpoll',
            'dear' => 'はじめ ',
            'thank' => '本ウェブサイトをご利用いただきありがとうございます. <br> あなたの投票が作成できまた. 下記はあなたのメッセージに送信された2つのリンクがあります',
            'link_vote' => '投票用リンク',
            'description_link_vote' => 'リンクを送信して友達を招待してください.',
            'link_admin' => 'これは投票管理用のリンクです',
            'description_link_admin' => 'あなたの選択を変更・削除することができます',//
            'password' => 'パスワード',
            'note' => '*<u>注意</u>: 新しいアカウントを登録せずにログイン可能です。 「アカウントの有効化」をクリックしてアカウントを開きます',
            'active_account' => 'アカウントの有効化',
            'end' => '-- 終了 --',
        ],
        'backup_database' => [
            'subject' => 'Fpoll - Backup database',
            'head' => 'データベースのバックアップファイルは添付ファイルで送られてきました',
        ],
        'participant_vote' => [
            'subject' => 'Fpoll - invite you vote a poll',
            'invite' => 'あなたはこの投票に招待されました。投票に参加するには、下のリンクをクリックしてください。',
        ],
        'edit_option' => [
            'subject' => 'Fpoll - Edit option of poll',
            'old_option' => '前の回答',
            'new_option' => '新しい回答',
            'thank' => '当社のウェブサイトをご利用いただきありがとうございます',
            'title' => '回答を変更する',
        ],
        'edit_setting' => [
            'subject' => 'Fpoll - Edit setting of poll',
            'old_setting' => '前回の設定',
            'new_setting' => '新しい設定',
            'title' => '設定を変更する',
        ],
        'register' => [
            'subject' => 'Fpoll - Register account',
            'thank' => 'ご利用いただきありがとうございます. <br> アカウント登録は完了しました。',
            'link_active' => 'アカウントを有効にするには、リンクをクリックしてください',
        ],
        'edit_link' => [
            'subject' => 'Fpoll - Edit link of poll',
            'thank' => 'ご利用いただきありがとうございます <br> あなたが正常にリンクを編集しました',
            'link_edit' => '詳細を表示するには下のリンクをクリックしてください',
        ],
        'close_poll' => [
            'subject' => 'Fpoll - Close poll',
            'thank' => 'ご利用いただきありがとうございます <br> 投票を終了しました',
            'link_admin' => '投票管理する為に下記のリンクをクリックしてください',
        ],
        'open_poll' => [
            'subject' => 'Fpoll - Open poll',
            'thank' => 'ご利用いただきありがとうございます <br> 投票を開始しました',
            'link_admin' => '投票管理する為に下記のリンクをクリックしてください',
        ],
        'delete_participant' => [
            'subject' => 'Fpoll - Delete all participant of poll',
            'thank' => 'ご利用いただきありがとうございます <br> 回答がすべて削除されました',
            'link_admin' => '投票管理する為に下記のリンクをクリックしてください',
        ],
    ],
    'paginations' => 'Hiển thị :start đến :finish của :numberOfRecords mục|Đang hiển thị :start đến :finish of :numberOfRecords mục',
    'gender' => [
        '' => '',
        '0' => '女性',
        '1' => '男性',
        '2' => 'その他',
    ],
    'footer' => [
        'location' => 'ベトナム、ハノイ市、Nam Tu Liem区、Pham Hung町、Keangnam Landmark 72ビル、13階',
        'copyright' => 'Copyright 2016 Framgia, Inc. <br>All rights reserved.',
        'email' => 'hr_team@framgia.com',
        'phone' => ' 84-4-3795-5417',
        'about' => 'Fpoll - 簡単・効果・便利',
        'facebook' => 'https://www.facebook.com/FramgiaVietnam',
        'github' => 'https://github.com/framgia',
        'linkedin' => 'https://www.linkedin.com/company/framgia-vietnam',
    ],

    /*
     * home page
     */
    'feature' => [
        'name' => '特徴',
        'vote' => '迅速で容易に投票を作る',
        'chart' => 'グラフを使用することにより結果をあらわす',
        'security' => 'パスワードで投票セキュリティを確保',
        'export' => 'PDF、Excelなどで結果を出力',
        'share' => '簡単にFacebook経由でのシェア',
        'responsive' => '多様な機器対応で、いつでもどこでもアクセスおよびサポート',
    ],
    'top' => 'ページ先頭へ',
    'tutorial' => 'ガイド',
    'feedback' => 'フィードバックを送信',
];
