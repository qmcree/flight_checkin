<?php

class Checkin extends Eloquent
{
    public $timestamps = false;
    protected $guarded = array('checked_in', 'attempts');

    const AIRLINE_SOUTHWEST = 'Southwest Airlines';

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
     */
    public static function attempt($flight)
    {
        $reservation = $flight['relations']['reservation']['attributes'];

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

        curl_close($request);

        return self::getCookies($response1);

        // @see http://stackoverflow.com/a/7179233
        // CURLOPT_COOKIE	 The contents of the "Cookie: " header to be used in the HTTP request. Note that multiple cookies are separated with a semicolon followed by a space (e.g., "fruit=apple; colour=red")
    }

    protected static function getCookies($response)
    {
        $headers = http_parse_headers($response);
        $cookies = array();

        foreach ($headers as $headerName => $headerValue) {
            if (strtolower($headerName) === 'set-cookie') {
                foreach ($headerValue as $cookieName => $cookieValue) {
                    array_push($cookies, http_parse_cookie($cookieValue));
                }
            }
        }

        return $cookies;
    }
} 