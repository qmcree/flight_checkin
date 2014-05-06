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


Route::get('/', function() {
    return Redirect::action('ReservationController@showCreateForm');
});

Route::get('reservation/new', 'ReservationController@showCreateForm');
Route::post('reservation/new', 'ReservationController@create');
Route::get('reservation/lookup', 'ReservationController@lookup');

Route::group(array('before' => 'auth.reservation'), function() {
    Route::get('reservation/{id}/edit', 'ReservationController@showEditForm');
    Route::post('reservation/{id}/edit', 'ReservationController@edit');
    Route::post('reservation/{id}/delete', 'ReservationController@delete');
});

Route::get('debug', function() {
    $upcomingFlights = Flight::upcoming()->with('reservation.checkin')->get();

    var_dump(++$upcomingFlights[0]->reservation->checkin->attempts);
});