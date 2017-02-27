<?php

return [
    'validate' => [
        'user_id' => [
            'required' => 'Hãy nhập id người dùng',
            'numeric' => 'Trường này phải là số nguyên'
        ],
        'provider_user_id' => [
            'required' => 'Hãy nhập id người cung cấp',
            'max' => 'Bạn đã nhập quá số ký tự cho phép'
        ]
    ]
];
