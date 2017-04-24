<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function () {
    Route::post('register', 'Auth\RegisterController@create');
    Route::post('login', 'Auth\AuthController@login');
    Route::get('loginSocial', 'User\SocialAccountsController@loginSocial');

    Route::resource('poll', 'PollController', ['only' => ['destroy', 'store']]);
    Route::post('poll/update/{id}', 'PollController@update');

    Route::get('poll/{id}', 'PollController@getPollDetail');

    // Voting
    Route::get('link/{token}', 'LinkController@show');
    Route::post('user/vote', 'VoteController@store');
    Route::post('feedback', 'FeedBackController@sendFeedback');

    Route::post('language', 'LanguageController@store');

    // Participant
    Route::delete('poll/participants/{token}', 'ParticipantController@deleteAll');

    // Comment Poll
    Route::post('poll/comment', 'CommentController@store');

    //Result Voted
    Route::get('poll/result/{token}', 'ResultController@show');

    Route::post('duplicatePoll', 'DuplicateController@store');

    // Reset Password
    Route::post('password/reset', 'Auth\ForgotPasswordController@sendResetLinkEmail');

    // Send email again for create poll
    Route::post('send-mail-again', 'CheckMailController@sendMail');

    Route::get('poll/result-detail/{token}', 'ResultController@resultDetail');

    Route::get('link/poll-info/{token}', 'LinkController@getInfo');

    Route::get('checkLinkExist', 'LinkController@checkLinkExist');

    Route::get('showActivity', 'ActivityController@showActivity');

    Route::post('checkLinkOfAdmin', 'LinkController@checkLinkOfAdmin');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', 'Auth\AuthController@logout');
        Route::resource('user', 'User\UsersController');
        Route::post('updateProfile', 'User\UsersController@updateProfile');
        Route::get('getPollsOfUser', 'PollController@getPollsOfUser');
        Route::get('getClosedPolls', 'PollController@loadClosedPolls');
        Route::get('getParticipatedPolls', 'PollController@getParticipantedPolls');
        Route::patch('updateLink', 'LinkController@update');
        Route::get('getProfile', 'User\UsersController@getProfile');
        Route::post('resetPassword', 'Auth\ResetPasswordController@resetPassword');
    });
});
