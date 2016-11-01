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
        'only' => ['index', 'edit', 'destroy']
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

Route::get('load-initiated-poll', 'User\LoadPollsController@loadInitiatedPolls');

Route::get('load-participanted-in-poll', 'User\LoadPollsController@loadParticipantedPolls');

Route::get('load-closed-poll', 'User\LoadPollsController@loadClosedPolls');

Route::get('link/{token?}', 'LinkController@show');

Route::get('/redirect/{provider}', 'SocialAuthController@redirectToProvider');
Route::get('/callback/{provider}', 'SocialAuthController@handleProviderCallback');

/*
 /--------------------------------------------------------------------
 / Route Admin
 /--------------------------------------------------------------------
 */
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.'], function () {
    Route::resource('poll', 'PollController', ['except' => [
        'show'
    ]]);
});

/*
 * Route check token of link
 */
Route::resource('link', 'LinkController', ['only' => [
    'store'
]]);
