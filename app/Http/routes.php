<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('index');
});

Route::get('/login', 'UserController@login');
Route::get('/flights', 'UserController@flights');
Route::get('/profile', 'UserController@profile');

Route::auth();

Route::post('/flights', 'UserController@searchForFlights');

/* Administration part */

Route::group(['namespace' => 'Admin', 'prefix' => 'admins', 'middleware' => 'admin'], function(){
    Route::get('/', ['as' => 'admin_index', 'uses' => 'AdminController@index']);
    Route::get('/db/import', 'AdminController@dbImport');
});