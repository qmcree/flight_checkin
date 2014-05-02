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


Route::get('/', 'DashboardController@show');

Route::get('reservation/all', 'ReservationController@showList');
Route::get('reservation/{id}', 'ReservationController@showDetail');
Route::get('reservation/new', 'ReservationController@showCreateForm');
Route::post('reservation/new', 'ReservationController@create');
Route::get('reservation/{id}/edit', 'ReservationController@showEditForm');
Route::post('reservation/{id}/edit', 'ReservationController@edit');
Route::post('reservation/{id}/delete', 'ReservationController@delete');

// @todo remove.
Route::get('debug', function() {
    $flight = Flight::with('airline', 'reservation')->find(3);
    var_dump($flight);
    return;
});