<?php

class Checkin extends Eloquent
{
    public $timestamps = false;
    protected $primaryKey = 'reservation_id', $fillable = array('reservation_id', 'checked_in', 'attempts');

    /**
     * Defines inverse reservation relation.
     * @return mixed
     */
    public function reservation()
    {
        return $this->belongsTo('Reservation');
    }
} 