<?php

class ReservationController extends BaseController
{
    const ALERT_SUCCESS_CREATE = '<strong>Great success!</strong> We will automatically check you in at the earliest possible time, so you get a great seat!';

    public function showDetail($id)
    {

    }

    public function showCreateForm($alert = null)
    {
        $timezones = Timezone::all();

        return View::make('reservation.create')->with(array(
            'alert' => $alert,
            'timezones' => $timezones,
        ));
    }

    public function create()
    {
        $utcDate = self::getUtcDate($_POST['timezone_id'], $_POST['date']);

        $flight = Flight::create(array(
            'date' => $utcDate,
            'timezone_id' => $_POST['timezone_id'],
        ));

        Reservation::create(array(
            'flight_id' => $flight->id,
            'confirmation_number' => $_POST['confirmation_number'],
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
        ));

        return $this->showCreateForm(self::ALERT_SUCCESS_CREATE);
    }

    /**
     * Converts date to UTC based on timezone.
     * @param integer $timezoneId
     * @param string $date
     * @return string
     */
    protected static function getUtcDate($timezoneId, $date)
    {
        $timezone = Timezone::find($timezoneId);

        $timezone = new DateTimeZone($timezone['attributes']['name']);
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