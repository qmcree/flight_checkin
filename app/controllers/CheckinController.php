<?php

class CheckinController extends BaseController
{
    const AIRLINE_SOUTHWEST = 'Southwest Airlines';
    const AIRLINE_SOUTHWEST_SESSION_COOKIE = 'JSESSIONID';
    const AIRLINE_SOUTHWEST_ERROR_NEEDLE = 'id="errors"';
    const USER_AGENT = 'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36';

    /**
     * Attempt to checkin.
     * @param Flight
     * @return bool true if checkin was successful, false if not.
     */
    public static function attempt($flight)
    {
        var_dump($flight->reservation->checkin->attempts);
        exit;
        $flight->save();

        // make first request.
        $request1 = curl_init('http://www.southwest.com/flight/retrieveCheckinDoc.html');
        curl_setopt_array($request1, array(
            CURLOPT_COOKIESESSION => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_POST => true,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_REFERER => 'https://www.southwest.com/flight/',
            CURLOPT_USERAGENT => self::USER_AGENT,
            CURLOPT_POSTFIELDS => sprintf('confirmationNumber=%s&firstName=%s&lastName=%s&submitButton=Check+In', $reservation['confirmation_number'],
                $reservation['first_name'], $reservation['last_name']),
        ));
        $response1 = curl_exec($request1);
        curl_close($request1);

        // check if SWA error occurred.
        if (strpos($response1, self::AIRLINE_SOUTHWEST_ERROR_NEEDLE) !== false) {
            return false;
        } else {
            // make second request, persisting session ID cookie.
            $sessionId = self::getSessionId($response1);

            $request2 = curl_init('https://www.southwest.com/flight/selectPrintDocument.html');
            curl_setopt_array($request2, array(
                CURLOPT_COOKIESESSION => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_POST => true,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 20,
                CURLOPT_REFERER => 'http://www.southwest.com/flight/retrieveCheckinDoc.html',
                CURLOPT_USERAGENT => self::USER_AGENT,
                CURLOPT_POSTFIELDS => 'checkinPassengers[0].selected=true&printDocuments=Check+In',
                CURLOPT_COOKIE => self::AIRLINE_SOUTHWEST_SESSION_COOKIE . '=' . $sessionId,
            ));
            $response2 = curl_exec($request2);

            // SWA error occurred if tries to redirect.
            if (self::triesRedirect($response2))
                return false;

            curl_close($request2);

            return true;
        }
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
            if (isset($cookie->cookies[self::AIRLINE_SOUTHWEST_SESSION_COOKIE]))
                return $cookie->cookies[self::AIRLINE_SOUTHWEST_SESSION_COOKIE];
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