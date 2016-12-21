<?php

return [
    'project_name' => 'Fpoll',
    'login' => 'Login',
    'logout' => 'Logout',
    'register' => 'Register',
    'create_poll' => 'Create poll',
    'forgot_password' => 'Forgot Your Password?',
    'remember' => 'Remember me',
    'email' => 'Email',
    'password' => 'Password',
    'avatar' => 'Avatar',
    'confirm_password' => 'Confirm password',
    'name' => 'Name',
    'label_gender' => 'Gender',
    'male' => 'Male',
    'female' => 'Female',
    'profile' => 'Profile',
    'edit' => 'Edit',
    'home' => 'Home',
    'admin_page' => 'ADMIN PAGE',
    'errors' => 'Message',

    /**
     * MASTER ADMIN
     */
    'placeholder_search' => 'START TYPING...',
    'name_admin_page' => 'ADMIN - VOTING',
    'main_menu' => 'MAIN MENU',
    'nav_menu' => [
        'user' => 'User',
        'poll' => 'Poll',
    ],

    /**
     * EMAIL
     */
    'mail' => [
        'head' => 'Fpoll',
        'link_vote' => 'Link to vote:',
        'link_admin' => 'Link manager vote:',
        'subject' => 'Fpoll',
        'delete_all_participant' => 'Admin of poll deleted all participant',
        'register_active_mail' => 'You register successfully. Please click on link to active account. Link here: ',
        'edit_poll' => [
            'subject' => 'Fpoll - Edit information of poll',
            'head' => 'Fpoll',
            'summary' => 'Poll of you changed!',
            'thead' => [
                'STT' => 'NO.',
                'info' => 'INFORMATION',
                'old_data' => 'OLD DATA',
                'new_data' => 'NEW DATA',
                'date' => 'DATE',
            ],
        ],
        'create_poll' => [
            'subject' => 'Fpoll - Create a poll',
            'title' => 'Poll',
            'head' => 'Fpoll',
            'dear' => 'Dear',
            'thank' => 'Thank you because you have used website our. <br> Your poll created SUCCESS. Below, it\'s two link which they send your mail.',
            'link_vote' => 'Link to vote for this poll',
            'description_link_vote' => 'Send this link to every body that you want invite for participant.',
            'link_admin' => 'Link to administrator for this poll',
            'description_link_admin' => 'Access this link help you can change, close or delete poll of you.',
            'password' => 'Password',
            'note' => '*<u>Note</u>: You can login our website without must not register a new account. Let\'s click "Active account" to open account of you',
            'active_account' => 'Active account',
            'end' => '-- END --',
        ],
        'backup_database' => [
            'subject' => 'Fpoll - Backup database',
            'head' => 'Hello Admin, This email to send backup database file',
        ],
        'participant_vote' => [
            'subject' => 'Fpoll - invite you vote a poll',
            'invite' => 'You have been invited to participant this poll. Let\'s clink link below to vote',
        ],
        'edit_option' => [
            'subject' => 'Fpoll - Edit option of poll',
            'old_option' => 'OLD OPTION',
            'new_option' => 'NEW OPTION',
            'thank' => 'Thank you because you have used website our',
            'title' => 'Change option',
        ],
        'edit_setting' => [
            'subject' => 'Fpoll - Edit setting of poll',
            'old_setting' => 'OLD SETTING',
            'new_setting' => 'NEW SETTING',
            'title' => 'Change setting',
        ],
        'register' => [
            'subject' => 'Fpoll - Register account',
            'thank' => 'Thank you because you have used website our. <br> Your register account SUCCESS. Below, it\'s link to active account',
            'link_active' => 'Click to this link to active account',
        ],
        'edit_link' => [
            'subject' => 'Fpoll - Edit link of poll',
            'thank' => 'Thank you because you have used website our. <br> You edit link SUCCESS.',
            'link_edit' => 'Click to blow link to view detail',
        ],
        'close_poll' => [
            'subject' => 'Fpoll - Close poll',
            'thank' => 'Thank you because you have used our website. <br> You close poll SUCCESS.',
            'link_admin' => 'Click to blow link to manage poll',
        ],
        'open_poll' => [
            'subject' => 'Fpoll - Open poll',
            'thank' => 'Thank you because you have used our website. <br> You open poll SUCCESS.',
            'link_admin' => 'Click to blow link to manage poll',
        ],
        'delete_participant' => [
            'subject' => 'Fpoll - Delete all participant of poll',
            'thank' => 'Thank you because you have used our website. <br> You delete all vote of poll SUCCESS.',
            'link_admin' => 'Click to blow link to manage poll',
        ],
    ],
    'footer' => [
        'location' => '13F Keangnam Landmark 72 Tower, Plot E6, Pham Hung Road, Nam Tu Liem, Ha Noi, Viet Nam',
        'copyright' => 'Copyright 2016 Framgia, Inc. <br>All rights reserved.',
        'email' => 'hr_team@framgia.com',
        'phone' => ' 84-4-3795-5417',
        'about' => 'Fpoll - a simple, convinient and powerfull Poll System',
        'description_website' => 'Fpoll help to create a poll quickly and easily',
        'facebook' => 'https://www.facebook.com/FramgiaVietnam',
        'github' => 'https://github.com/framgia',
        'linkedin' => 'https://www.linkedin.com/company/framgia-vietnam',
    ],
    'paginations' => 'Showing :start to :finish of :numberOfRecords entry|Showing :start to :finish of :numberOfRecords entries',
    'gender' => [
        '' => '',
        '0' => 'Female',
        '1' => 'Male',
        '2' => 'Other',
    ],

    /*
     * Home page
     */
    'feature' => [
        'name' => 'FEATURES',
        'vote' => 'Create a poll fast and easy',
        'chart' => 'Result illustrated by bar chart and pie chart ',
        'security' => 'Guaranteed security by password of poll',
        'export' => 'Can export result to PDF file or EXCEL file',
        'share' => 'Share poll by facebook',
        'responsive' => 'Support multiple deceive: laptop or mobie',
    ],
    'tutorial' => 'Tutorial',
    'feedback' => 'Feedback',
    'top' => 'Top',
];
