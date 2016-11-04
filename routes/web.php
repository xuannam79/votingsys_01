<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::group(['prefix' => 'user', 'middleware' => 'auth'], function() {
    Route::resource('profile', 'User\UsersController', [
        'only' => ['index', 'update']
    ]);
});

Route::group(['prefix' => 'user', 'middleware' => 'XSS'], function() {
    Route::resource('poll', 'User\PollController', [
        'except' => ['show']
    ]);

    Route::resource('comment', 'User\CommentController', [
        'only' => ['store', 'destroy']
    ]);

    Route::resource('vote', 'User\VoteController', [
        'only' => ['store', 'destroy']
    ]);

    Route::resource('activity', 'User\ActivityController', [
        'only' => ['show']
    ]);
});

Route::post('exportPDF', [
    'as' => 'exportPDF',
    'uses' => 'User\ExportController@exportPDF'
]);

Route::post('exportExcel', [
    'as' => 'exportExcel',
    'uses' => 'User\ExportController@exportExcel'
]);

Route::get('delete-all-participant', 'User\ParticipantController@deleteAllParticipant');

Route::get('load-initiated-poll', 'User\LoadPollsController@loadInitiatedPolls');

Route::get('load-participanted-in-poll', 'User\LoadPollsController@loadParticipantedPolls');

Route::get('load-closed-poll', 'User\LoadPollsController@loadClosedPolls');

Route::get('link/{token?}', 'LinkController@show');

Route::resource('language', 'User\LanguageController', [
    'only' => ['store']
]);

Route::get('/redirect/{provider}', 'SocialAuthController@redirectToProvider');
Route::get('/callback/{provider}', 'SocialAuthController@handleProviderCallback');

/*
 /--------------------------------------------------------------------
 / Route Admin
 /--------------------------------------------------------------------
 */
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.','middleware' => 'admin'], function () {
    Route::resource('poll', 'PollController', ['except' => [
        'show'
    ]]);
    Route::resource('user', 'UserController', ['except' => [
        'show'
    ]]);
});

Route::resource('poll', 'PollController');

/*
 * Route check token of link
 */
Route::resource('link', 'LinkController', ['only' => [
    'store'
]]);

/*
 * Route check email of creator
 */
Route::resource('email', 'EmailController', ['only' => [
    'store'
]]);

/*
 * Route change status of poll
 */
Route::resource('status', 'StatusController', ['only' => [
    'store'
]]);
