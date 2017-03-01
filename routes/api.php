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

    Route::resource('poll', 'PollController', ['only' => ['update', 'destroy', 'store']]);
    Route::get('poll/{id}', 'PollController@getPollDetail');
    // Voting
    Route::get('link/{token}', 'LinkController@show');
    Route::post('user/vote', 'VoteController@store');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', 'Auth\AuthController@logout');
        Route::resource('user', 'User\UsersController');
        Route::post('updateProfile', 'User\UsersController@updateProfile');
    });
});
