<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'AchievmentController@getMain');

Route::get('users', 'AchievmentController@getUsers');
Route::get('users/{id}', 'AchievmentController@getUser');

Route::get('achievments', 'AchievmentController@getAchievments');
Route::get('achievments/{id}', 'AchievmentController@getAchievment');
