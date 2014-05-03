<?php

class Checkin extends Eloquent
{
    public $timestamps = false;
    protected $primaryKey = 'reservation_id', $guarded = array('checked_in', 'attempts');

    const AIRLINE_SOUTHWEST = 'Southwest Airlines';
    const AIRLINE_SOUTHWEST_SESSION_COOKIE = 'JSESSIONID';
    const AIRLINE_SOUTHWEST_ERROR_NEEDLE = 'id="errors"';

    /**
     * Defines inverse reservation relation.
     * @return mixed
     */
    public function reservation()
    {
        return $this->belongsTo('Reservation');
    }

    /**
     * Attempt to checkin.
     * @param Flight
     * @return bool true if checkin was successful, false if not.
     */
    public static function attempt($flight)
    {
        $reservation = $flight['relations']['reservation']['attributes'];

        $checkin = self::find($reservation['id']);
        var_dump($checkin['attempts']);
        $checkin['attempts'] = $checkin['attempts'] + 1;
        $checkin->save();
        var_dump($checkin['attempts']);
        exit;

        $request = curl_init('http://www.southwest.com/flight/retrieveCheckinDoc.html');
        curl_setopt_array($request, array(
            CURLOPT_COOKIESESSION => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_POST => true,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_REFERER => 'https://www.southwest.com/flight/', // spoof
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36', // spoof
            CURLOPT_POSTFIELDS => sprintf('confirmationNumber=%s&firstName=%s&lastName=%s&submitButton=Check+In', $reservation['confirmation_number'],
                $reservation['first_name'], $reservation['last_name']),
        ));
        $response1 = curl_exec($request);

        // check if error occurred.
        if (strpos($response1, self::AIRLINE_SOUTHWEST_ERROR_NEEDLE) !== false) {
            curl_close($request);
            return false;
        } else {
            $sessionId = self::getSessionId($response1);
            curl_close($request);
            return true;
        }

        // CURLOPT_COOKIE	 The contents of the "Cookie: " header to be used in the HTTP request. Note that multiple cookies are separated with a semicolon followed by a space (e.g., "fruit=apple; colour=red")
    }

    /**
     * Parses headers to find session cookie ID.
     * @param string $response
     * @return string|bool false if session cookie not found.
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
} 