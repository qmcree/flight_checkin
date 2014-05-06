<?php

class ReservationController extends BaseController
{
    const ALERT_DANGER_LOOKUP = "I can't find a reservation matching those details.";
    const ALERT_SUCCESS_CREATE = "We will automatically check you in at the earliest possible time so you can board early!";
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
            'timezone_id' => array('required', 'numeric', 'max:5'),
        );
    }

    protected function showLookupForm()
    {
        return View::make('reservation.lookup')->with(array(
            '_success' => $this->getAlertSuccess(),
            '_danger' => $this->getAlertDanger(),
        ));
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

        return View::make('reservation.create')->with(array(
            '_success' => $this->getAlertSuccess(),
            '_danger' => $this->getAlertDanger(),
            'timezones' => $timezones,
        ));
    }

    public function create()
    {
        $validator = Validator::make(Input::all(), $this->validatorRules);

        if ($validator->passes()) {
            $utcDate = self::getUtcDate(Input::get('timezone_id'), Input::get('date'));

            $flight = Flight::create(array(
                'date' => $utcDate,
                'timezone_id' => Input::get('timezone_id'),
            ));

            Reservation::create(array(
                'flight_id' => $flight->id,
                'confirmation_number' => Input::get('confirmation_number'),
                'first_name' => Input::get('first_name'),
                'last_name' => Input::get('last_name'),
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
        $reservation = Reservation::find($id)->with('checkin', 'flight.timezone')->first();
        $timezones = Timezone::all();

        return View::make('reservation.edit')->with(array(
            '_success' => $this->getAlertSuccess(),
            '_danger' => $this->getAlertDanger(),
            'timezones' => $timezones,
            'reservation' => $reservation,
        ));
    }

    public function edit($id)
    {
        $validator = Validator::make(Input::all(), $this->validatorRules);

        if ($validator->passes()) {
            $reservation = Reservation::find($id)->with('checkin', 'flight.timezone')->first();

            $reservation['relations']['flight']['attributes']['date'] = Input::get('date');
            $reservation['attributes']['confirmation_number'] = Input::get('confirmation_number');
            $reservation['attributes']['first_name'] = Input::get('first_name');
            $reservation['attributes']['last_name'] = Input::get('last_name');
            $reservation['relations']['checkin']['attributes']['passenger_email'] = Input::get('email');
            $reservation['relations']['flight']['attributes']['timezone_id'] = Input::get('timezone_id');

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