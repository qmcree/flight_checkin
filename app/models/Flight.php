<?php

class Flight extends Eloquent
{
    public $timestamps = false;
    protected $guarded = array('id');

    /**
     * Defines query scope that finds flights within the next 24 hours.
     * @return mixed
     */
    public function scopeUpcoming()
    {
        $utc = new DateTimeZone('UTC');
        $format = 'Y-m-d H:i:s';
        $now = new DateTime('now', $utc);
        $tomorrow = new DateTime('now + 1 day', $utc);

        return Flight::where('date', '>', $now->format($format))
            ->where('date', '<', $tomorrow->format($format));
    }

    /**
     * Defines airline relation.
     * @return mixed
     */
    public function airline()
    {
        return $this->hasOne('Airline');
    }

    /**
     * Defines airport relation.
     * @return mixed
     */
    public function airport()
    {
        return $this->hasOne('Airport');
    }

    /**
     * Defines inverse reservation relation.
     * @return mixed
     */
    public function reservation()
    {
        return $this->belongsTo('Reservation', 'id', 'flight_id');
    }
} 