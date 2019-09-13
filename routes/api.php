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
    //
    // Route::post('/oauth/token', [
    //   'uses' => '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken',
    //   'middleware' => 'throttle:6000|6000,1'
    // ]);

    Route::post('/oauth/token', [
        'uses' => 'API\LoginController@issueToken',
        'middleware' => 'throttle:6000|6000,1'
    ]);

    Route::post('login', 'API\UserController@login');
    Route::post('register', 'API\UserController@register');
    Route::post('email-verify', 'API\UserController@emailVerify');
});

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('details', 'API\UserController@details');
});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
//
