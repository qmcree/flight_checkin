<?php

class ReservationController extends BaseController
{
    public function showDetail($id)
    {

    }

    public function showCreateForm()
    {
        $airports = Airport::all();

        return View::make('reservation.create')->with('airports', $airports);
    }

    public function create()
    {
        $airport = Airport::with('timezone')->find($_POST['airport_id']);
        $timezone = $airport['relations']['timezone']['attributes']['name'];

        $date = new DateTime($_POST['date'], new DateTimeZone($timezone));
        $utcDate = gmdate('Y-m-d H:i:s', $date->getTimestamp());
        echo $utcDate;
        exit;

        $flight = Flight::create(array(
            'date' => $_POST['date'],
            'airline_id' => 1, // always southwest.
            'airport_id' => $_POST['airport_id'],
        ));

        Reservation::create(array(
            'flight_id' => $flight->id,
            'confirmation_number' => $_POST['confirmation_number'],
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
        ));
    }

    public function showEditForm($id)
    {

    }

    public function edit($id)
    {

    }

    public function delete($id)
    {

    }
} 