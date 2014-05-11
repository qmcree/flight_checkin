<?php

$request = curl_init('http://www.southwest.com/flight/retrieveCheckinDoc.html');
curl_setopt_array($request, array(
    CURLOPT_COOKIESESSION => true,
    CURLOPT_POST => true,
    CURLOPT_HEADER => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 20,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36',
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 3,
    //CURLOPT_REFERER => self::REQUEST_URL_1,
    CURLOPT_POSTFIELDS => sprintf('confirmationNumber=%s&firstName=%s&lastName=%s&submitButton=Check+In',
        'TUA90Q', 'Joseph', 'Jones'),
));
$response = curl_exec($request);
curl_close($request);

var_dump($response);