@extends('layout.master')

@section('content')
    <form method="post" action="{{ action('ReservationController@create') }}">
        <div class="form-group">
            <label for="date">Date</label>
            <input type="datetime" name="date" id="date" />
        </div>
        <div class="form-group">
            <label for="confirmation-number">Confirmation Number</label>
            <input type="text" name="confirmation_number" id="confirmation-number" />
        </div>
        <div class="form-group">
            <label for="first-name">First Name</label>
            <input type="text" name="first_name" id="first-name" />
        </div>
        <div class="form-group">
            <label for="last-name">Last Name</label>
            <input type="text" name="last_name" id="last-name" />
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" />
        </div>
        <div class="form-group">
            <label for="timezone">Departure Airport Timezone</label>
            <select name="timezone_id" id="timezone">
                <option value="">Select...</option>
                @foreach ($timezones as $timezone)
                <option value="{{ $timezone['attributes']['id'] }}">{{{ $timezone['attributes']['name'] }}}</option>
                @endforeach
            </select>
        </div>
        <input type="submit" name="submit" />
    </form>
    <p><small><a href="{{ action('ReservationController@showLookupForm') }}">Trying to change an existing reservation?</a></small></p>
@stop