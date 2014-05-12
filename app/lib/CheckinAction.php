<?php

namespace FlightCheckin;

use FlightCheckin\util\DateUtil;

class CheckinAction
{
    const ATTEMPT_MAX = 10;
    const AIRLINE_NAME = 'Southwest Airlines';
    const AIRLINE_SESSION_COOKIE = 'cacheid';
    const REQUEST_URL = 'https://mobile.southwest.com/middleware/MWServlet';
    const USER_AGENT = 'Mozilla/5.0 (iPad; CPU OS 7_0_2 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A501 Safari/9537.53';
    const NOTIFY_SUCCESS = 8;
    const NOTIFY_MAX = 16;
    const NOTIFY_SUCCESS_SUBJECT = "You're checked in!";
    const NOTIFY_MAX_SUBJECT = "Unable to automate your checkin";

    protected $flight;
    protected $curlOptions;

    /**
     * @param \Flight $flight
     */
    public function __construct($flight)
    {
        $this->flight = $flight;

        $this->curlOptions = array(
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_COOKIESESSION => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => sprintf('recordLocator=%s&firstName=%s&lastName=%s&serviceID=flightcheckin_new&appID=swa&channel=wap&platform=thinclient&cacheid=&rcid=spaiphone',
                $flight->reservation->confirmation_number, $flight->reservation->first_name, $flight->reservation->last_name),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_USERAGENT => self::USER_AGENT,
        );
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

            if ($sessionId = $this->getValidSessionId())
                $this->makeRequest($sessionId);

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
     * Makes first request to obtain session ID from cookie.
     *
     * @throws CheckinActionException
     * @return string Valid session ID.
     */
    private function getValidSessionId()
    {
        $request = curl_init(self::REQUEST_URL);
        curl_setopt_array($request, $this->curlOptions);
        curl_setopt_array($request, array(
            CURLOPT_HEADER => true,
        ));
        $response = curl_exec($request);

        if ($response === false)
            throw new CheckinActionException(sprintf('First request failed. ([%d] %s)', curl_errno($request), curl_error($request)));

        curl_close($request);

        if ($sessionId = self::getSessionId($response)) {
            return $sessionId;
        } else {
            throw new CheckinActionException('No session cookie found in first request.');
        }
    }

    /**
     * Makes second request with valid session.
     *
     * @param string $sessionId
     * @throws CheckinActionException
     */
    private function makeRequest($sessionId)
    {
        $request = curl_init(self::REQUEST_URL);
        curl_setopt_array($request, $this->curlOptions);
        curl_setopt_array($request, array(
            CURLOPT_COOKIE => self::AIRLINE_SESSION_COOKIE . '=' . $sessionId,
        ));
        $response = curl_exec($request);

        if ($response === false)
            throw new CheckinActionException(sprintf('Second request failed. ([%d] %s)', curl_errno($request), curl_error($request)));

        curl_close($request);

        $response = json_decode($response);
        var_dump($response);
        exit;
        /*
        if ($response)
            throw new CheckinActionException('Error detected in second request response.');
        */
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
} 