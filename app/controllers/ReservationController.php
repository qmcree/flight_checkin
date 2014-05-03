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
        $utcDate = self::getUtcDate($_POST['airport_id'], $_POST['date']);

        $flight = Flight::create(array(
            'date' => $utcDate,
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

    /**
     * Converts date to UTC based on airport's timezone.
     * @param integer $airportId
     * @param string $date
     * @return string
     */
    protected static function getUtcDate($airportId, $date)
    {
        $airport = Airport::with('timezone')->find($airportId);

        $timezone = new DateTimeZone($airport['relations']['timezone']['attributes']['name']);
        $dateTime = new DateTime($date, $timezone);

        return gmdate('Y-m-d H:i:s', $dateTime->getTimestamp());
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