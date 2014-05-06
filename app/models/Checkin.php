<?php

class Checkin extends Eloquent
{
    public $timestamps = false;
    protected $primaryKey = 'reservation_id', $guarded = array('checked_in', 'attempts');

    /**
     * Defines inverse reservation relation.
     * @return mixed
     */
    public function reservation()
    {
        return $this->belongsTo('Reservation');
    }
} 