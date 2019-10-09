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


Route::group(['prefix' => '/v1/'], function(){

    Route::post('/oauth/token', [
        'uses' => 'API\LoginController@issueToken',
        'middleware' => 'throttle:6000|6000,1'
    ]);

    Route::post('login', 'API\RegisterController@login');
    Route::post('register', 'API\RegisterController@register');
    Route::post('email-verify', 'API\RegisterController@emailVerify');
    Route::post('confirm-otp', 'API\LoginController@confirmOtp');

    Route::group(['prefix' => '/user', 'middleware' => 'auth:api'], function() {
        Route::get('/', 'API\Auth\UserController@getCurrentUser');
        Route::get('/security-level', 'API\Auth\UserController@getInformation');
    });

    Route::group(['prefix' => '/authenticator', 'middleware' => 'auth:api'], function() {
        Route::get('/general-otp', 'API\TwoFaceController@getOTPGoogleAuthenticator');
        Route::post('/verify-otp', 'API\TwoFaceController@otpVerify');
        Route::post('/disable-otp', 'API\TwoFaceController@disableOtp');
    });

    Route::group(['prefix' => '/notes', 'middleware' => 'auth:api'], function() {
        Route::get('/', 'API\NoteController@index');
        Route::post('/take-a-note', 'API\NoteController@store');
    });
});
