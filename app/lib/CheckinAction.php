<?php

namespace FlightCheckin;

use FlightCheckin\util\DateUtil;

class CheckinAction
{
    const ATTEMPT_MAX = 10;
    const AIRLINE_NAME = 'Southwest Airlines';
    const AIRLINE_SESSION_COOKIE = 'JSESSIONID';
    const AIRLINE_ERROR_NEEDLE = 'id="errors"';
    const REQUEST_URL_1 = 'http://www.southwest.com/flight/retrieveCheckinDoc.html';
    const REQUEST_URL_2 = 'https://www.southwest.com/flight/selectPrintDocument.html';
    const USER_AGENT = 'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36';
    const NOTIFY_SUCCESS = 8;
    const NOTIFY_MAX = 16;
    const NOTIFY_SUCCESS_SUBJECT = "You're checked in!";
    const NOTIFY_MAX_SUBJECT = "Unable to automate your checkin";

    protected $flight;
    protected $curlOptions = array(
        CURLOPT_COOKIESESSION => true,
        CURLOPT_POST => true,
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_USERAGENT => self::USER_AGENT,
    );
    protected $sessionId;

    /**
     * @param \Flight $flight
     */
    public function __construct($flight)
    {
        $this->flight = $flight;
    }

    /**
     * Attempt to checkin.
     *
     * @throws CheckinActionException
     */
    public function attempt()
    {
        if ($this->alreadyCheckedIn())
            throw new CheckinActionException('Already checked in.');

        if (!$this->maxReached()) {
            $this->increaseCount();

            $this->execRequest1();
            $this->execRequest2();

            $this->setCheckedIn();

            $this->notify(self::NOTIFY_SUCCESS);
        } else {
            if (!$this->alreadyNotified()) {
                $this->notify(self::NOTIFY_MAX);
            }

            throw new CheckinActionException('Already tried the max number of attempts.');
        }
    }

    /**
     * Notifies passenger by email and updates notified_at timestamp.
     *
     * @param integer $type Constant
     * @throws CheckinActionException
     */
    protected function notify($type)
    {
        $data = array( 'flight' => $this->flight, );
        $passengerEmail = $this->flight->reservation->checkinNotice->email;
        $passengerName = $this->flight->reservation->first_name . ' ' . $this->flight->reservation->last_name;

        switch ($this) {
            case ($type == self::NOTIFY_SUCCESS):
                $subject = self::NOTIFY_SUCCESS_SUBJECT;

                \Mail::send('email.attempt_success', $data, function($email) use ($passengerEmail, $passengerName, $subject) {
                    $email->to($passengerEmail, $passengerName)->subject($subject);
                });

                $this->setNotifiedTimestamp();
                break;
            case ($type == self::NOTIFY_MAX):
                $subject = self::NOTIFY_MAX_SUBJECT;

                \Mail::send('email.attempt_max', $data, function($email) use ($passengerEmail, $passengerName, $subject) {
                    $email->to($passengerEmail, $passengerName)->subject($subject);
                });

                $this->setNotifiedTimestamp();
                break;
            default:
                throw new CheckinActionException('Undefined notify type provided.');
        }
    }

    /**
     * Makes first request.
     *
     * @throws CheckinActionException
     */
    private function execRequest1()
    {
        $request = curl_init(self::REQUEST_URL_1);
        curl_setopt_array($request, $this->curlOptions);
        curl_setopt_array($request, array(
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_REFERER => self::REQUEST_URL_1,
            CURLOPT_POSTFIELDS => sprintf('confirmationNumber=%s&firstName=%s&lastName=%s&submitButton=Check+In',
                $this->flight->reservation->confirmation_number, $this->flight->reservation->first_name, $this->flight->reservation->last_name),
        ));
        $response = curl_exec($request);

        if ($response === false)
            throw new CheckinActionException(sprintf('First request failed. ([%d] %s)', curl_errno($request), curl_error($request)));

        curl_close($request);

        // check for error in Southwest's response.
        if (strpos($response, self::AIRLINE_ERROR_NEEDLE) === false) {
            $this->sessionId = self::getSessionId($response);
        } else {
            throw new CheckinActionException('Error detected in first request response.');
        }
    }

    /**
     * Makes second request.
     *
     * @throws CheckinActionException
     */
    private function execRequest2()
    {
        $request = curl_init(self::REQUEST_URL_2);
        curl_setopt_array($request, $this->curlOptions);
        curl_setopt_array($request, array(
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_REFERER => self::REQUEST_URL_1,
            CURLOPT_COOKIE => self::AIRLINE_SESSION_COOKIE . '=' . $this->sessionId,
            CURLOPT_POSTFIELDS => 'checkinPassengers[0].selected=true&printDocuments=Check+In',
        ));
        $response = curl_exec($request);

        if ($response === false)
            throw new CheckinActionException(sprintf('Second request failed. ([%d] %s)', curl_errno($request), curl_error($request)));

        curl_close($request);

        // error occurred if Southwest tries to redirect.
        if (self::triesRedirect($response))
            throw new CheckinActionException('Error detected in second request response.');
    }

    /**
     * Determines if reached max number of attempts.
     *
     * @return boolean
     */
    protected function maxReached()
    {
        return ($this->flight->reservation->checkin->attempts >= self::ATTEMPT_MAX);
    }

    /**
     * Determines if passenger has already been notified.
     *
     * @return boolean
     */
    protected function alreadyNotified()
    {
        return (!is_null($this->flight->reservation->checkinNotice->notified_at));
    }

    /**
     * Updates notified_at to now.
     */
    protected function setNotifiedTimestamp()
    {
        $this->flight->reservation->checkinNotice->notified_at = date(DateUtil::DATE_FORMAT_MYSQL);
        $this->flight->reservation->checkinNotice->save();
    }

    /**
     * Determines if reservation has already been checked in.
     *
     * @return boolean
     */
    protected function alreadyCheckedIn()
    {
        return ($this->flight->reservation->checkin->checked_in > 0);
    }

    /**
     * Sets reservation as checked in.
     */
    protected function setCheckedIn()
    {
        $this->flight->reservation->checkin->checked_in = 1;
        $this->flight->reservation->checkin->save();
    }

    /**
     * Increases attempt count by 1.
     */
    protected function increaseCount()
    {
        $this->flight->reservation->checkin->attempts++;
        $this->flight->reservation->checkin->save();
    }

    /**
     * Parses headers to find session cookie ID.
     * @param string $response
     * @return string|boolean false if session cookie not found.
     */
    protected static function getSessionId($response)
    {
        $headers = http_parse_headers($response);
        $cookies = array();

        foreach ($headers as $headerName => $headerValue) {
            if (strtolower($headerName) === 'set-cookie') {
                foreach ($headerValue as $cookieValue) {
                    array_push($cookies, http_parse_cookie($cookieValue));
                }
            }
        }

        foreach ($cookies as $cookie) {
            if (isset($cookie->cookies[self::AIRLINE_SESSION_COOKIE]))
                return $cookie->cookies[self::AIRLINE_SESSION_COOKIE];
        }

        return false;
    }

    /**
     * Determines if response headers denote redirect.
     * @param string $response
     * @return boolean
     */
    protected static function triesRedirect($response)
    {
        $headers = http_parse_headers($response);

        foreach ($headers as $k => $v) {
            if (strtolower($k) === 'location') {
                return true;
            }
        }

        return false;
    }
} 