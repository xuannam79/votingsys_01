<?php

return [
    'avatar_path' => 'uploads/avatar',
    'avatar_default' => 'default.jpg',
    'gender' => [
        '0' => 'Female',
        '1' => 'Male',
        '2' => 'Other',
    ],
    'activity' => [
        'participated' => '1',
        'all_participants_deleted' => '2',
        'added_a_comment' => '3',
        'reset_link' => '4',
        'delete_comment' => '5',
        'edit_vote' => '6',
    ],
    'image_default_path' => 'uploads/avatar/default.jpg',

    /**-------------------------------
     * Poll config
    -------------------------------*/
    'length_poll' => [
        'name' => 255,
        'email' => 255,
        'title' => 255,
        'description' => 255,
        'link' => 16,
        'option' => 5,
        'number_record' => 10,
        'number_option' => 1,
        'number_limit' => 2,
        'password_poll' => 16,
    ],
    'type' => [
        'single_choice' => 0,
        'multiple_choice' => 1,
    ],
    'status' => [
        'open' => 1,
        'close' => 0,
    ],
    'option' => [
        'path_image' => '/uploads/options/',
        'path_image_default' => '/uploads/images/default-thumb.gif',
    ],
    'setting' => [
        'required_email' => 1,
        'add_answer' => 2,
        'hide_result' => 3,
        'custom_link' => 4,
        'set_limit' => 5,
        'set_password' => 6,
    ],
    'input_setting' => [
        'email' => 'required_email',
        'answer' => 'add_answer',
        'result' => 'hide_result',
        'link' => 'custom_link',
        'limit' => 'set_limit',
        'password' => 'set_password',
    ],
    'participant' => [
        'invite_all' => 0,
        'invite_people' => 1,
    ],
    'email' => [
        'link_vote' => '/link/',
    ],
    'link_poll' => [
        'vote' => 0,
        'admin' => 1,
    ],
    'search_all' => 3,
    'view' => [
        'poll_mail' => 'layouts.poll_mail',
    ],
];
