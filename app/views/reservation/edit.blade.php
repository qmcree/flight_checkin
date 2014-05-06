@extends('layout.master')

@section('content')
    <?php
    //$flight_timezone_id = $reservation['relations']['flight']['relations']['timezone']['attributes']['id'];
    ?>
    <form method="post" action="{{ action('ReservationController@edit') }}">
        <div class="form-group">
            <label for="date">Date</label>

            <div class="input-group">
                <input type="text" class="form-control datetime" name="date" id="date" />
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
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
            <label for="timezone">Airport Timezone</label>
            <select class="form-control" name="timezone_id" id="timezone">
                <option value="">Select...</option>
                @foreach ($timezones as $timezone)
                <option value="{{ $timezone['attributes']['id'] }}"
                    {{ ($timezone['attributes']['id'] === $reservation['attributes']['timezone_id']) ? 'selected' : '' }}>
                {{{ $timezone['attributes']['name'] }}}
                </option>
                @endforeach
            </select>
            <p class="help-block">What's the timezone of the airport you'll be departing from?
                <a href="/packages/flight_checkin/images/timezone_map.gif" target="_blank">I dunno.</a></p>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="reset" class="btn btn-default">Clear</button>
    </form>
@stop