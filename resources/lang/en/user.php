<?php

return [
    'title' => 'Admin - User',
    'panel_head' => [
        'index' => 'LIST ALL USERS',
        'create' => 'CREATE A NEW USER',
        'edit' => 'EDIT A USER',
    ],
    'button' => [
        'create' => 'CREATE USER',
        'reset_search' => 'RESET SEARCH',
        'search' => 'SEARCH NOW',
        'back' => 'BACK TO LIST USERS',
        'edit' => 'EDIT USER',
    ],
    'label' => [
        'search' => 'Search information of user...',
        'STT' => 'No.',
        'name' => 'Full name',
        'email' => 'E-mail address',
        'chatwork' => 'Chatwork ID',
        'gender' => [
            'name' => 'Gender',
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
            '' => null,
        ],
        'avatar' => 'Avatar',
        'password' => 'Password',
        'required' => '*',
    ],
    'label_for' => [
        'name' => 'name',
        'email' => 'email',
        'chatwork' => 'chatwork',
        'gender' => [
            'name' => 'gender',
            'male' => 'male',
            'female' => 'female',
            'other' => 'other',
        ],
        'avatar' => 'avatar',
        'password' => 'password',
    ],
    'message' => [
        'confirm_delete' => 'Are you sure you want to delete this user?',
        'not_found_users' => 'Can\'t found list of users in system',
        'create_success' => ' Create user SUCCESS',
        'create_fail' => ' Create user FAIL',
        'update_success' => ' Update user SUCCESS',
        'update_fail' => ' Update user FAIL',
        'delete_success' => ' Delete user SUCCESS',
        'delete_fail' => ' Delete user FAIL',
    ],
    'tooltip' => [
        'edit' => 'Edit this user',
        'delete' => 'Delete this user',
    ],
    'placeholder' => [
        'name' => 'Please enter full name of user...',
        'email' => 'Please enter email address of user...',
        'chatwork' => 'Please enter id chatwork of user...',
        'password' => 'Please enter password of user...',
        'gender' => 'Please choose gender...',
    ],
    'validate' => [
        'name' => [
            'required' => 'Please enter name!',
            'max' => 'Please enter a value less than or equal to ' . config('common.length_user.name'),
        ],
        'email' => [
            'required' => 'Please enter email!',
            'max' => 'Please enter a value less than or equal to ' . config('common.length_user.email'),
            'email' => 'Email invalid',
            'unique' => 'Email is exists in database. Please enter a new email',
        ],
        'chatwork' => [
            'max' => 'Please enter a value less than or equal to ' . config('common.length_user.chatwork'),
        ],
        'avatar' => [
            'image' => 'Please upload image!',
            'max' => 'Please enter a image have size less than or equal to ' . config('common.length_user.name') . 'MB',
        ],
        'password' => [
            'required' => 'Please enter password!',
            'max' => 'Please enter a value less than or equal to ' . config('common.length_user.password'),
            'min' => 'Please enter a value larger than or equal to 6 characters',
            'confirmed' => 'Password confirm incorrect, please check again!',
        ],
    ],
    'update_profile_successfully' => 'You updated profile successfully',
    'register_account_successfully' => 'You actived account successfully',
    'register_account_fail' => 'You actived account fail',
    'register_account' => 'You registered account, Please check mail to active account',
    'account_unactive' => 'Please check mail and active this account',
    'login_successfully' => 'Login successfully',
    'login_fail' => 'Login fail, Please try again',
    'login' => [
        'placeholder' => [
            'email' => 'Enter your email...',
            'password' => 'Enter your password...',
        ],
    ],
    'register' => [
        'placeholder' => [
            'name' => 'Enter your name...',
            'email' => 'Enter your email...',
            'password' => 'Enter your password...',
            'password_confirm' => 'Enter confirm password...',
        ],
    ],
];
