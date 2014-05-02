<?php

class Checkin extends Eloquent
{
    public $timestamps = false;
    protected $guarded = array('checked_in', 'attempts');

    /**
     * Defines reservation relation.
     * @return mixed
     */
    public function reservation()
    {
        return $this->hasOne('Reservation');
    }

    /**
     * Attempt to checkin.
     * @param Flight
     */
    public static function attempt($flight)
    {
        return 'Attempting to checkin flight ID ' . $flight['attributes']['id'] . ' with reservation ID ' . $flight['relations']['reservation']['attributes']['id'];
    }
} 