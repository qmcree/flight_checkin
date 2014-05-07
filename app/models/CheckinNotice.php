<?php

class CheckinNotice extends Eloquent
{
    public $timestamps = false;
    protected $primaryKey = 'reservation_id';

    /**
     * Defines inverse reservation relation.
     * @return mixed
     */
    public function reservation()
    {
        return $this->belongsTo('Reservation');
    }
} 