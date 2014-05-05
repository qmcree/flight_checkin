@extends('layout.master')

@section('content')
    <h2>Lookup Your Reservation</h2>

    <form method="get" action="{{ action('ReservationController@lookup') }}">
        <div class="form-group">
            <label for="confirmation-number">Confirmation Number</label>
            <input type="text" class="form-control" name="confirmation_number" id="confirmation-number" />
        </div>
        <div class="form-group">
            <label for="first-name">First Name</label>
            <input type="text" class="form-control" name="first_name" id="first-name" />
        </div>
        <div class="form-group">
            <label for="last-name">Last Name</label>
            <input type="text" class="form-control" name="last_name" id="last-name" />
        </div>
        <button type="submit" class="btn btn-default">Lookup</button>
    </form>
@stop