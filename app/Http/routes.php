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
    return view('welcome');
});

Route::get('users/{id}', ['as' => 'asignar', function ($id) {
    Auth::user()->givePermissionTo('edit articles');
    return view('welcome');
}]);

Route::get('/top-secret-page', [
   'middleware'=> 'can:edit articles',
   'uses' => 'TopSecretController@index',
]);