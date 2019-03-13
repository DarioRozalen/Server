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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});

Route::apiResource('users', 'UsersController');
Route::post ('register', 'UsersController@register');
Route::post ('login', 'UsersController@login');
Route::apiResource('password', 'PasswordController');
Route::post('updatePassword', 'PasswordController@updatePassword');
Route::post('deletePassword', 'PasswordController@deletePassword');
Route::apiResource('category', 'CategoryController');
Route::post('updateCategory', 'CategoryController@updateCategory');
Route::post('deleteCategory', 'CategoryController@deleteCategory');