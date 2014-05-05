<?php

class ReservationController extends BaseController
{
    const ALERT_DANGER_LOOKUP = "Whoops! I can't find a reservation matching those details.";
    const ALERT_SUCCESS_CREATE = "<strong>Great success!</strong> We will automatically check you in at the earliest possible time so you can board early!";

    public function showLookupForm()
    {
        return View::make('reservation.lookup')->with(array(
            '_success' => $this->getAlertSuccess(),
            '_danger' => $this->getAlertDanger(),
        ));
    }

    /**
     * Finds reservation_id based on parameters, sets session, and then redirects to show details.
     * GET parameters should contain 'confirmation_number', 'first_name', 'last_name'.
     */
    public function lookup()
    {
        if (empty($_GET['confirmation_number']) || empty($_GET['first_name']) || empty($_GET['last_name'])) {
            return $this->showLookupForm();
        } else {
            $reservation = Reservation::where('confirmation_number', '=', $_GET['confirmation_number'])->first();

            if (($reservation->count() > 0) && ($reservation->first_name === $_GET['first_name']) && ($reservation->last_name === $_GET['last_name'])) {
                $this->setAlertSuccess('Yay found that one!');
                return $this->showLookupForm();
            } else {
                $this->setAlertDanger(self::ALERT_DANGER_LOOKUP);
                return $this->showLookupForm();
            }
        }
    }

    public function showDetail($id)
    {

    }

    public function showCreateForm()
    {
        $timezones = Timezone::all();

        return View::make('reservation.create')->with(array(
            '_success' => $this->getAlertSuccess(),
            '_danger' => $this->getAlertDanger(),
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

        $this->setAlertSuccess(self::ALERT_SUCCESS_CREATE);
        return $this->showCreateForm();
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