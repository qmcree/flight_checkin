<?php

class Flight extends Eloquent
{
    public $timestamps = false;
    protected $guarded = array('id');

    public function scopeUpcoming()
    {
        $utc = new DateTimeZone('UTC');
        $format = 'Y-m-d H:i:s';
        $now = new DateTime('now', $utc);
        $tomorrow = $now->modify('+1 day');

        return Flight::where('date', '>', $now->format($format))
            ->where('date', '<', $tomorrow->format($format));
    }
} 