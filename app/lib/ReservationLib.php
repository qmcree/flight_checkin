<?php

namespace FlightCheckin;

class ReservationLib
{
    /**
     * Determines if reservation with confirmation number already exists.
     *
     * @param string $confirmationNumber
     * @return boolean
     */
    public static function exists($confirmationNumber)
    {
        return (\Reservation::where('confirmation_number', '=', $confirmationNumber)->count() > 0);
    }
} 