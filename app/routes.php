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

// debug
Route::get('debug', function() {
    $validate = Validator::make(array(
        'date' => '2014-09-11 13:09:35',
        'first_name' => 'Hello.',
    ), array(
        'date' => array('required', 'date_format:Y-m-d H:i:s'),
        'confirmation_number' => array('required', 'alpha_num', 'min:5', 'max:12'),
        'first_name' => array('required', 'alpha', 'min:2', 'max:20'),
        'last_name' => array('required', 'alpha', 'min:2', 'max:20'),
        'email' => array('required', 'email', 'max:30'),
        'timezone_id' => array('required', 'numeric', 'max:5'),
    ));

    var_dump($validate->passes());
});