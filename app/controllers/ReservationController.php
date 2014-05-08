<?php

use FlightCheckin\util\DateUtil;

class ReservationController extends BaseController
{
    const ALERT_DANGER_LOOKUP = "I can't find a reservation matching those details.";
    const ALERT_SUCCESS_CREATE = "We will automatically check you in at the earliest possible time so you can board early!";
    const ALERT_DANGER_PAST = "Reservations cannot be in the past.";
    const ALERT_SUCCESS_EDIT = "Your reservation has been updated.";

    protected $validatorRules;

    public function __construct()
    {
        $this->validatorRules = array(
            'date' => array('required', 'date_format:Y-m-d H:i:s'),
            'confirmation_number' => array('required', 'alpha_num', 'min:5', 'max:12'),
            'first_name' => array('required', 'alpha', 'min:2', 'max:20'),
            'last_name' => array('required', 'alpha', 'min:2', 'max:20'),
            'email' => array('required', 'email', 'max:30'),
            'timezone_id' => array('required', 'numeric', 'max:50'),
        );
    }

    protected function showLookupForm()
    {
        return $this->makeView('reservation.lookup');
    }

    /**
     * Set session for reservation ID.
     */
    protected static function authenticate($id)
    {
        Session::put('reservation_id', $id);
    }

    /**
     * Finds reservation_id based on parameters, sets session, and then redirects to show details.
     * GET parameters should contain 'confirmation_number', 'first_name', 'last_name'.
     */
    public function lookup()
    {
        if (Input::has('confirmation_number')) {
            $validator = Validator::make(Input::all(), array(
                'confirmation_number' => $this->validatorRules['confirmation_number'],
                'first_name' => $this->validatorRules['first_name'],
                'last_name' => $this->validatorRules['last_name'],
            ));

            if ($validator->passes()) {
                $reservation = Reservation::where('confirmation_number', '=', Input::get('confirmation_number'))->first();

                if ((!is_null($reservation)) && ($reservation->first_name === Input::get('first_name')) && ($reservation->last_name === Input::get('last_name'))) {
                    self::authenticate($reservation->id);

                    return Redirect::action('ReservationController@showEditForm', array('id' => $reservation->id));
                } else {
                    $this->setAlertDanger(self::ALERT_DANGER_LOOKUP);
                    return $this->showLookupForm();
                }
            } else {
                $messageHtml = self::renderMessages($validator->messages());
                $this->setAlertDanger($messageHtml);

                return $this->showLookupForm();
            }
        } else {
            return $this->showLookupForm();
        }
    }

    public function showCreateForm()
    {
        $timezones = Timezone::all();

        return $this->makeView('reservation.create', array( 'timezones' => $timezones, ));
    }

    public function create()
    {
        $validator = Validator::make(Input::all(), $this->validatorRules);

        if ($validator->passes()) {
            $utcDate = DateUtil::getUtcDateByTimezoneId(Input::get('timezone_id'), Input::get('date'));

            // disallow past dates.
            if (DateUtil::hasPassed($utcDate)) {
                $this->setAlertDanger(self::ALERT_DANGER_PAST);
                return $this->showCreateForm();
            }

            $flight = Flight::create(array(
                'date' => $utcDate,
                'timezone_id' => Input::get('timezone_id'),
            ));

            $reservation = Reservation::create(array(
                'flight_id' => $flight->id,
                'confirmation_number' => Input::get('confirmation_number'),
                'first_name' => Input::get('first_name'),
                'last_name' => Input::get('last_name'),
            ));

            Checkin::create(array(
                'reservation_id' => $reservation->id,
            ));

            CheckinNotice::create(array(
                'reservation_id' => $reservation->id,
                'email' => Input::get('email'),
            ));

            $this->setAlertSuccess(self::ALERT_SUCCESS_CREATE);
        } else {
            $messageHtml = self::renderMessages($validator->messages());
            $this->setAlertDanger($messageHtml);
        }

        return $this->showCreateForm();
    }

    public function showEditForm($id)
    {
        echo $id;
        exit;

        $reservation = Reservation::find($id)->with('checkinNotice', 'flight.timezone')->first();
        $timezones = Timezone::all();

        // convert stored UTC date to local date in timezone.
        $localDate = DateUtil::getLocalDate($reservation->flight->timezone->name, $reservation->flight->date);

        return $this->makeView('reservation.edit', array(
            'timezones' => $timezones,
            'reservation' => $reservation,
            'local_date' => $localDate,
        ));
    }

    public function edit($id)
    {
        $validator = Validator::make(Input::all(), $this->validatorRules);

        if ($validator->passes()) {
            $utcDate = DateUtil::getUtcDateByTimezoneId(Input::get('timezone_id'), Input::get('date'));

            $reservation = Reservation::find($id)->with('checkinNotice', 'flight.timezone')->first();

            $reservation->flight->date = $utcDate;
            $reservation->confirmation_number = Input::get('confirmation_number');
            $reservation->first_name = Input::get('first_name');
            $reservation->last_name = Input::get('last_name');
            $reservation->checkinNotice->email = Input::get('email');
            $reservation->flight->timezone_id = Input::get('timezone_id');

            $reservation->save();

            $this->setAlertSuccess(self::ALERT_SUCCESS_EDIT);
        } else {
            $messageHtml = self::renderMessages($validator->messages());
            $this->setAlertDanger($messageHtml);
        }

        return $this->showEditForm($id);
    }

    public function delete($id)
    {

    }
} 