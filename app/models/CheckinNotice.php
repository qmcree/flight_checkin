<?php

class CheckinNotice extends Eloquent
{
    public $timestamps = false;
    protected $primaryKey = 'reservation_id';
    protected $fillable = array('reservation_id', 'email', 'notified_at');

    /**
     * Defines inverse reservation relation.
     * @return mixed
     */
    public function reservation()
    {
        return $this->belongsTo('Reservation');
    }
} 