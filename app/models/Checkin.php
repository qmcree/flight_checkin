<?php

class Checkin extends Eloquent
{
    public $timestamps = false;
    protected $guarded = array('checked_in', 'attempts');
} 