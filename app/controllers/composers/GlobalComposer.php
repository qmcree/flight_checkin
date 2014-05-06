<?php

class GlobalComposer 
{
    public function compose($view)
    {
        $view->with(array(
            'reservation_count' => Reservation::all()->count(),
            '_warning' => BaseController::instance()->getAlertWarning(),
        ));
    }
} 