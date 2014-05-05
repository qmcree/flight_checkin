@extends('layout.master')

@section('content')
    <h2>New Reservation</h2>

    <form method="post" action="{{ action('ReservationController@create') }}">
        <div class="form-group">
            <label for="date">Date</label>
            <input type="datetime" class="form-control" name="date" id="date" />
        </div>
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
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email" />
            <p class="help-block">We'll shoot you an email right after we check you in.</p>
        </div>
        <div class="form-group">
            <label for="timezone">Departure Airport Timezone</label>
            <select class="form-control" name="timezone_id" id="timezone">
                <option value="">Select...</option>
                @foreach ($timezones as $timezone)
                <option value="{{ $timezone['attributes']['id'] }}">{{{ $timezone['attributes']['name'] }}}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
    <p><small><a href="{{ action('ReservationController@lookup') }}">Trying to change an existing reservation?</a></small></p>
@stop