<?php

class Airline extends Eloquent
{
    public $timestamps = false;
    protected $guarded = array('id');

    /**
     * Defines flight relation.
     * @return mixed
     */
    public function flights()
    {
        return $this->hasMany('Flight');
    }
} 