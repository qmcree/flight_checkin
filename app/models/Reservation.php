<?php

class Reservation extends Eloquent
{
    protected $guarded = array('id', 'created_at', 'updated_at');

    /**
     * Defines inverse checkin relation.
     * @return mixed
     */
    public function checkin()
    {
        return $this->belongsTo('Checkin');
    }

    /**
     * Defines flight relation.
     * @return mixed
     */
    public function flight()
    {
        return $this->hasOne('Flight');
    }
} 