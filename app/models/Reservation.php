<?php

class Reservation extends Eloquent
{
    protected $guarded = array('id', 'created_at', 'updated_at');

    /**
     * Defines checkin relation.
     * @return mixed
     */
    public function checkin()
    {
        return $this->hasOne('Checkin');
    }

    /**
     * Defines inverse flight relation.
     * @return mixed
     */
    public function flight()
    {
        return $this->belongsTo('Flight');
    }
} 