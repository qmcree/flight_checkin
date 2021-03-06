<?php

use FlightCheckin\ReservationLib;
use FlightCheckin\util\DateUtil;

class ReservationController extends BaseController
{
    const ALERT_DANGER_LOOKUP = "I can't find a reservation matching those details.";
    const ALERT_SUCCESS_CREATE = "We will automatically check you in at the earliest possible time so you can board early!";
    const ALERT_DANGER_PAST = "Reservations cannot be in the past.";
    const ALERT_DANGER_DUPLICATE = "Looks like there's already a reservation with that confirmation number.";
    const ALERT_DANGER_CLOSED = "This reservation cannot be looked up because either we've already checked it in or we've been unsuccessful in doing so.";
    const ALERT_SUCCESS_EDIT = "Your reservation has been updated.";

    protected $validatorRules = array(
        'date' => array('required', 'date_format:Y-m-d H:i:s'),
        'confirmation_number' => array('required', 'alpha_num', 'min:5', 'max:12'),
        'first_name' => array('required', 'alpha', 'min:2', 'max:20'),
        'last_name' => array('required', 'alpha', 'min:2', 'max:20'),
        'email' => array('required', 'email', 'max:30'),
        'timezone_id' => array('required', 'numeric', 'max:50'),
    );

    /**
     * Renders lookup form.
     *
     * @return View
     */
    protected function showLookupForm()
    {
        return $this->makeView('reservation.lookup');
    }

    /**
     * Set session for reservation ID.
     *
     * @param integer $id Reservation ID
     */
    protected static function authenticate($id)
    {
        Session::put('reservation_id', $id);
    }

    /**
     * Finds reservation_id based on parameters, sets session, and then redirects to show details.
     * GET parameters should contain 'confirmation_number', 'first_name', 'last_name'.
     *
     * @return \Illuminate\Http\RedirectResponse|View
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
                $reservation = Reservation::with('checkinNotice')->where('confirmation_number', '=', Input::get('confirmation_number'))->first();

                if ((!is_null($reservation)) && ($reservation->first_name === Input::get('first_name')) && ($reservation->last_name === Input::get('last_name'))) {

                    if (!is_null($reservation->checkinNotice->notified_at)) {
                        $this->setAlertDanger(self::ALERT_DANGER_CLOSED);
                        return $this->showLookupForm();
                    }

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

    /**
     * Renders create form.
     *
     * @return View
     */
    public function showCreateForm()
    {
        $timezones = Timezone::all();

        return $this->makeView('reservation.create', array( 'timezones' => $timezones, ));
    }

    /**
     * Creates new reservation.
     *
     * @return View
     */
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

            // disallow duplicates.
            if (ReservationLib::exists(Input::get('confirmation_number'))) {
                $this->setAlertDanger(self::ALERT_DANGER_DUPLICATE);
                return $this->showCreateForm();
            }

            $reservation = Reservation::create(array(
                'confirmation_number' => Input::get('confirmation_number'),
                'first_name' => Input::get('first_name'),
                'last_name' => Input::get('last_name'),
            ));

            $flight = Flight::create(array(
                'reservation_id' => $reservation->id,
                'date' => $utcDate,
                'timezone_id' => Input::get('timezone_id'),
            ));

            Checkin::create(array(
                'reservation_id' => $reservation->id,
            ));

            CheckinNotice::create(array(
                'reservation_id' => $reservation->id,
                'email' => Input::get('email'),
            ));

            Mail::send('email.create_success', array(
                'reservation' => $reservation,
                'flight' => $flight,
                'local_date' => Input::get('date'),
            ), function($email) {
                $name = Input::get('first_name') . ' ' . Input::get('last_name');
                $email->to(Input::get('email'), $name)->subject("You're all set.");
            });

            self::authenticate($reservation->id);
            $this->setAlertSuccess(self::ALERT_SUCCESS_CREATE);
        } else {
            $messageHtml = self::renderMessages($validator->messages());
            $this->setAlertDanger($messageHtml);
        }

        return $this->showCreateForm();
    }

    /**
     * Renders edit form.
     *
     * @param integer $id
     * @return View
     */
    public function showEditForm($id)
    {
        $reservation = Reservation::with('checkinNotice', 'flight.timezone')->find($id);
        $timezones = Timezone::all();

        // convert stored UTC date to local date in timezone.
        $localDate = DateUtil::getLocalDate($reservation->flight->timezone->name, $reservation->flight->date);

        return $this->makeView('reservation.edit', array(
            'timezones' => $timezones,
            'reservation' => $reservation,
            'local_date' => $localDate,
        ));
    }

    /**
     * Edits reservation.
     *
     * @param integer $id
     * @return View
     */
    public function edit($id)
    {
        $validator = Validator::make(Input::all(), $this->validatorRules);

        if ($validator->passes()) {
            $utcDate = DateUtil::getUtcDateByTimezoneId(Input::get('timezone_id'), Input::get('date'));

            // disallow past dates.
            if (DateUtil::hasPassed($utcDate)) {
                $this->setAlertDanger(self::ALERT_DANGER_PAST);
                return $this->showEditForm($id);
            }

            // disallow duplicates.
            if (ReservationLib::exists(Input::get('confirmation_number'))) {
                $this->setAlertDanger(self::ALERT_DANGER_DUPLICATE);
                return $this->showEditForm($id);
            }

            $reservation = Reservation::with('checkinNotice', 'flight.timezone')->find($id);

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

    /**
     * Deletes reservation.
     *
     * @param integer $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        Checkin::where('reservation_id', '=', $id)->delete();
        CheckinNotice::where('reservation_id', '=', $id)->delete();
        Flight::where('reservation_id', '=', $id)->delete();
        Reservation::where('id', '=', $id)->delete();

        Session::forget('reservation_id');

        return Redirect::action('ReservationController@showCreateForm');
    }
} 