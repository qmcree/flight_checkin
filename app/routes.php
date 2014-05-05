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

Route::pattern('id', '[0-9]+');


Route::get('/', 'ReservationController@showCreateForm');

//Route::get('reservation/lookup', 'ReservationController@lookup');
Route::get('reservation/lookup', function() {
    return 'Hellur!';
});
Route::get('reservation/{id}', 'ReservationController@showDetail'); // @todo filter for valid session.
Route::get('reservation/new', 'ReservationController@showCreateForm');
Route::post('reservation/new', 'ReservationController@create');
Route::get('reservation/{id}/edit', 'ReservationController@showEditForm'); // @todo filter for valid session.
Route::post('reservation/{id}/edit', 'ReservationController@edit'); // @todo filter for valid session.
Route::post('reservation/{id}/delete', 'ReservationController@delete'); // @todo filter for valid session.