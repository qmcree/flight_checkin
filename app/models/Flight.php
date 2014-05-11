<?php

use FlightCheckin\util\DateUtil;

class Flight extends Eloquent
{
    public $timestamps = false;
    protected $primaryKey = 'reservation_id';
    protected $guarded = array('id');

    /**
     * Defines query scope that finds flights within the next 24 hours.
     * @param Flight $query
     * @return mixed
     */
    public function scopeUpcoming($query)
    {
        $utc = new DateTimeZone('UTC');

        $now = new DateTime('now', $utc);
        $tomorrow = new DateTime('now + 1 day', $utc);

        return $query->where('date', '>', $now->format(DateUtil::DATE_FORMAT_MYSQL))
            ->where('date', '<', $tomorrow->format(DateUtil::DATE_FORMAT_MYSQL));
    }

    /**
     * Defines inverse timezone relation.
     * @return mixed
     */
    public function timezone()
    {
        return $this->belongsTo('Timezone');
    }

    /**
     * Defines reservation relation.
     * @return mixed
     */
    public function reservation()
    {
        return $this->belongsTo('Reservation');
    }
} 