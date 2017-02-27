<?php

return [
    'validate' => [
        'user_id' => [
            'required' => 'Please enter id of user',
            'numeric' => 'Please enter a value is numeric',
        ],
        'provider_user_id' => [
            'required' => 'Please enter id of provider user',
            'max' => 'Please enter a value less than 255 char',
        ]
    ]
];
