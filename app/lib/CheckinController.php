<?php

namespace FlightCheckin;

class CheckinLib
{
    const ATTEMPT_MAX = 10;
    const AIRLINE_NAME = 'Southwest Airlines';
    const AIRLINE_SESSION_COOKIE = 'JSESSIONID';
    const AIRLINE_ERROR_NEEDLE = 'id="errors"';
    const REQUEST_URL_1 = 'http://www.southwest.com/flight/retrieveCheckinDoc.html';
    const REQUEST_URL_2 = 'https://www.southwest.com/flight/selectPrintDocument.html';
    const USER_AGENT = 'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36';

    protected $flight, $curlOptions, $sessionId;

    /**
     * @param \Flight $flight
     */
    public function __construct($flight)
    {
        $this->flight = $flight;
        $this->curlOptions = array(
            CURLOPT_COOKIESESSION => true,
            CURLOPT_POST => true,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_USERAGENT => self::USER_AGENT,
        );
    }

    /**
     * Attempt to checkin.
     *
     * @throws \Exception
     */
    public function attempt()
    {
        if ($this->beforeMax()) {
            $this->addAttempt();

            $this->execRequest1();
            $this->execRequest2();
        } else {
            // @todo check if user notified and if not, call self::notifyFail.
            throw new \Exception();
        }
    }

    /**
     * Makes first request.
     *
     * @throws \Exception
     */
    private function execRequest1()
    {
        $request = curl_init(self::REQUEST_URL_1);
        curl_setopt_array($request, array_merge($this->curlOptions, array(
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_REFERER => self::REQUEST_URL_1,
            CURLOPT_POSTFIELDS => sprintf('confirmationNumber=%s&firstName=%s&lastName=%s&submitButton=Check+In',
                $this->flight->reservation->confirmation_number, $this->flight->reservation->first_name, $this->flight->reservation->last_name),
        )));
        $response = curl_exec($request);
        curl_close($request);

        // check for error in Southwest's response.
        if (strpos($response, self::AIRLINE_ERROR_NEEDLE) === false) {
            $this->sessionId = self::getSessionId($response);
        } else {
            throw new \Exception();
        }
    }

    /**
     * Makes second request.
     *
     * @throws \Exception
     */
    private function execRequest2()
    {
        $request = curl_init(self::REQUEST_URL_2);
        curl_setopt_array($request, array_merge($this->curlOptions, array(
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_REFERER => self::REQUEST_URL_1,
            CURLOPT_COOKIE => self::AIRLINE_SESSION_COOKIE . '=' . $this->sessionId,
            CURLOPT_POSTFIELDS => 'checkinPassengers[0].selected=true&printDocuments=Check+In',
        )));
        $response = curl_exec($request);

        curl_close($request);

        // error occurred if Southwest tries to redirect.
        if (self::triesRedirect($response))
            throw new \Exception();
    }

    /**
     * Determines if number of attempts are before max.
     *
     * @return boolean
     */
    protected function beforeMax()
    {
        return ($this->flight->reservation->checkin->attempts < self::ATTEMPT_MAX);
    }

    /**
     * Increases attempt count by 1.
     */
    protected function addAttempt()
    {
        $this->flight->reservation->checkin->attempts++;
        $this->flight->reservation->checkin->save();
    }

    /**
     * Notifies passenger by email that max no. of attempts reached.
     */
    protected static function notifyFail()
    {

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