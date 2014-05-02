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
        //return 'Attempting to checkin flight ID ' . $flight['attributes']['id'] . ' with reservation ID ' . $flight['relations']['reservation']['attributes']['id'];

        /*$airline = $flight['relations']['airline']['attributes']['name'];

        switch ($airline) {
            case (self::AIRLINE_SOUTHWEST):
                $url =
                break;
        }*/

        $reservation = $flight['relations']['reservation']['attributes'];

        $request = curl_init('https://www.southwest.com/flight/retrieveCheckinDoc.html');
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
            CURLOPT_POSTFIELDS => array(
                'confirmationNumber' => $reservation['confirmation_number'],
                'firstName' => $reservation['first_name'],
                'lastName' => $reservation['last_name'],
                'submitButton' => 'Check+In',
            ),
        ));
        //return curl_exec($request);
        return $reservation;

        // CURLOPT_COOKIE	 The contents of the "Cookie: " header to be used in the HTTP request. Note that multiple cookies are separated with a semicolon followed by a space (e.g., "fruit=apple; colour=red")
    }
} 