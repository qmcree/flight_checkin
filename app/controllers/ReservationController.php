<?php

class ReservationController extends BaseController
{
    public function showDetail($id)
    {

    }

    public function showCreateForm()
    {
        $airports = Airport::all();

        return View::make('reservation.create')->with('airports', $airports);
    }

    public function create()
    {

    }

    public function showEditForm($id)
    {

    }

    public function edit($id)
    {

    }

    public function delete($id)
    {

    }
} 