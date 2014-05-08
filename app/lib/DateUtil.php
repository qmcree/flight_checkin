<?php

namespace FlightCheckin\util;

class DateUtil 
{
    const DATE_FORMAT_MYSQL = 'Y-m-d H:i:s';

    /**
     * Converts date, timezone pair to UTC date.
     * @param string $timezoneName
     * @param string $localDate
     * @return string UTC date.
     */
    public static function getUtcDate($timezoneName, $localDate)
    {
        $timezone = new \DateTimeZone($timezoneName);
        $dateTime = new \DateTime($localDate, $timezone);

        return gmdate(self::DATE_FORMAT_MYSQL, $dateTime->getTimestamp());
    }

    /**
     * Converts date to UTC based on timezone_id.
     * @param integer $timezoneId
     * @param string $localDate
     * @return string UTC date.
     */
    public static function getUtcDateByTimezoneId($timezoneId, $localDate)
    {
        $timezone = \Timezone::find($timezoneId);
        return self::getUtcDate($timezone->name, $localDate);
    }

    /**
     * Convert UTC date to local date in timezone.
     * @param string $timezoneName
     * @param string $utcDate
     * @return string local date.
     */
    public static function getLocalDate($timezoneName, $utcDate)
    {
        $dateTime = new \DateTime($utcDate, new \DateTimeZone('UTC'));
        $dateTime->setTimezone(new \DateTimeZone($timezoneName));

        return '0000-00-00 00:00:00';
        //return $dateTime->format(self::DATE_FORMAT_MYSQL);
    }

    /**
     * Converts date, timezone pair to time.
     * @param string $timezoneName
     * @param string $localDate
     * @return integer UNIX timestamp.
     */
    public static function getTime($timezoneName, $localDate)
    {
        $dateTime = new \DateTime($localDate, new \DateTimeZone($timezoneName));

        return $dateTime->getTimestamp();
    }

    /**
     * Determines if UTC date is in past.
     * @param string $date in UTC
     * @return boolean
     */
    public static function hasPassed($date)
    {
        return (self::getTime('UTC', $date) <= time());
    }
} 